@extends('layouts.master')
@section('page_title', 'Cash & Bank')
@section('content')
    @include('partials.finance.finance_tabs')

    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header header-elements-inline">
                    <h5 class="card-title"><i class="icon-plus3 mr-2 text-primary"></i>Add Account</h5>
                    {!! Qs::getPanelOptions() !!}
                </div>
                <div class="card-body">
                    <form method="post" action="{{ route('finance.accounts.store') }}" class="ajax-store">
                        @csrf
                        <div class="form-group">
                            <label>Account Name</label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. Zenith Main Account" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Type</label>
                                    <select name="type" class="form-control select">
                                        <option value="cash">Cash</option>
                                        <option value="bank">Bank</option>
                                        <option value="mobile_money">Mobile Money</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Opening Balance</label>
                                    <input type="number" step="0.01" name="opening_balance" class="form-control" value="0.00">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Account Number</label>
                            <input type="text" name="account_number" class="form-control" placeholder="Optional">
                        </div>
                        <div class="form-group">
                            <label class="form-check-label">
                                <input type="checkbox" name="is_active" class="form-check-input-styled" checked data-fouc>
                                Active
                            </label>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Add Account <i class="icon-checkmark3 ml-2"></i></button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="card-header header-elements-inline">
                    <h5 class="card-title"><i class="icon-bank mr-2 text-primary"></i>Accounts</h5>
                    {!! Qs::getPanelOptions() !!}
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($accounts as $acc)
                            <div class="col-sm-6 col-xl-4 mb-3">
                                <div class="card {{ $acc->is_active ? '' : 'opacity-50' }}">
                                    <div class="card-body">
                                        <div class="media">
                                            <div class="media-body">
                                                <h3 class="mb-0 font-weight-semibold text-primary">{{ number_format($acc->current_balance, 2) }}</h3>
                                                <span class="text-uppercase font-size-xs text-muted">{{ $acc->name }}</span><br>
                                                <span class="font-size-sm text-muted">{{ $acc->type_label }}</span>
                                            </div>
                                            <div class="ml-3 align-self-center">
                                                <button data-toggle="modal" data-target="#editAccount{{ $acc->id }}" class="btn btn-outline-primary btn-sm"><i class="icon-pencil"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="modal fade" id="editAccount{{ $acc->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Account: {{ $acc->name }}</h5>
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        </div>
                                        <form method="post" action="{{ route('finance.accounts.update', $acc->id) }}" class="ajax-update">
                                            @csrf @method('PUT')
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label>Account Name</label>
                                                    <input type="text" name="name" class="form-control" value="{{ $acc->name }}" required>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Type</label>
                                                            <select name="type" class="form-control select">
                                                                <option value="cash" {{ $acc->type == 'cash' ? 'selected' : '' }}>Cash</option>
                                                                <option value="bank" {{ $acc->type == 'bank' ? 'selected' : '' }}>Bank</option>
                                                                <option value="mobile_money" {{ $acc->type == 'mobile_money' ? 'selected' : '' }}>Mobile Money</option>
                                                                <option value="other" {{ $acc->type == 'other' ? 'selected' : '' }}>Other</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Opening Balance</label>
                                                            <input type="number" step="0.01" name="opening_balance" class="form-control" value="{{ $acc->opening_balance }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label>Account Number</label>
                                                    <input type="text" name="account_number" class="form-control" value="{{ $acc->account_number }}">
                                                </div>
                                                <div class="form-group">
                                                    <label class="form-check-label">
                                                        <input type="checkbox" name="is_active" class="form-check-input-styled" {{ $acc->is_active ? 'checked' : '' }} data-fouc>
                                                        Active
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-primary">Save</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        @if($accounts->count() < 1)
                            <div class="col-12 text-center text-muted">No accounts yet.</div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header header-elements-inline">
                    <h5 class="card-title"><i class="icon-history mr-2 text-primary"></i>Recent Transactions</h5>
                    {!! Qs::getPanelOptions() !!}
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>Date</th>
                                <th>Description</th>
                                <th>Account</th>
                                <th>Type</th>
                                <th>Amount</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($transactions as $t)
                                <tr>
                                    <td>{{ $t->created_at ? $t->created_at->format('Y-m-d H:i') : '-' }}</td>
                                    <td>{{ $t->description }}</td>
                                    <td>{{ $t->account->name ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge {{ $t->type == 'credit' ? 'badge-success' : 'badge-danger' }}">{{ ucfirst($t->type) }}</span>
                                    </td>
                                    <td class="font-weight-semibold {{ $t->type == 'credit' ? 'text-success' : 'text-danger' }}">
                                        {{ ($t->type == 'credit' ? '+' : '-') . number_format($t->amount, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                            @if($transactions->count() < 1)
                                <tr><td colspan="5" class="text-center text-muted">No transactions yet.</td></tr>
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection