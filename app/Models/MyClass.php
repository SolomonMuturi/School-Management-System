<?php

namespace App\Models;

use App\User;
use Eloquent;

class MyClass extends Eloquent
{
    protected $fillable = ['name', 'code', 'class_type_id', 'teacher_id', 'status'];

    public function class_type()
    {
        return $this->belongsTo(ClassType::class);
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function student_record()
    {
        return $this->hasMany(StudentRecord::class);
    }
}
