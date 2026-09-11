<?php

namespace App\Models;

use App\User;
use Eloquent;

class Discount extends Eloquent
{
    protected $fillable = [
        'student_id', 'student_fee_id', 'type', 'amount',
        'reason', 'approved_by', 'status', 'session', 'term', 'year'
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function studentFee()
    {
        return $this->belongsTo(StudentFee::class);
    }
}