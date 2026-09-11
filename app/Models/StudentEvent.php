<?php

namespace App\Models;

use App\User;
use Eloquent;

class StudentEvent extends Eloquent
{
    protected $fillable = ['student_id', 'event_type', 'description', 'meta', 'recorded_by'];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function recorder()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}