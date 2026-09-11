<?php

namespace App\Models;

use App\User;
use Eloquent;

class AssignmentSubmission extends Eloquent
{
    protected $fillable = ['assignment_id', 'student_id', 'submission_text', 'attachment_path', 'status', 'submitted_at', 'marks', 'feedback'];

    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}