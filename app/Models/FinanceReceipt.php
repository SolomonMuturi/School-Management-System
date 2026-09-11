<?php

namespace App\Models;

use App\User;
use Eloquent;

class FinanceReceipt extends Eloquent
{
    protected $fillable = [
        'receipt_no', 'finance_payment_id', 'student_id', 'amount',
        'payment_method', 'reference_no', 'balance_after', 'session', 'term', 'year'
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function payment()
    {
        return $this->belongsTo(FinancePayment::class, 'finance_payment_id');
    }
}