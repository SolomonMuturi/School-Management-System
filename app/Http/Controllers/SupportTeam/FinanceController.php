<?php

namespace App\Http\Controllers\SupportTeam;

use App\Helpers\Qs;
use App\Http\Controllers\Controller;
use App\Models\Discount;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\FeeStructure;
use App\Models\FeeType;
use App\Models\FinanceAccount;
use App\Models\FinancePayment;
use App\Models\FinanceReceipt;
use App\Models\FinanceTransaction;
use App\Models\Refund;
use App\Models\StudentFee;
use App\Models\StudentRecord;
use App\Models\Supplier;
use App\Repositories\MyClassRepo;
use App\Repositories\StudentRepo;
use App\Services\PaymentService;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PDF;

class FinanceController extends Controller
{
    protected $my_class, $student, $year, $payment;

    public function __construct(MyClassRepo $my_class, StudentRepo $student, PaymentService $payment)
    {
        $this->my_class = $my_class;
        $this->student = $student;
        $this->payment = $payment;
        $this->year = Qs::getCurrentSession();
        $this->middleware('teamAccount');
    }

    /* ---------- Helpers ---------- */
    protected function sessionData()
    {
        return [
            'session' => $this->year,
            'year'    => $this->year,
        ];
    }

    protected function terms()
    {
        return ['First Term', 'Second Term', 'Third Term'];
    }

    protected function paymentMethods()
    {
        return ['cash', 'bank', 'mobile_money', 'mpesa', 'card', 'other'];
    }

    protected function defaultFeeTypes()
    {
        $types = [
            'Tuition', 'Registration', 'Transport', 'Boarding', 'Meals',
            'Exams', 'Activities', 'Library', 'Other'
        ];
        foreach ($types as $i => $name) {
            FeeType::firstOrCreate(['name' => $name]);
        }
    }

    public function recordTransaction($account_id, $type, $amount, $date, $description, $reference = null, $related = null)
    {
        if (!$account_id) return;

        FinanceTransaction::create(array_merge([
            'finance_account_id' => $account_id,
            'type' => $type,
            'amount' => $amount,
            'description' => $description,
            'transaction_date' => $date,
            'reference_no' => $reference,
            'related_to' => $related,
        ], $this->sessionData()));

        $account = FinanceAccount::find($account_id);
        if ($account) {
            $account->current_balance = $type === 'expense' || $type === 'refund'
                ? $account->current_balance - $amount
                : $account->current_balance + $amount;
            $account->save();
        }
    }

    protected function generateReceiptNo()
    {
        return 'RCP-' . strtoupper(substr(str_replace('-', '', $this->year), 0, 8)) . '-' . str_pad(FinanceReceipt::count() + 1, 5, '0', STR_PAD_LEFT);
    }

    /* ---------- Dashboard ---------- */
    public function dashboard()
    {
        $session = $this->year;

        $d['total_fees'] = StudentFee::where('year', $session)->sum('amount_due');
        $d['fees_collected'] = FinancePayment::where('year', $session)->where('status', 'completed')->sum('amount');
        $d['outstanding'] = StudentFee::where('year', $session)->where('status', '!=', 'paid')->sum('balance');
        $d['fees_discount'] = Discount::where('year', $session)->where('status', 'active')->sum('amount');
        $d['expenses'] = Expense::where('year', $session)->where('status', 'completed')->sum('amount');
        $d['refunds'] = Refund::where('year', $session)->where('status', 'completed')->sum('amount');
        $d['net_income'] = $d['fees_collected'] - $d['expenses'] - $d['refunds'];
        $d['overdue'] = StudentFee::where('year', $session)
            ->where('status', 'overdue')
            ->orWhere(function($q) use ($session) {
                $q->where('year', $session)->where('due_date', '<', now())->where('status', '!=', 'paid');
            })
            ->sum('balance');

        $d['recent_payments'] = FinancePayment::with(['student', 'studentFee.feeStructure.feeType'])
            ->where('year', $session)
            ->orderByDesc('id')->limit(10)->get();

        $outstandingFees = StudentFee::with(['student', 'student.admission', 'student.admission.my_class', 'feeStructure.feeType'])
            ->where('year', $session)->where('balance', '>', 0)->where('status', '!=', 'paid')->get();

        $d['outstanding_students'] = $outstandingFees->groupBy('student_id')->map(function ($group) {
            $first = $group->first();
            $sr = $first->student ? $first->student->admission : null;

            return (object)[
                'student_id'   => $first->student_id,
                'name'         => $first->student ? $first->student->name : 'N/A',
                'adm_no'       => $sr ? $sr->adm_no : null,
                'class'        => ($sr && $sr->my_class) ? $sr->my_class->name : null,
                'due'          => $group->sum('amount_due'),
                'paid'         => $group->sum('amount_paid'),
                'discount'     => $group->sum('discount'),
                'balance'      => $group->sum('balance'),
            ];
        })->sortByDesc('balance')->values();

        $d['stat_counts'] = [
            'students' => StudentRecord::where('grad', 0)->count(),
            'payments' => FinancePayment::where('year', $session)->count(),
            'receipts' => FinanceReceipt::where('year', $session)->count(),
            'expenses' => Expense::where('year', $session)->count(),
        ];

        $d['fee_types'] = FeeType::all();
        $d['accounts'] = FinanceAccount::all();
        $d['year'] = $session;

        return view('pages.support_team.finance.dashboard', $d);
    }

    /* ---------- Fee Types ---------- */
    public function feeTypes()
    {
        $this->defaultFeeTypes();
        $d['fee_types'] = FeeType::orderBy('name')->get();
        return view('pages.support_team.finance.fee_types', $d);
    }

    public function feeTypesStore(Request $req)
    {
        $req->validate(['name' => 'required|string|max:191']);
        FeeType::create([
            'name' => $req->name,
            'description' => $req->description,
            'is_active' => $req->boolean('is_active'),
        ]);
        return Qs::jsonStoreOk();
    }

    public function feeTypesUpdate(Request $req, $id)
    {
        $req->validate(['name' => 'required|string|max:191']);
        $type = FeeType::find($id);
        $type->update([
            'name' => $req->name,
            'description' => $req->description,
            'is_active' => $req->boolean('is_active'),
        ]);
        return Qs::jsonUpdateOk();
    }

    public function feeTypesToggle($id)
    {
        $type = FeeType::find($id);
        $type->update(['is_active' => !$type->is_active]);
        return back()->with('flash_success', __('msg.update_ok'));
    }

    /* ---------- Fee Structures ---------- */
    public function feeStructures()
    {
        $session = $this->year;
        $d['fee_structures'] = FeeStructure::with(['feeType', 'myClass'])
            ->where('session', $session)->orderBy('my_class_id')->get();
        $d['fee_types'] = FeeType::where('is_active', true)->get();
        $d['my_classes'] = $this->my_class->all();
        $d['terms'] = $this->terms();
        $d['selected'] = $session;
        return view('pages.support_team.finance.fee_structures', $d);
    }

    public function feeStructuresStore(Request $req)
    {
        $req->validate([
            'fee_type_id' => 'required|exists:fee_types,id',
            'my_class_id' => 'required|exists:my_classes,id',
            'term' => 'required',
            'amount' => 'required|numeric|min:0',
        ]);

        FeeStructure::updateOrCreate(
            ['fee_type_id' => $req->fee_type_id, 'my_class_id' => $req->my_class_id, 'session' => $this->year, 'term' => $req->term],
            ['amount' => $req->amount, 'is_active' => $req->has('is_active'), 'description' => $req->description]
        );
        return Qs::jsonStoreOk();
    }

    public function feeStructuresUpdate(Request $req, $id)
    {
        $req->validate(['amount' => 'required|numeric|min:0', 'term' => 'required']);
        FeeStructure::find($id)->update([
            'amount' => $req->amount,
            'term' => $req->term,
            'is_active' => $req->has('is_active'),
        ]);
        return Qs::jsonUpdateOk();
    }

    public function feeStructuresDestroy($id)
    {
        FeeStructure::find($id)->delete();
        return Qs::deleteOk('finance.fee_structures');
    }

    /* ---------- Billing / Student Fees ---------- */
    public function billing(Request $req)
    {
        $session = $this->year;
        $term = $req->get('term', 'First Term');
        $class_id = $req->get('my_class_id');
        $status = $req->get('status');

        $query = StudentFee::with(['student', 'feeStructure.feeType'])
            ->where('year', $session)->where('term', $term);

        if ($class_id) {
            $classFeeStructureIds = FeeStructure::where('my_class_id', $class_id)->pluck('id');
            $query->whereIn('fee_structure_id', $classFeeStructureIds);
        }
        if ($status) $query->where('status', $status);

        $d['fees'] = $query->orderByDesc('id')->get();
        $d['my_classes'] = $this->my_class->all();
        $d['terms'] = $this->terms();
        $d['selected'] = $term;
        $d['selected_class'] = $class_id;
        $d['selected_status'] = $status;

        return view('pages.support_team.finance.billing', $d);
    }

    public function generateBilling(Request $req)
    {
        $req->validate(['my_class_id' => 'required|exists:my_classes,id', 'term' => 'required']);

        $session = $this->year;
        $term = $req->term;
        $class_id = $req->my_class_id;

        $structures = FeeStructure::where('my_class_id', $class_id)
            ->where('session', $session)->where('term', $term)->where('is_active', true)->get();

        if ($structures->count() < 1) {
            return back()->with('flash_danger', 'No active fee structures found for this class.');
        }

        $students = $this->student->findStudentsByClass($class_id);

        foreach ($students as $st) {
            $total = $structures->sum('amount');
            foreach ($structures as $structure) {
                StudentFee::updateOrCreate(
                    ['student_id' => $st->user_id, 'fee_structure_id' => $structure->id, 'session' => $session, 'term' => $term],
                    [
                        'amount_due' => $structure->amount,
                        'amount_paid' => 0,
                        'balance' => $structure->amount,
                        'due_date' => $req->due_date ?: now()->addDays(30),
                        'status' => 'unpaid',
                        'year' => $session,
                    ]
                );
            }
        }

        return back()->with('flash_success', 'Billing generated for ' . $students->count() . ' students.');
    }

    public function studentBill($student_id, Request $req)
    {
        $student = User::find($student_id);
        if (!$student) return Qs::goWithDanger();

        $term = $req->get('term', 'First Term');
        $session = $this->year;

        $d['student'] = $student;
        $d['sr'] = StudentRecord::where('user_id', $student_id)->first();
        $d['fees'] = StudentFee::with(['feeStructure.feeType', 'discounts'])
            ->where('student_id', $student_id)->where('year', $session)->where('term', $term)->get();
        $d['terms'] = $this->terms();
        $d['selected'] = $term;

        return view('pages.support_team.finance.student_bill', $d);
    }

    public function createInvoice(Request $req)
    {
        $req->validate(['student_id' => 'required|exists:users,id', 'term' => 'required']);
        return Qs::goToRoute(['finance.student_bill', $req->student_id, 'term' => $req->term]);
    }

    /* ---------- Payments ---------- */
    public function payments(Request $req)
    {
        $session = $this->year;
        $method = $req->get('method');
        $query = FinancePayment::with(['student', 'studentFee.feeStructure.feeType', 'receipt'])
            ->where('year', $session);
        if ($method) $query->where('payment_method', $method);

        $d['payments'] = $query->orderByDesc('id')->limit(300)->get();
        $d['methods'] = $this->paymentMethods();
        $d['selected_method'] = $method;
        $d['today_total'] = FinancePayment::where('year', $session)->where('status', 'completed')
            ->whereDate('payment_date', today())->sum('amount');
        $d['total_collected'] = FinancePayment::where('year', $session)->where('status', 'completed')->sum('amount');
        $d['refunds_total'] = Refund::where('year', $session)->where('status', 'completed')->sum('amount');
        $d['my_classes'] = $this->my_class->all();
        $d['outstanding_fees'] = StudentFee::with(['student', 'student.admission.my_class', 'feeStructure.feeType'])
            ->where('year', $session)->where('balance', '>', 0)->where('status', '!=', 'paid')->get();
        $d['selected_student_id'] = $req->filled('student_id') ? $req->student_id : null;

        if ($d['selected_student_id']) {
            $d['selected_student'] = User::with('admission.my_class')->where('id', $req->student_id)
                ->where('user_type', 'student')->first();
            $d['selected_fees'] = StudentFee::with(['student', 'feeStructure.feeType'])
                ->where('student_id', $req->student_id)->where('year', $session)
                ->where('balance', '>', 0)->where('status', '!=', 'paid')->get();
        }

        return view('pages.support_team.finance.payments', $d);
    }

    public function paymentStudents(Request $req)
    {
        $q = trim((string)$req->get('q'));
        $class_id = $req->get('class_id');

        $query = User::with('admission.my_class')
            ->where('user_type', 'student')
            ->where(function ($w) {
                $w->where('status', '!=', 'inactive')->orWhereNull('status');
            });

        if ($class_id && $class_id !== 'all') {
            $query->whereHas('admission', function ($x) use ($class_id) {
                $x->where('my_class_id', $class_id);
            });
        }
        if ($q !== '') {
            $query->where(function ($x) use ($q) {
                $x->where('name', 'like', '%' . $q . '%')
                  ->orWhereHas('admission', function ($a) use ($q) {
                      $a->where('adm_no', 'like', '%' . $q . '%');
                  });
            });
        }

        $students = $query->orderBy('name')->limit(20)->get();

        $results = $students->map(function ($u) {
            $sr = $u->admission;
            $suffix = [];
            if ($sr && $sr->adm_no) $suffix[] = $sr->adm_no;
            if ($sr && $sr->my_class) $suffix[] = $sr->my_class->name;
            return [
                'id' => $u->id,
                'text' => $u->name . ($suffix ? ' (' . implode(' - ', $suffix) . ')' : ''),
            ];
        });

        return response()->json(['results' => $results]);
    }

    public function paymentFees(Request $req)
    {
        $student_id = $req->get('student_id');
        $fees = StudentFee::with(['student', 'feeStructure.feeType'])
            ->where('student_id', $student_id)->where('year', $this->year)
            ->where('balance', '>', 0)->where('status', '!=', 'paid')->get();

        $results = $fees->map(function ($f) {
            return [
                'id' => $f->id,
                'text' => ($f->fee_type_name ?: 'Fee') . ' - ' . $f->term . ' (KES ' . number_format($f->balance, 2) . ')',
            ];
        });

        return response()->json(['results' => $results]);
    }

    public function outstandingDetail($student_id)
    {
        $session = $this->year;
        $fees = StudentFee::with(['student.admission.my_class', 'student.admission.my_parent', 'feeStructure.feeType', 'payments'])
            ->where('student_id', $student_id)->where('year', $session)
            ->where('balance', '>', 0)->where('status', '!=', 'paid')->get();

        $first = $fees->first();
        $sr = $first && $first->student ? $first->student->admission : null;

        $data = [
            'student_id'   => (int)$student_id,
            'name'         => $first && $first->student ? $first->student->name : 'N/A',
            'adm_no'       => $sr ? $sr->adm_no : null,
            'class'        => ($sr && $sr->my_class) ? $sr->my_class->name : null,
            'parent'       => ($sr && $sr->my_parent) ? $sr->my_parent->name : null,
            'parent_phone' => ($sr && $sr->my_parent) ? $sr->my_parent->phone : null,
            'due'          => $fees->sum('amount_due'),
            'paid'         => $fees->sum('amount_paid'),
            'discount'     => $fees->sum('discount'),
            'balance'      => $fees->sum('balance'),
            'breakdown'    => $fees->map(function ($f) {
                return [
                    'fee'      => $f->fee_type_name,
                    'term'     => $f->term,
                    'due'      => (float)$f->amount_due,
                    'paid'     => (float)$f->amount_paid,
                    'discount' => (float)$f->discount,
                    'balance'  => (float)$f->balance,
                ];
            })->values(),
            'recent_payments' => $fees->flatMap(function ($f) {
                return $f->payments->sortByDesc('id')->take(2)->map(function ($p) {
                    return [
                        'amount'  => (float)$p->amount,
                        'date'    => $p->payment_date,
                        'receipt' => $p->receipt ? $p->receipt->receipt_no : null,
                        'method'  => $p->method_label,
                    ];
                });
            })->values(),
        ];

        return response()->json($data);
    }

    public function paymentsStore(Request $req)
    {
        $req->validate([
            'student_id' => 'required|exists:users,id',
            'student_fee_id' => 'required|exists:student_fees,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'payment_method' => 'required|in:' . implode(',', $this->paymentMethods()),
        ]);

        try {
            $result = $this->payment->record([
                'student_id' => $req->student_id,
                'student_fee_id' => $req->student_fee_id,
                'amount' => $req->amount,
                'payment_date' => $req->payment_date,
                'payment_method' => $req->payment_method,
                'reference_no' => $req->reference_no,
                'notes' => $req->notes,
                'finance_account_id' => $req->finance_account_id,
            ]);
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            return $req->ajax() ? Qs::json($msg, FALSE) : back()->with('flash_danger', $msg);
        }

        $receipt = $result['receipt'];
        $msg = 'Payment recorded successfully';
        if ($req->ajax()) {
            return Qs::json($msg, TRUE, ['redirect' => route('finance.receipts.show', $receipt->id)]);
        }

        return redirect()->route('finance.receipts.show', $receipt->id)
            ->with('flash_success', $msg);
    }

    public function paymentsVoid($id)
    {
        $payment = FinancePayment::find($id);
        if (!$payment) return Qs::goWithDanger();
        if ($payment->status === 'voided') return back()->with('flash_danger', 'Payment already voided.');

        // Reverse the student fee
        $studentFee = $payment->studentFee;
        $studentFee->amount_paid = max(0, $studentFee->amount_paid - $payment->amount);
        $studentFee->balance = $studentFee->amount_due - $studentFee->discount - $studentFee->amount_paid;
        $studentFee->status = $studentFee->balance <= 0 ? 'paid' : ($studentFee->amount_paid > 0 ? 'partial' : 'unpaid');
        $studentFee->save();

        // Reverse account
        $this->recordTransaction(
            $payment->finance_account_id, 'refund', $payment->amount, now()->toDateString(),
            'Void payment ' . ($payment->reference_no ?: ''),
            $payment->reference_no, 'FinancePayment#' . $payment->id
        );

        $payment->update(['status' => 'voided']);

        return back()->with('flash_success', 'Payment voided and reversed.');
    }

    /* ---------- M-Pesa ---------- */
    public function mpesaTransactions(Request $req)
    {
        $status = $req->get('status');
        $query = \App\Models\MpesaTransaction::with(['student', 'payment']);
        if ($status) $query->where('status', $status);

        $d['transactions'] = $query->orderByDesc('id')->limit(200)->get();
        $d['selected_status'] = $status;
        $d['configured'] = app(\App\Services\MpesaService::class)->configured();
        return view('pages.support_team.finance.mpesa_transactions', $d);
    }

    /* ---------- Receipts ---------- */
    public function receipts(Request $req)
    {
        $session = $this->year;
        $d['receipts'] = FinanceReceipt::with(['student', 'payment'])
            ->where('year', $session)->orderByDesc('id')->get();
        return view('pages.support_team.finance.receipts', $d);
    }

    public function receiptsShow($id)
    {
        $d['receipt'] = FinanceReceipt::with(['student', 'payment.studentFee.feeStructure.feeType', 'payment.student'])->find($id);
        if (!$d['receipt']) return Qs::goWithDanger();
        $d['settings'] = app(\App\Models\Setting::class)->all()->flatMap(function($s){ return [$s->type => $s->description]; });
        return view('pages.support_team.finance.receipt_show', $d);
    }

    public function receiptsPdf($id)
    {
        $d['receipt'] = FinanceReceipt::with(['student', 'student.admission.my_class', 'payment.studentFee.feeStructure.feeType', 'payment.student', 'payment.account'])->find($id);
        if (!$d['receipt']) return Qs::goWithDanger();
        $d['settings'] = app(\App\Models\Setting::class)->all()->flatMap(function($s){ return [$s->type => $s->description]; });
        $pdf_name = 'Receipt_' . $d['receipt']->receipt_no;
        return PDF::loadView('pages.support_team.finance.receipt_pdf', $d)->download($pdf_name);
    }

    /* ---------- Fee Statement ---------- */
    public function statement($student_id, Request $req)
    {
        $student = User::find($student_id);
        if (!$student) return Qs::goWithDanger();

        $term = $req->get('term', 'First Term');
        $session = $this->year;

        $terms = StudentFee::where('student_id', $student_id)->where('year', $session)
            ->distinct()->pluck('term')->toArray();
        if (empty($terms) || !in_array($term, $terms)) $term = $terms[0] ?? 'First Term';

        $fees = StudentFee::with(['feeStructure.feeType', 'discounts', 'payments'])
            ->where('student_id', $student_id)->where('year', $session)->where('term', $term)->get();

        $charged = $fees->sum('amount_due');
        $discount = $fees->sum('discount');
        $paid = $fees->sum('amount_paid');
        $balance = $fees->sum('balance');

        $d['student'] = $student;
        $d['sr'] = StudentRecord::where('user_id', $student_id)->first();
        $d['fees'] = $fees;
        $d['payments'] = FinancePayment::with(['receipt', 'studentFee.feeStructure.feeType'])
            ->where('student_id', $student_id)->where('year', $session)->orderBy('payment_date')->get();
        $d['refunds'] = Refund::where('student_id', $student_id)->where('year', $session)->orderBy('refund_date')->get();
        $d['discounts'] = Discount::with(['studentFee.feeStructure.feeType'])
            ->where('student_id', $student_id)->where('year', $session)->get();
        $d['all_terms'] = $terms;
        $d['terms'] = $this->terms();
        $d['selected'] = $term;
        $d['totals'] = compact('charged', 'discount', 'paid', 'balance');
        $d['year'] = $session;
        $d['years'] = range(2018, (int)substr($session, 0, 4));

        return view('pages.support_team.finance.statement', $d);
    }

    public function statementPdf($student_id, Request $req)
    {
        $student = User::find($student_id);
        if (!$student) return Qs::goWithDanger();

        $term = $req->get('term', 'First Term');
        $session = $this->year;

        $terms = StudentFee::where('student_id', $student_id)->where('year', $session)
            ->distinct()->pluck('term')->toArray();
        if (empty($terms) || !in_array($term, $terms)) $term = $terms[0] ?? 'First Term';

        $fees = StudentFee::with(['feeStructure.feeType', 'discounts', 'payments'])
            ->where('student_id', $student_id)->where('year', $session)->where('term', $term)->get();

        $d['student'] = $student;
        $d['sr'] = StudentRecord::where('user_id', $student_id)->first();
        $d['fees'] = $fees;
        $d['payments'] = FinancePayment::with(['receipt', 'studentFee.feeStructure.feeType'])
            ->where('student_id', $student_id)->where('year', $session)->where('term', $term)->orderBy('payment_date')->get();
        $d['refunds'] = Refund::where('student_id', $student_id)->where('year', $session)->orderBy('refund_date')->get();
        $d['discounts'] = Discount::with(['studentFee.feeStructure.feeType'])
            ->where('student_id', $student_id)->where('year', $session)->get();
        $d['selected'] = $term;
        $d['year'] = $session;
        $d['settings'] = app(\App\Models\Setting::class)->all()->flatMap(function($s){ return [$s->type => $s->description]; });

        $pdf_name = 'Statement_' . $student->name . '_' . $term;
        return PDF::loadView('pages.support_team.finance.statement_pdf', $d)->download($pdf_name);
    }

    /* ---------- Discounts ---------- */
    public function discounts(Request $req)
    {
        $session = $this->year;
        $query = Discount::with(['student', 'studentFee.feeStructure.feeType']);
        $d['discounts'] = $query->orderByDesc('id')->get();
        $d['students'] = User::where('user_type', 'student')->orderBy('name')->get();
        $d['student_fees'] = StudentFee::with('feeStructure.feeType')->where('year', $session)->get();
        return view('pages.support_team.finance.discounts', $d);
    }

    public function discountsStore(Request $req)
    {
        $req->validate([
            'student_id' => 'required|exists:users,id',
            'student_fee_id' => 'required|exists:student_fees,id',
            'type' => 'required|in:discount,scholarship,waiver',
            'amount' => 'required|numeric|min:0.01',
            'reason' => 'required',
        ]);

        $studentFee = StudentFee::find($req->student_fee_id);

        $discount = Discount::create([
            'student_id' => $req->student_id,
            'student_fee_id' => $studentFee->id,
            'type' => $req->type,
            'amount' => $req->amount,
            'reason' => $req->reason,
            'approved_by' => Auth::user()->name,
            'status' => 'active',
            'session' => $this->year,
            'term' => $studentFee->term,
            'year' => $this->year,
        ]);

        $totalDiscount = $studentFee->discounts()->where('status', 'active')->sum('amount');
        $studentFee->update([
            'discount' => $totalDiscount,
            'balance' => max(0, $studentFee->amount_due - $totalDiscount - $studentFee->amount_paid),
        ]);

        return Qs::jsonStoreOk();
    }

    public function discountsRevoke($id)
    {
        $discount = Discount::find($id);
        if (!$discount) return Qs::goWithDanger();
        $discount->update(['status' => 'revoked']);

        $studentFee = $discount->studentFee;
        $totalDiscount = $studentFee->discounts()->where('status', 'active')->sum('amount');
        $studentFee->update([
            'discount' => $totalDiscount,
            'balance' => max(0, $studentFee->amount_due - $totalDiscount - $studentFee->amount_paid),
        ]);

        return back()->with('flash_success', 'Discount revoked.');
    }

    /* ---------- Refunds ---------- */
    public function refunds()
    {
        $d['refunds'] = Refund::with(['student', 'payment'])->orderByDesc('id')->get();
        $d['payments'] = FinancePayment::with('student')->where('status', 'completed')->get();
        $d['students'] = User::where('user_type', 'student')->orderBy('name')->get();
        return view('pages.support_team.finance.refunds', $d);
    }

    public function refundsStore(Request $req)
    {
        $req->validate([
            'finance_payment_id' => 'required|exists:finance_payments,id',
            'amount' => 'required|numeric|min:0.01',
            'reason' => 'required',
            'refund_date' => 'required|date',
        ]);

        $payment = FinancePayment::find($req->finance_payment_id);
        if ($req->amount > $payment->amount) return back()->with('flash_danger', 'Refund exceeds original payment.');

        Refund::create([
            'finance_payment_id' => $payment->id,
            'student_id' => $payment->student_id,
            'amount' => $req->amount,
            'reason' => $req->reason,
            'processed_by' => Auth::user()->name,
            'status' => 'completed',
            'refund_date' => $req->refund_date,
            'session' => $this->year,
            'term' => $payment->term,
            'year' => $this->year,
        ]);

        // Increase the student's balance accordingly
        $studentFee = $payment->studentFee;
        $studentFee->amount_paid = max(0, $studentFee->amount_paid - $req->amount);
        $studentFee->balance = $studentFee->amount_due - $studentFee->discount - $studentFee->amount_paid;
        $studentFee->status = $studentFee->balance <= 0 ? 'paid' : ($studentFee->amount_paid > 0 ? 'partial' : 'unpaid');
        $studentFee->save();

        $this->recordTransaction(
            $payment->finance_account_id, 'refund', $req->amount, $req->refund_date,
            'Refund', null, 'Refund on FinancePayment#' . $payment->id
        );

        return back()->with('flash_success', 'Refund recorded.');
    }

    public function refundsVoid($id)
    {
        Refund::find($id)->update(['status' => 'voided']);
        return back()->with('flash_success', 'Refund voided.');
    }

    /* ---------- Expenses ---------- */
    public function expenses(Request $req)
    {
        $session = $this->year;
        $category = $req->get('category');
        $query = Expense::with(['category', 'supplier', 'account'])
            ->where('year', $session);
        if ($category) $query->where('expense_category_id', $category);

        $d['expenses'] = $query->orderByDesc('id')->get();
        $d['categories'] = ExpenseCategory::all();
        $d['suppliers'] = Supplier::where('is_active', true)->get();
        $d['accounts'] = FinanceAccount::where('is_active', true)->get();
        $d['methods'] = $this->paymentMethods();
        $d['selected_category'] = $category;
        return view('pages.support_team.finance.expenses', $d);
    }

    public function expensesStore(Request $req)
    {
        $req->validate([
            'expense_category_id' => 'required|exists:expense_categories,id',
            'description' => 'required',
            'amount' => 'required|numeric|min:0.01',
            'expense_date' => 'required|date',
            'payment_method' => 'required|in:' . implode(',', $this->paymentMethods()),
        ]);

        $expense = Expense::create([
            'expense_category_id' => $req->expense_category_id,
            'supplier_id' => $req->supplier_id,
            'finance_account_id' => $req->finance_account_id,
            'description' => $req->description,
            'amount' => $req->amount,
            'expense_date' => $req->expense_date,
            'payee' => $req->payee,
            'payment_method' => $req->payment_method,
            'reference_no' => $req->reference_no,
            'receipt_document' => $req->receipt_document,
            'status' => 'completed',
            'recorded_by' => Auth::user()->name,
            'session' => $this->year,
            'term' => $req->term ?: 'First Term',
            'year' => $this->year,
        ]);

        $this->recordTransaction(
            $req->finance_account_id, 'expense', $req->amount, $req->expense_date,
            $req->description, $req->reference_no, 'Expense#' . $expense->id
        );

        return Qs::jsonStoreOk();
    }

    public function expensesVoid($id)
    {
        $expense = Expense::find($id);
        if (!$expense) return Qs::goWithDanger();
        if ($expense->status === 'voided') return back()->with('flash_danger', 'Expense already voided.');
        $expense->update(['status' => 'voided']);
        return back()->with('flash_success', 'Expense voided.');
    }

    /* ---------- Expense Categories (used in settings) ---------- */
    public function expenseCategoriesStore(Request $req)
    {
        $req->validate(['name' => 'required']);
        ExpenseCategory::create(['name' => $req->name, 'description' => $req->description]);
        return Qs::jsonStoreOk();
    }

    /* ---------- Suppliers ---------- */
    public function suppliers()
    {
        $d['suppliers'] = Supplier::orderBy('name')->get();
        return view('pages.support_team.finance.suppliers', $d);
    }

    public function suppliersStore(Request $req)
    {
        $req->validate(['name' => 'required']);
        Supplier::create($req->only(['name', 'contact_person', 'phone', 'email', 'address']));
        return Qs::jsonStoreOk();
    }

    public function suppliersUpdate(Request $req, $id)
    {
        $req->validate(['name' => 'required']);
        Supplier::find($id)->update($req->only(['name', 'contact_person', 'phone', 'email', 'address']));
        return Qs::jsonUpdateOk();
    }

    public function suppliersToggle($id)
    {
        $s = Supplier::find($id);
        $s->update(['is_active' => !$s->is_active]);
        return back()->with('flash_success', __('msg.update_ok'));
    }

    /* ---------- Cash & Bank (Accounts) ---------- */
    public function accounts()
    {
        $session = $this->year;
        $d['accounts'] = FinanceAccount::with('transactions')->get();
        $d['transactions'] = FinanceTransaction::with('account')
            ->where('year', $session)->orderByDesc('id')->limit(50)->get();
        return view('pages.support_team.finance.accounts', $d);
    }

    public function accountsStore(Request $req)
    {
        $req->validate(['name' => 'required', 'type' => 'required|in:cash,bank,mobile_money']);
        $data = $req->only(['name', 'type', 'account_number', 'bank_name', 'branch']);
        $data['opening_balance'] = $req->opening_balance ?: 0;
        $data['current_balance'] = $data['opening_balance'];
        FinanceAccount::create($data);
        return Qs::jsonStoreOk();
    }

    public function accountsUpdate(Request $req, $id)
    {
        $req->validate(['name' => 'required', 'type' => 'required']);
        $account = FinanceAccount::find($id);
        $data = $req->only(['name', 'type', 'account_number', 'bank_name', 'branch']);
        $data['is_active'] = $req->boolean('is_active');
        $account->update($data);
        return Qs::jsonUpdateOk();
    }

    /* ---------- Reports ---------- */
    public function reports(Request $req)
    {
        $session = $this->year;
        $type = $req->get('type', 'fees_collected');
        $term = $req->get('term');

        $byTerm = $term ? ['term' => $term] : [];

        $report = [];
        $fees_collected = FinancePayment::where('year', $session)->where('status', 'completed');
        $expenses = Expense::where('year', $session)->where('status', 'completed');
        $student_fees = StudentFee::where('year', $session);
        if ($term) { $fees_collected = $fees_collected->where('term', $term); $expenses = $expenses->where('term', $term); $student_fees = $student_fees->where('term', $term); }

        switch ($type) {
            case 'fees_collected':
                $report['rows'] = FinancePayment::with(['student', 'studentFee.feeStructure.feeType'])
                    ->where('year', $session)->where('status', 'completed')
                    ->when($term, fn($q) => $q->where('term', $term))
                    ->orderByDesc('id')->get();
                $report['total'] = $fees_collected->sum('amount');
                break;

            case 'outstanding':
                $report['rows'] = StudentFee::with(['student', 'feeStructure.feeType'])
                    ->where('year', $session)->where('balance', '>', 0)
                    ->when($term, fn($q) => $q->where('term', $term))
                    ->get();
                $report['total'] = $student_fees->where('balance', '>', 0)->sum('balance');
                break;

            case 'student_balances':
                $report['rows'] = StudentFee::with(['student', 'feeStructure.feeType'])
                    ->where('year', $session)->when($term, fn($q) => $q->where('term', $term))
                    ->get()->groupBy('student_id')->map(function($group){
                        return [
                            'student' => $group->first()->student,
                            'total_charged' => $group->sum('amount_due'),
                            'total_paid' => $group->sum('amount_paid'),
                            'balance' => $group->sum('balance'),
                        ];
                    })->values();
                $report['total'] = $report['rows']->sum('balance');
                break;

            case 'payments':
                $report['rows'] = FinancePayment::with(['student', 'studentFee.feeStructure.feeType'])
                    ->where('year', $session)
                    ->when($term, fn($q) => $q->where('term', $term))
                    ->orderByDesc('id')->get();
                $report['total'] = $fees_collected->sum('amount');
                break;

            case 'expenses':
                $report['rows'] = Expense::with(['category', 'supplier'])->where('year', $session)
                    ->when($term, fn($q) => $q->where('term', $term))
                    ->orderByDesc('id')->get();
                $report['total'] = $expenses->sum('amount');
                break;

            case 'revenue':
                $report['rows'] = FinancePayment::selectRaw('payment_method as method, sum(amount) as total')
                    ->where('year', $session)->where('status', 'completed')
                    ->when($term, fn($q) => $q->where('term', $term))
                    ->groupBy('payment_method')->get();
                $report['total'] = $fees_collected->sum('amount');
                break;

            case 'net_income':
                $income = $fees_collected->sum('amount');
                $exp = $expenses->sum('amount');
                $ref = Refund::where('year', $session)->where('status', 'completed')
                    ->when($term, fn($q) => $q->where('term', $term))->sum('amount');
                $report['rows'] = [
                    ['item' => 'Total Fees Collected', 'amount' => $income],
                    ['item' => 'Total Expenses', 'amount' => $exp],
                    ['item' => 'Total Refunds', 'amount' => $ref],
                    ['item' => 'Net Income', 'amount' => $income - $exp - $ref],
                ];
                $report['total'] = $income - $exp - $ref;
                break;

            case 'payments_by_method':
                $report['rows'] = FinancePayment::selectRaw('payment_method as method, count(*) as count, sum(amount) as total')
                    ->where('year', $session)->where('status', 'completed')
                    ->when($term, fn($q) => $q->where('term', $term))
                    ->groupBy('payment_method')->get();
                $report['total'] = $fees_collected->sum('amount');
                break;

            case 'fees_by_class':
                $report['rows'] = StudentFee::with('feeStructure.myClass')
                    ->where('year', $session)->when($term, fn($q) => $q->where('term', $term))
                    ->get()->groupBy('feeStructure.my_class_id')->map(function($group){
                        return [
                            'class' => $group->first()->feeStructure && $group->first()->feeStructure->myClass ? $group->first()->feeStructure->myClass->name : 'N/A',
                            'total_charged' => $group->sum('amount_due'),
                            'total_paid' => $group->sum('amount_paid'),
                            'balance' => $group->sum('balance'),
                        ];
                    })->values();
                $report['total'] = $report['rows']->sum('balance');
                break;

            case 'fees_by_term':
                $report['rows'] = StudentFee::where('year', $session)
                    ->groupBy('term')->selectRaw('term, sum(amount_due) as charged, sum(amount_paid) as paid, sum(balance) as balance')
                    ->get();
                $report['total'] = $report['rows']->sum('charged');
                break;

            default:
                return $this->reports($req);
        }

        $d['report'] = $report;
        $d['type'] = $type;
        $d['term'] = $term;
        $d['year'] = $session;
        $d['terms'] = $this->terms();

        return view('pages.support_team.finance.reports', $d);
    }

    /* ---------- Settings ---------- */
    public function settings()
    {
        $this->defaultFeeTypes();
        if (ExpenseCategory::count() === 0) {
            foreach (['Salaries', 'Food', 'Electricity', 'Water', 'Transport', 'Maintenance', 'Supplies', 'Other'] as $i => $name) {
                ExpenseCategory::create(['name' => $name]);
            }
        }
        if (FinanceAccount::count() === 0) {
            FinanceAccount::create(['name' => 'Main Cash', 'type' => 'cash', 'opening_balance' => 0, 'current_balance' => 0]);
            FinanceAccount::create(['name' => 'Main Bank Account', 'type' => 'bank', 'opening_balance' => 0, 'current_balance' => 0]);
            FinanceAccount::create(['name' => 'Mobile Money', 'type' => 'mobile_money', 'opening_balance' => 0, 'current_balance' => 0]);
        }

        $d['fee_types'] = FeeType::orderBy('name')->get();
        $d['expense_categories'] = ExpenseCategory::orderBy('name')->get();
        $d['payment_methods'] = $this->paymentMethods();
        $d['accounts'] = FinanceAccount::all();
        $d['suppliers'] = Supplier::all();
        $d['terms'] = $this->terms();
        $d['mpesa'] = [
            'environment' => Qs::getSetting('mpesa_environment'),
            'consumer_key' => Qs::getSetting('mpesa_consumer_key'),
            'consumer_secret' => Qs::getSetting('mpesa_consumer_secret'),
            'passkey' => Qs::getSetting('mpesa_passkey'),
            'shortcode' => Qs::getSetting('mpesa_shortcode'),
            'callback_url' => Qs::getSetting('mpesa_callback_url'),
            'account_reference' => Qs::getSetting('mpesa_account_reference'),
            'transaction_type' => Qs::getSetting('mpesa_transaction_type'),
        ];
        return view('pages.support_team.finance.settings', $d);
    }

    public function mpesaSettingsStore(Request $req)
    {
        if (!Qs::userIsTeamSA() && !Qs::userIsSuperAdmin()) {
            return back()->with('flash_danger', 'Only administrators can change M-Pesa settings.');
        }

        $data = [
            'mpesa_environment' => $req->environment,
            'mpesa_consumer_key' => $req->consumer_key,
            'mpesa_consumer_secret' => $req->consumer_secret,
            'mpesa_passkey' => $req->passkey,
            'mpesa_shortcode' => $req->shortcode,
            'mpesa_callback_url' => $req->callback_url,
            'mpesa_account_reference' => $req->account_reference,
            'mpesa_transaction_type' => $req->transaction_type,
        ];

        foreach ($data as $type => $value) {
            \App\Models\Setting::updateOrCreate(['type' => $type], ['description' => $value]);
        }
        \Illuminate\Support\Facades\Cache::forget('settings');

        return back()->with('flash_success', 'M-Pesa settings saved.');
    }
}