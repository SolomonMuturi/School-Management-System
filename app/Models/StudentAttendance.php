<?php

namespace App\Models;

use App\User;
use Eloquent;

class StudentAttendance extends Eloquent
{
    protected $table = 'student_attendance';

    protected $fillable = ['student_id', 'class_id', 'date', 'status', 'note'];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function my_class()
    {
        return $this->belongsTo(MyClass::class, 'class_id');
    }
}