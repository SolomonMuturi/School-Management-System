<?php

namespace App\Models;

use Eloquent;

class FeeType extends Eloquent
{
    protected $fillable = ['name', 'description', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function feeStructures()
    {
        return $this->hasMany(FeeStructure::class);
    }
}