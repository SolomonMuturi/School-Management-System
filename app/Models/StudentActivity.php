<?php

namespace App\Models;

use App\User;
use Eloquent;

class StudentActivity extends Eloquent
{
    protected $fillable = ['student_id', 'activity_type', 'activity_name', 'role', 'session', 'achievements', 'notes'];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}