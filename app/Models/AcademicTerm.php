<?php

namespace App\Models;

use Eloquent;

class AcademicTerm extends Eloquent
{
    protected $fillable = ['name', 'abbr', 'sequence', 'is_current'];

    public function years()
    {
        return $this->belongsToMany(AcademicYear::class, 'academic_year_term')->withPivot('start_date', 'end_date', 'status');
    }
}