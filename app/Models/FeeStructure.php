<?php

namespace App\Models;

use Eloquent;

class FeeStructure extends Eloquent
{
    protected $fillable = [
        'fee_type_id', 'my_class_id', 'session', 'term',
        'amount', 'is_active', 'description'
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function feeType()
    {
        return $this->belongsTo(FeeType::class);
    }

    public function myClass()
    {
        return $this->belongsTo(MyClass::class, 'my_class_id');
    }

    public function studentFees()
    {
        return $this->hasMany(StudentFee::class);
    }
}