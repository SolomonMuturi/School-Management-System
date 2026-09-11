<?php

namespace App\Services;

use App\Helpers\Qs;
use App\Models\AppNotification;
use App\Models\FinancePayment;
use App\Models\FinanceReceipt;
use App\Models\FinanceTransaction;
use App\Models\FinanceAccount;
use App\Models\StudentFee;
use App\Models\StudentRecord;
use App\User;

class PaymentService
{
    protected $year;

    public function __construct()
    {
        $this->year = Qs::getCurrentSession();
    }

    public function record(array $data): array
    {
        $studentFee = StudentFee::find($data['student_fee_id']);
        if (!$studentFee) {
            throw new \Exception('Fee record not found.');
        }

        $amount = (float) $data['amount'];
        $availableBalance = max(0, $studentFee->amount_due - $studentFee->discount - $studentFee->amount_paid);
        if ($amount > $availableBalance) {
            throw new \Exception('Payment exceeds the outstanding balance.');
        }

        $newPaid = $studentFee->amount_paid + $amount;
        $newBalance = max(0, $studentFee->amount_due - $studentFee->discount - $newPaid);

        $payment = FinancePayment::create([
            'student_id' => $data['student_id'],
            'student_fee_id' => $studentFee->id,
            'amount' => $amount,
            'payment_date' => $data['payment_date'] ?? now()->toDateString(),
            'payment_method' => $data['payment_method'] ?? 'mobile_money',
            'reference_no' => $data['reference_no'] ?? null,
            'received_by' => $data['received_by'] ?? (auth()->check() ? auth()->user()->name : 'M-Pesa'),
            'notes' => $data['notes'] ?? null,
            'status' => 'completed',
            'finance_account_id' => $data['finance_account_id'] ?? null,
            'session' => $this->year,
            'term' => $studentFee->term,
            'year' => $this->year,
        ]);

        $studentFee->update([
            'amount_paid' => $newPaid,
            'balance' => $newBalance,
            'status' => $newBalance <= 0 ? 'paid' : 'partial',
        ]);

        $receipt = FinanceReceipt::create([
            'receipt_no' => $this->generateReceiptNo(),
            'finance_payment_id' => $payment->id,
            'student_id' => $data['student_id'],
            'amount' => $amount,
            'payment_method' => $payment->payment_method,
            'reference_no' => $payment->reference_no,
            'balance_after' => $newBalance,
            'session' => $this->year,
            'term' => $studentFee->term,
            'year' => $this->year,
        ]);

        if ($data['finance_account_id']) {
            $this->recordTransaction(
                $data['finance_account_id'], 'income', $amount, $payment->payment_date,
                'Payment - ' . ($payment->reference_no ?: $payment->payment_method),
                $payment->reference_no, 'FinancePayment#' . $payment->id
            );
        }

        $this->notify($data['student_id'], $payment, $newBalance);

        return [
            'payment' => $payment,
            'receipt' => $receipt,
            'balance' => $newBalance,
        ];
    }

    protected function generateReceiptNo()
    {
        return 'RCP-' . strtoupper(substr(str_replace('-', '', $this->year), 0, 8)) . '-' . str_pad(FinanceReceipt::count() + 1, 5, '0', STR_PAD_LEFT);
    }

    protected function recordTransaction($account_id, $type, $amount, $date, $description, $reference = null, $related = null)
    {
        $account = FinanceAccount::find($account_id);
        if (!$account) {
            return;
        }

        FinanceTransaction::create([
            'finance_account_id' => $account->id,
            'type' => $type,
            'amount' => $amount,
            'description' => $description,
            'transaction_date' => $date,
            'reference_no' => $reference,
            'related_to' => $related,
            'session' => $this->year,
            'term' => null,
            'year' => $this->year,
        ]);

        $account->current_balance = $type === 'expense' || $type === 'refund'
            ? $account->current_balance - $amount
            : $account->current_balance + $amount;
        $account->save();
    }

    protected function notify($studentId, FinancePayment $payment, $newBalance)
    {
        $student = User::find($studentId);
        if (!$student) {
            return;
        }

        $amount = number_format($payment->amount, 2);
        $recipients = [$studentId];

        if ($student->student_record && $student->student_record->my_parent_id) {
            $recipients[] = $student->student_record->my_parent_id;
        }

        AppNotification::createFor(
            $recipients,
            'Payment received',
            'A payment of ' . $amount . ' was recorded against your fees. Receipt: ' . $payment->receipt()->value('receipt_no'),
            $payment->receipt ? route('finance.receipts.show', $payment->receipt->id) : null,
            'success'
        );
    }
}