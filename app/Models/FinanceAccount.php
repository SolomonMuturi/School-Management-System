<?php

namespace App\Models;

use Eloquent;

class FinanceAccount extends Eloquent
{
    protected $fillable = [
        'name', 'type', 'account_number', 'bank_name',
        'branch', 'opening_balance', 'current_balance', 'is_active'
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function transactions()
    {
        return $this->hasMany(FinanceTransaction::class, 'finance_account_id');
    }

    public function payments()
    {
        return $this->hasMany(FinancePayment::class, 'finance_account_id');
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class, 'finance_account_id');
    }

    public function getTypeLabelAttribute()
    {
        return ucwords(str_replace('_', ' ', $this->type));
    }
}