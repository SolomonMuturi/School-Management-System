<?php

namespace App\Models;

use Eloquent;

class Exam extends Eloquent
{
    protected $fillable = ['name', 'type', 'start_date', 'end_date', 'status', 'term', 'year'];
}
