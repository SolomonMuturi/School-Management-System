<?php

namespace App\Models;

use Eloquent;

class FinanceTransaction extends Eloquent
{
    protected $fillable = [
        'finance_account_id', 'type', 'amount', 'description',
        'transaction_date', 'reference_no', 'related_to', 'session', 'year'
    ];

    public function account()
    {
        return $this->belongsTo(FinanceAccount::class, 'finance_account_id');
    }
}