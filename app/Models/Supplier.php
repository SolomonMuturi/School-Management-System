<?php

namespace App\Models;

use Eloquent;

class Supplier extends Eloquent
{
    protected $fillable = [
        'name', 'contact_person', 'phone', 'email',
        'address', 'total_owed', 'is_active'
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }
}