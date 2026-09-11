<?php

namespace App\Http\Controllers\SupportTeam;

use App\Helpers\Qs;
use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Expense;
use App\Models\FinancePayment;
use App\Models\MyClass;
use App\Models\StudentRecord;
use App\Models\Subject;
use App\Models\StudentFee;
use App\User;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function __construct()
    {
        $this->middleware('perm:Analytics');
    }

    public function dashboard()
    {
        $session = Qs::getCurrentSession();

        $d['kp_academic'] = [
            'students' => StudentRecord::where('grad', 0)->count(),
            'teachers' => User::where('user_type', 'teacher')->count(),
            'subjects' => Subject::count(),
            'classes' => MyClass::count(),
        ];

        $d['kp_financial'] = [
            'collected' => (float) FinancePayment::where('year', $session)->where('status', 'completed')->sum('amount'),
            'outstanding' => (float) StudentFee::where('year', $session)->where('balance', '>', 0)->sum('balance'),
            'expenses' => (float) Expense::where('year', $session)->where('status', 'completed')->sum('amount'),
            'net' => (float) FinancePayment::where('year', $session)->where('status', 'completed')->sum('amount')
                - (float) Expense::where('year', $session)->where('status', 'completed')->sum('amount'),
        ];

        $d['students_by_class'] = MyClass::withCount(['student_record' => function ($q) {
            $q->where('grad', 0);
        }])->orderBy('name')->get();

        $d['monthly_cashflow'] = $this->monthlyCashflow($session);
        $d['payment_methods'] = FinancePayment::where('year', $session)->where('status', 'completed')
            ->selectRaw('payment_method, SUM(amount) as total, COUNT(*) as count')
            ->groupBy('payment_method')->get();

        $d['outstanding_by_class'] = StudentFee::with('feeStructure.myClass')
            ->where('year', $session)->where('balance', '>', 0)
            ->get()
            ->groupBy('feeStructure.my_class_id')
            ->map(function ($group) {
                return [
                    'class' => $group->first()->feeStructure && $group->first()->feeStructure->myClass
                        ? $group->first()->feeStructure->myClass->name
                        : 'N/A',
                    'balance' => $group->sum('balance'),
                ];
            })->values();

        $latestExam = Exam::where('year', $session)->latest('id')->first();
        $d['exam_performance'] = collect();
        if ($latestExam) {
            $d['exam_performance'] = DB::table('exam_records')
                ->where('exam_id', $latestExam->id)
                ->selectRaw('my_class_id, AVG(ave) as avg_ave, COUNT(*) as students')
                ->groupBy('my_class_id')
                ->orderBy('my_class_id')
                ->get()
                ->map(function ($row) {
                    $class = MyClass::find($row->my_class_id);
                    return [
                        'class' => $class ? $class->name : 'N/A',
                        'average' => round((float) $row->avg_ave, 1),
                        'students' => $row->students,
                    ];
                });
        }

        $d['marks_distribution'] = DB::table('marks')
            ->where('year', $session)
            ->selectRaw("CASE
                WHEN COALESCE(cum, tca + exm) >= 80 THEN '80-100'
                WHEN COALESCE(cum, tca + exm) >= 70 THEN '70-79'
                WHEN COALESCE(cum, tca + exm) >= 60 THEN '60-69'
                WHEN COALESCE(cum, tca + exm) >= 50 THEN '50-59'
                WHEN COALESCE(cum, tca + exm) >= 40 THEN '40-49'
                ELSE '0-39'
            END as band, COUNT(*) as count")
            ->groupBy('band')->orderBy('band')->get();

        $d['latest_exam'] = $latestExam;
        $d['year'] = $session;

        return view('pages.support_team.analytics.dashboard', $d);
    }

    protected function monthlyCashflow($session)
    {
        $income = FinancePayment::where('year', $session)->where('status', 'completed')
            ->selectRaw("DATE_FORMAT(payment_date, '%Y-%m') as month, SUM(amount) as total")
            ->groupBy('month')->orderBy('month')->get()->keyBy('month');

        $expenses = Expense::where('year', $session)->where('status', 'completed')
            ->selectRaw("DATE_FORMAT(expense_date, '%Y-%m') as month, SUM(amount) as total")
            ->groupBy('month')->orderBy('month')->get()->keyBy('month');

        $months = [];
        for ($i = 5; $i >= 0; $i--) {
            $months[] = \Carbon\Carbon::now()->startOfMonth()->subMonths($i);
        }

        return array_map(function ($month) use ($income, $expenses) {
            $key = $month->format('Y-m');
            return [
                'label' => $month->format('M'),
                'income' => round((float) (isset($income[$key]) ? $income[$key]->total : 0), 2),
                'expenses' => round((float) (isset($expenses[$key]) ? $expenses[$key]->total : 0), 2),
            ];
        }, $months);
    }
}