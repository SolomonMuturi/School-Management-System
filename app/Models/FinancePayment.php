<?php

namespace App\Models;

use App\User;
use Eloquent;

class FinancePayment extends Eloquent
{
    protected $fillable = [
        'student_id', 'student_fee_id', 'amount', 'payment_date', 'payment_method',
        'reference_no', 'received_by', 'notes', 'status', 'session', 'term', 'year', 'finance_account_id'
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function studentFee()
    {
        return $this->belongsTo(StudentFee::class);
    }

    public function receipt()
    {
        return $this->hasOne(FinanceReceipt::class);
    }

    public function refunds()
    {
        return $this->hasMany(Refund::class);
    }

    public function account()
    {
        return $this->belongsTo(FinanceAccount::class, 'finance_account_id');
    }

    public function getMethodLabelAttribute()
    {
        return ucwords(str_replace('_', ' ', $this->payment_method));
    }
}