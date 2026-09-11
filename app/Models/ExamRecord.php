<?php

namespace App\Models;

use Eloquent;

class ExamRecord extends Eloquent
{
    protected $fillable = ['exam_id', 'my_class_id', 'student_id', 'af', 'ps', 't_comment', 'p_comment', 'year', 'total', 'ave', 'class_ave', 'pos'];
}
