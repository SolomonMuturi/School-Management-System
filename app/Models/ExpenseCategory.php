<?php

namespace App\Models;

use Eloquent;

class ExpenseCategory extends Eloquent
{
    protected $fillable = ['name', 'description'];

    public function expenses()
    {
        return $this->hasMany(Expense::class, 'expense_category_id');
    }
}