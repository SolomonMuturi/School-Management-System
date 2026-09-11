<?php

namespace App\Models;

use App\User;
use Eloquent;

class StudentDocument extends Eloquent
{
    protected $fillable = ['student_id', 'title', 'doc_type', 'file_path', 'notes'];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}