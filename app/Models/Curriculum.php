<?php

namespace App\Models;

use Eloquent;

class Curriculum extends Eloquent
{
    protected $table = 'curriculums';

    protected $fillable = ['name', 'program', 'academic_year_id', 'academic_term_id', 'status', 'description'];

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    public function academicTerm()
    {
        return $this->belongsTo(AcademicTerm::class, 'academic_term_id');
    }

    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'curriculum_subject')->withPivot('my_class_id');
    }

    public function topics()
    {
        return $this->hasMany(CurriculumTopic::class);
    }
}