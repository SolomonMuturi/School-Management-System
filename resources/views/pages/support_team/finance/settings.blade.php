@extends('layouts.master')
@section('page_title', 'Finance Settings')
@section('content')
    @include('partials.finance.finance_tabs')

    <div class="row">
        <div class="col-sm-6 col-xl-3 mb-3">
            <div class="dashboard-stat stat-teachers">
                <h3>{{ $fee_types->count() }}</h3>
                <span class="stat-label">Fee Types</span>
                <i class="icon-price-tag2 stat-icon"></i>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3 mb-3">
            <div class="dashboard-stat stat-students">
                <h3>{{ $expense_categories->count() }}</h3>
                <span class="stat-label">Expense Categories</span>
                <i class="icon-cart5 stat-icon"></i>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3 mb-3">
            <div class="dashboard-stat stat-parents">
                <h3>{{ $accounts->count() }}</h3>
                <span class="stat-label">Accounts</span>
                <i class="icon-bank stat-icon"></i>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3 mb-3">
            <div class="dashboard-stat stat-admins">
                <h3>{{ number_format($accounts->sum('current_balance'), 2) }}</h3>
                <span class="stat-label">Total Account Balance</span>
                <i class="icon-coins stat-icon"></i>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-5">
            <div class="card">
                <div class="card-header header-elements-inline">
                    <h5 class="card-title"><i class="icon-price-tag2 mr-2 text-primary"></i>Fee Types</h5>
                    {!! Qs::getPanelOptions() !!}
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                            <tr><th>#</th><th>Name</th><th>Status</th></tr>
                            </thead>
                            <tbody>
                            @foreach($fee_types as $i => $ft)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $ft->name }}</td>
                                    <td>
                                        @if($ft->is_active)
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-secondary">Disabled</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            @if($fee_types->count() < 1)
                                <tr><td colspan="3" class="text-center text-muted">No fee types.</td></tr>
                            @endif
                            </tbody>
                        </table>
                    </div>
                    <a href="{{ route('finance.fee_types') }}" class="btn btn-sm btn-light"><i class="icon-gear mr-1"></i> Manage</a>
                </div>
            </div>
        </div>

        <div class="col-md-7">
            <div class="card">
                <div class="card-header header-elements-inline">
                    <h5 class="card-title"><i class="icon-cart5 mr-2 text-primary"></i>Expense Categories</h5>
                    {!! Qs::getPanelOptions() !!}
                </div>
                <div class="card-body">
                    <form method="post" action="{{ route('finance.expense_categories.store') }}" class="mb-3 ajax-store">
                        @csrf
                        <div class="input-group">
                            <input type="text" name="name" class="form-control" placeholder="New category name" required>
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-primary"><i class="icon-plus3 mr-1"></i>Add</button>
                            </div>
                        </div>
                    </form>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                            <tr><th>#</th><th>Name</th><th>Expenses</th></tr>
                            </thead>
                            <tbody>
                            @foreach($expense_categories as $c)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $c->name }}</td>
                                    <td>{{ $c->expenses->count() }}</td>
                                </tr>
                            @endforeach
                            @if($expense_categories->count() < 1)
                                <tr><td colspan="3" class="text-center text-muted">No categories.</td></tr>
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header header-elements-inline">
                    <h5 class="card-title"><i class="icon-bank mr-2 text-primary"></i>Accounts Snapshot</h5>
                    <a href="{{ route('finance.accounts') }}" class="btn btn-sm btn-primary"><i class="icon-gear mr-1"></i> Manage</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                            <tr><th>Account</th><th>Type</th><th>Balance</th></tr>
                            </thead>
                            <tbody>
                            @foreach($accounts as $acc)
                                <tr>
                                    <td class="font-weight-semibold">{{ $acc->name }}</td>
                                    <td>{{ $acc->type_label }}</td>
                                    <td class="font-weight-bold text-primary">{{ number_format($acc->current_balance, 2) }}</td>
                                </tr>
                            @endforeach
                            @if($accounts->count() < 1)
                                <tr><td colspan="3" class="text-center text-muted">No accounts.</td></tr>
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            @if(Qs::userIsTeamSA())
            <div class="card">
                <div class="card-header header-elements-inline">
                    <h5 class="card-title"><i class="icon-phone mr-2 text-success"></i>M-Pesa (Daraja) Settings</h5>
                    {!! Qs::getPanelOptions() !!}
                </div>
                <div class="card-body">
                    <form method="post" action="{{ route('finance.settings.mpesa_store') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Environment</label>
                                    <select name="environment" class="form-control select">
                                        <option value="sandbox" {{ ($mpesa['environment'] ?? 'sandbox') === 'sandbox' ? 'selected' : '' }}>Sandbox (test)</option>
                                        <option value="live" {{ ($mpesa['environment'] ?? '') === 'live' ? 'selected' : '' }}>Live</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Shortcode / Paybill (6 digits)</label>
                                    <input type="text" name="shortcode" class="form-control" value="{{ $mpesa['shortcode'] ?? '' }}" placeholder="174379">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Consumer Key</label>
                                    <input type="text" name="consumer_key" class="form-control" value="{{ $mpesa['consumer_key'] ?? '' }}" autocomplete="off">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Consumer Secret</label>
                                    <input type="text" name="consumer_secret" class="form-control" value="{{ $mpesa['consumer_secret'] ?? '' }}" autocomplete="off">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Passkey</label>
                                    <input type="text" name="passkey" class="form-control" value="{{ $mpesa['passkey'] ?? '' }}" autocomplete="off">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Transaction Type</label>
                                    <select name="transaction_type" class="form-control select">
                                        <option value="CustomerPayBillOnline" {{ ($mpesa['transaction_type'] ?? '') === 'CustomerPayBillOnline' ? 'selected' : '' }}>CustomerPayBillOnline</option>
                                        <option value="CustomerBuyGoodsOnline" {{ ($mpesa['transaction_type'] ?? '') === 'CustomerBuyGoodsOnline' ? 'selected' : '' }}>CustomerBuyGoodsOnline</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Callback URL</label>
                            <input type="text" name="callback_url" class="form-control" value="{{ $mpesa['callback_url'] ?? url('/finance/mpesa/callback') }}">
                            <small class="form-text text-muted">Must be publicly reachable (e.g. ngrok) for live webhooks from Safaricom. Leave blank to use <code>{{ url('/finance/mpesa/callback') }}</code>.</small>
                        </div>
                        <div class="form-group">
                            <label>Account Reference</label>
                            <input type="text" name="account_reference" class="form-control" value="{{ $mpesa['account_reference'] ?? 'FEE' }}" maxlength="12">
                        </div>
                        <button type="submit" class="btn btn-success"><i class="icon-save mr-1"></i>Save M-Pesa Settings</button>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>
@endsection