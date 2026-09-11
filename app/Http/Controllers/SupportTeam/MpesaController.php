<?php

namespace App\Http\Controllers\SupportTeam;

use App\Helpers\Qs;
use App\Http\Controllers\Controller;
use App\Models\MpesaTransaction;
use App\Models\StudentFee;
use App\Services\MpesaService;
use App\Services\PaymentService;
use App\User;
use Illuminate\Http\Request;

class MpesaController extends Controller
{
    protected $service;
    protected $payments;

    public function __construct(MpesaService $service, PaymentService $payments)
    {
        $this->service = $service;
        $this->payments = $payments;
        $this->middleware('teamAccount', ['except' => ['callback']]);
    }

    public function stkPush(Request $req)
    {
        $req->validate([
            'student_id' => 'required|exists:users,id',
            'student_fee_id' => 'required|exists:student_fees,id',
            'amount' => 'required|numeric|min:1',
            'phone' => 'required|string',
        ]);

        if (!$this->service->configured()) {
            return Qs::json('M-Pesa is not configured. Ask an administrator to set the M-Pesa settings in Finance > Settings.', FALSE);
        }

        $studentFee = StudentFee::find($req->student_fee_id);
        if (!$studentFee) {
            return Qs::json('Fee record not found.', FALSE);
        }

        $availableBalance = max(0, $studentFee->amount_due - $studentFee->discount - $studentFee->amount_paid);
        if ($req->amount > $availableBalance) {
            return Qs::json('Amount exceeds the outstanding balance.', FALSE);
        }

        $student = User::find($req->student_id);
        $accountRef = $student && $student->student_record && $student->student_record->adm_no
            ? substr('FEE-' . $student->student_record->adm_no, 0, 12)
            : 'FEE-' . $studentFee->id;

        $txn = MpesaTransaction::create([
            'phone' => $req->phone,
            'amount' => $req->amount,
            'reference' => $req->reference_no,
            'description' => ($studentFee->fee_type_name ?: 'School fees') . ' - ' . $studentFee->term,
            'student_id' => $req->student_id,
            'student_fee_id' => $studentFee->id,
            'status' => 'pending',
        ]);

        $res = $this->service->stkPush($req->phone, $req->amount, $accountRef, $txn->description);

        if (!isset($res['ResponseCode']) || (string) $res['ResponseCode'] !== '0') {
            $txn->update([
                'result_desc' => $res['errorMessage'] ?? ($res['ResponseDescription'] ?? 'STK push failed.'),
                'raw_callback' => json_encode($res),
                'status' => 'failed',
            ]);
            return Qs::json($txn->result_desc, FALSE);
        }

        $txn->update([
            'checkout_request_id' => $res['CheckoutRequestID'] ?? null,
            'merchant_request_id' => $res['MerchantRequestID'] ?? null,
            'result_desc' => $res['ResponseDescription'] ?? 'STK push sent.',
            'status' => 'processing',
        ]);

        return Qs::json('STK push sent. Complete the payment by entering your M-Pesa PIN on your phone.', TRUE, [
            'id' => $txn->id,
        ]);
    }

    public function status($id)
    {
        $txn = MpesaTransaction::find($id);
        if (!$txn) {
            return Qs::json('Transaction not found.', FALSE);
        }

        if ($txn->status === 'completed') {
            return response()->json([
                'ok' => true,
                'status' => 'completed',
                'receipt' => $txn->mpesa_receipt_number,
                'redirect' => $txn->finance_payment_id ? route('finance.receipts.show', $txn->finance_payment_id) : null,
            ]);
        }

        if ($txn->status === 'failed') {
            return response()->json(['ok' => false, 'status' => 'failed', 'msg' => $txn->result_desc ?: 'Payment failed.']);
        }

        $res = $this->service->queryStatus($txn->checkout_request_id);
        $resultCode = $res['ResultCode'] ?? ($res['resultCode'] ?? null);
        $resultDesc = $res['ResultDesc'] ?? ($res['resultDesc'] ?? '');

        if ($resultCode === '0' || $resultCode === 0) {
            $txn = $this->finalize($txn, [
                'mpesa_receipt_number' => $this->extractMeta($res, 'MpesaReceiptNumber'),
                'result_code' => '0',
                'result_desc' => $resultDesc ?: 'Payment successful',
            ]);

            return response()->json([
                'ok' => true,
                'status' => 'completed',
                'receipt' => $txn->mpesa_receipt_number,
                'redirect' => route('finance.receipts.show', $txn->finance_payment_id),
            ]);
        }

        if (in_array((string) $resultCode, ['1037', '1032', '1', '2001', '500'], true)) {
            $txn->update([
                'status' => 'failed',
                'result_code' => (string) $resultCode,
                'result_desc' => $resultDesc ?: 'Payment not completed.',
            ]);
            return response()->json(['ok' => false, 'status' => 'failed', 'msg' => $txn->result_desc]);
        }

        return response()->json(['ok' => true, 'status' => 'pending', 'msg' => 'Waiting for payment confirmation...']);
    }

    public function callback(Request $req)
    {
        $data = $req->all();
        $cb = $data['Body']['stkCallback'] ?? null;
        if (!$cb) {
            return response()->json(['ResultCode' => 1, 'ResultDesc' => 'Invalid callback payload']);
        }

        $txn = MpesaTransaction::where('checkout_request_id', $cb['CheckoutRequestID'])->first();
        if (!$txn) {
            return response()->json(['ResultCode' => 1, 'ResultDesc' => 'Transaction not found']);
        }

        $resultCode = $cb['ResultCode'] ?? null;
        $resultDesc = $cb['ResultDesc'] ?? '';

        $txn->update([
            'raw_callback' => json_encode($data),
            'result_code' => (string) $resultCode,
            'result_desc' => $resultDesc,
        ]);

        if ((string) $resultCode === '0') {
            $this->finalize($txn, [
                'mpesa_receipt_number' => $this->extractMeta($cb, 'MpesaReceiptNumber'),
                'result_code' => '0',
                'result_desc' => $resultDesc ?: 'Payment successful',
            ]);
        } else {
            $txn->update(['status' => 'failed']);
        }

        return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Success']);
    }

    protected function extractMeta($payload, $name)
    {
        $items = $payload['CallbackMetadata']['Item'] ?? [];
        foreach ($items as $item) {
            if (($item['Name'] ?? null) === $name) {
                return (string) ($item['Value'] ?? '');
            }
        }
        return null;
    }

    protected function finalize(MpesaTransaction $txn, array $extra = [])
    {
        if ($txn->status === 'completed') {
            return $txn->refresh();
        }

        $txn->update($extra);

        $result = $this->payments->record([
            'student_id' => $txn->student_id,
            'student_fee_id' => $txn->student_fee_id,
            'amount' => $txn->amount,
            'payment_date' => now()->toDateString(),
            'payment_method' => 'mpesa',
            'reference_no' => $txn->mpesa_receipt_number ?: $txn->checkout_request_id,
            'notes' => 'M-Pesa mobile payment - ' . ($txn->mpesa_receipt_number ?: ''),
        ]);

        $txn->update([
            'status' => 'completed',
            'finance_payment_id' => $result['payment']->id,
            'completed_at' => now(),
        ]);

        return $txn->refresh();
    }
}