<?php

namespace App\Models;

use Eloquent;

class AcademicYear extends Eloquent
{
    protected $fillable = ['name', 'start_date', 'end_date', 'is_current', 'status'];

    public function terms()
    {
        return $this->belongsToMany(AcademicTerm::class, 'academic_year_term')->withPivot('start_date', 'end_date', 'status');
    }

    public function curriculums()
    {
        return $this->hasMany(Curriculum::class);
    }
}