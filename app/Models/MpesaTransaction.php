<?php

namespace App\Models;

use App\User;
use Eloquent;

class MpesaTransaction extends Eloquent
{
    protected $fillable = [
        'phone', 'amount', 'reference', 'description', 'student_id', 'student_fee_id',
        'checkout_request_id', 'merchant_request_id', 'mpesa_receipt_number',
        'result_code', 'result_desc', 'status', 'finance_payment_id', 'raw_callback', 'completed_at'
    ];

    protected $casts = [
        'amount' => 'float',
        'completed_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function studentFee()
    {
        return $this->belongsTo(StudentFee::class);
    }

    public function payment()
    {
        return $this->belongsTo(FinancePayment::class, 'finance_payment_id');
    }

    public function getStatusLabelAttribute()
    {
        return ucfirst($this->status);
    }
}