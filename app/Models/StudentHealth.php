<?php

namespace App\Models;

use App\User;
use Eloquent;

class StudentHealth extends Eloquent
{
    protected $table = 'student_health';

    protected $fillable = [
        'student_id', 'allergies', 'medical_conditions', 'medications',
        'emergency_contact_name', 'emergency_contact_phone', 'emergency_contact_relationship', 'health_notes'
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}