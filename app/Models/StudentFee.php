<?php

namespace App\Models;

use App\User;
use Eloquent;

class StudentFee extends Eloquent
{
    protected $fillable = [
        'student_id', 'fee_structure_id', 'amount_due', 'discount',
        'amount_paid', 'balance', 'due_date', 'status', 'session', 'term', 'year'
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function feeStructure()
    {
        return $this->belongsTo(FeeStructure::class);
    }

    public function discounts()
    {
        return $this->hasMany(Discount::class);
    }

    public function payments()
    {
        return $this->hasMany(FinancePayment::class);
    }

    public function getFeeTypeNameAttribute()
    {
        return $this->feeStructure && $this->feeStructure->feeType
            ? $this->feeStructure->feeType->name
            : 'N/A';
    }
}