<?php

namespace App\Models;

use App\User;
use Eloquent;

class StudentGuardian extends Eloquent
{
    protected $fillable = ['student_id', 'user_id', 'relationship', 'is_primary'];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}