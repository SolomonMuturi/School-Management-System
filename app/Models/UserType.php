<?php

namespace App\Models;

use Eloquent;

class UserType extends Eloquent
{
    protected $fillable = ['title', 'name', 'level', 'status'];
}
