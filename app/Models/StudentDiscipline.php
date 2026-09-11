<?php

namespace App\Models;

use App\User;
use Eloquent;

class StudentDiscipline extends Eloquent
{
    protected $table = 'student_discipline';

    protected $fillable = ['student_id', 'date', 'incident_type', 'description', 'action_taken', 'warning_level', 'notes'];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}