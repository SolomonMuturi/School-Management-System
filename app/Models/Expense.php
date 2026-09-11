<?php

namespace App\Models;

use App\User;
use Eloquent;

class Expense extends Eloquent
{
    protected $fillable = [
        'expense_category_id', 'supplier_id', 'finance_account_id', 'description',
        'amount', 'expense_date', 'payee', 'payment_method', 'reference_no',
        'receipt_document', 'status', 'recorded_by', 'session', 'term', 'year'
    ];

    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function account()
    {
        return $this->belongsTo(FinanceAccount::class, 'finance_account_id');
    }

    public function recorder()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function getMethodLabelAttribute()
    {
        return ucwords(str_replace('_', ' ', $this->payment_method));
    }
}