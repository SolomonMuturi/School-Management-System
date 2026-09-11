<?php

namespace App\Models;

use App\User;
use Eloquent;

class Refund extends Eloquent
{
    protected $fillable = [
        'finance_payment_id', 'student_id', 'amount', 'reason',
        'processed_by', 'status', 'refund_date', 'session', 'term', 'year'
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