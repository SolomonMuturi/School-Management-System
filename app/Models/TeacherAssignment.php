<?php

namespace App\Models;

use App\User;
use Eloquent;

class TeacherAssignment extends Eloquent
{
    protected $fillable = ['teacher_id', 'subject_id', 'my_class_id', 'academic_year_id', 'academic_term_id', 'status'];

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function myClass()
    {
        return $this->belongsTo(MyClass::class, 'my_class_id');
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    public function academicTerm()
    {
        return $this->belongsTo(AcademicTerm::class, 'academic_term_id');
    }
}