<?php

namespace App\Models;

use Eloquent;

class RolePermission extends Eloquent
{
    protected $table = 'permissions';

    protected $fillable = ['role', 'module', 'allowed'];
}