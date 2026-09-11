@extends('layouts.master')
@section('page_title', 'Expenses')
@section('content')
    @include('partials.finance.finance_tabs')

    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header header-elements-inline">
                    <h5 class="card-title"><i class="icon-plus3 mr-2 text-primary"></i>Record Expense</h5>
                    {!! Qs::getPanelOptions() !!}
                </div>
                <div class="card-body">
                    <form method="post" action="{{ route('finance.expenses.store') }}" class="ajax-store">
                        @csrf
                        <div class="form-group">
                            <label>Expense Category</label>
                            <select name="expense_category_id" class="form-control select" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <input type="text" name="description" class="form-control" placeholder="What was this for?" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Amount</label>
                                    <input type="number" step="0.01" min="0.01" name="amount" class="form-control" placeholder="0.00" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Date</label>
                                    <input type="date" name="expense_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Supplier</label>
                            <select name="supplier_id" class="form-control select">
                                <option value="">Select Supplier</option>
                                @foreach($suppliers as $su)
                                    <option value="{{ $su->id }}">{{ $su->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Payee</label>
                            <input type="text" name="payee" class="form-control" placeholder="Who was paid?">
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Payment Method</label>
                                    <select name="payment_method" class="form-control select" required>
                                        <option value="">Select</option>
                                        @foreach($methods as $m)
                                            <option value="{{ $m }}">{{ ucwords(str_replace('_', ' ', $m)) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Reference No.</label>
                                    <input type="text" name="reference_no" class="form-control" placeholder="Optional">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Account</label>
                            <select name="finance_account_id" class="form-control select">
                                <option value="">Select Account</option>
                                @foreach($accounts as $acc)
                                    <option value="{{ $acc->id }}">{{ $acc->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Save Expense <i class="icon-checkmark3 ml-2"></i></button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="card-header header-elements-inline">
                    <h5 class="card-title"><i class="icon-cart5 mr-2 text-primary"></i>Expenses</h5>
                    {!! Qs::getPanelOptions() !!}
                </div>
                <div class="card-body">
                    <div class="alert alert-light border mb-3">
                        <strong>Total Expenses:</strong> <span class="text-danger font-weight-bold">{{ number_format($expenses->where('status', 'completed')->sum('amount'), 2) }}</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table datatable-button-html5-columns">
                            <thead>
                            <tr>
                                <th>S/N</th>
                                <th>Category</th>
                                <th>Description</th>
                                <th>Supplier</th>
                                <th>Account</th>
                                <th>Amount</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($expenses as $ex)
                                <tr class="{{ $ex->status == 'completed' ? '' : 'opacity-50' }}">
                                    <td>{{ $loop->iteration }}</td>
                                    <td class="font-weight-semibold">{{ $ex->category->name ?? 'N/A' }}</td>
                                    <td>{{ $ex->description }}</td>
                                    <td>{{ $ex->supplier->name ?? '---' }}</td>
                                    <td>{{ $ex->account->name ?? '---' }}</td>
                                    <td class="text-danger font-weight-bold">{{ number_format($ex->amount, 2) }}</td>
                                    <td>{{ $ex->expense_date }}</td>
                                    <td>
                                        @if($ex->status == 'completed')
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-danger">Voided</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($ex->status == 'completed')
                                            <form method="post" action="{{ route('finance.expenses.void', $ex->id) }}" onsubmit="return confirm('Void this expense?')">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-danger"><i class="icon-x mr-1"></i>Void</button>
                                            </form>
                                        @else
                                            <span class="text-muted small">Voided</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            @if($expenses->count() < 1)
                                <tr><td colspan="9" class="text-center text-muted">No expenses yet.</td></tr>
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection