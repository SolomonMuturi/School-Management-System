@extends('layouts.master')
@section('page_title', 'Student Bill - ' . $student->name)
@section('content')
    @include('partials.finance.finance_tabs')

    <div class="card">
        <div class="card-header header-elements-inline">
            <h5 class="card-title"><i class="icon-user mr-2 text-primary"></i>{{ $student->name }} - Bill</h5>
            <a href="{{ route('finance.statement', $student->id) }}" class="btn btn-sm btn-primary"><i class="icon-history"></i> View Statement</a>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-4"><strong>Admission No:</strong> {{ $sr->adm_no ?? 'N/A' }}</div>
                <div class="col-md-4"><strong>Class:</strong> {{ $sr->my_class->name ?? 'N/A' }}</div>
                <div class="col-md-4">
                    <form method="get" action="{{ route('finance.student_bill', $student->id) }}">
                        <select name="term" class="form-control select" onchange="this.form.submit()">
                            @foreach($terms as $term)
                                <option value="{{ $term }}" {{ $selected == $term ? 'selected' : '' }}>{{ $term }}</option>
                            @endforeach
                        </select>
                    </form>
                </div>
            </div>

            <div class="alert alert-info">
                <strong>Record Payment:</strong> Select a fee below and enter the amount to be paid.
            </div>

            <form method="post" action="{{ route('finance.payments.store') }}">
                @csrf
                <input type="hidden" name="student_id" value="{{ $student->id }}">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Fee</label>
                            <select name="student_fee_id" class="form-control select" required>
                                <option value="">Select Fee</option>
                                @foreach($fees->where('balance', '>', 0) as $fee)
                                    <option value="{{ $fee->id }}">
                                        {{ optional(optional($fee->feeStructure)->feeType)->name ?? 'N/A' }} - Balance: {{ number_format($fee->balance, 2) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Amount</label>
                            <input type="number" step="0.01" min="0.01" name="amount" class="form-control" placeholder="0.00" required>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Method</label>
                            <select name="payment_method" class="form-control select" required>
                                <option value="cash">Cash</option>
                                <option value="bank">Bank</option>
                                <option value="mobile_money">Mobile Money</option>
                                <option value="card">Card</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Date</label>
                            <input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Reference No.</label>
                            <input type="text" name="reference_no" class="form-control" placeholder="Optional">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Account</label>
                            <select name="finance_account_id" class="form-control select">
                                <option value="">Select Account</option>
                                @foreach(\App\Models\FinanceAccount::where('is_active', true)->get() as $acc)
                                    <option value="{{ $acc->id }}">{{ $acc->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Notes</label>
                            <input type="text" name="notes" class="form-control" placeholder="Optional notes">
                        </div>
                    </div>
                    <div class="col-md-3 mt-4">
                        <button type="submit" class="btn btn-success btn-block"><i class="icon-cash3 mr-1"></i> Receive Payment</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header header-elements-inline">
            <h5 class="card-title"><i class="icon-list mr-2 text-primary"></i>Fees ({{ $selected }})</h5>
            {!! Qs::getPanelOptions() !!}
        </div>
        <div class="card-body">
            <table class="table datatable-button-html5-columns">
                <thead>
                <tr>
                    <th>S/N</th>
                    <th>Fee Type</th>
                    <th>Amount Due</th>
                    <th>Discount</th>
                    <th>Paid</th>
                    <th>Balance</th>
                    <th>Status</th>
                </tr>
                </thead>
                <tbody>
                @foreach($fees as $fee)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td class="font-weight-semibold">{{ optional(optional($fee->feeStructure)->feeType)->name ?? 'N/A' }}</td>
                        <td>{{ number_format($fee->amount_due, 2) }}</td>
                        <td>{{ number_format($fee->discount, 2) }}</td>
                        <td class="text-success">{{ number_format($fee->amount_paid, 2) }}</td>
                        <td class="text-danger font-weight-bold">{{ number_format($fee->balance, 2) }}</td>
                        <td>
                            @if($fee->status == 'paid')
                                <span class="badge badge-success">Paid</span>
                            @elseif($fee->status == 'partial')
                                <span class="badge badge-warning">Partial</span>
                            @elseif($fee->status == 'overdue')
                                <span class="badge badge-danger">Overdue</span>
                            @else
                                <span class="badge badge-secondary">Unpaid</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
                @if($fees->count() < 1)
                    <tr><td colspan="7" class="text-center text-muted">No fees for this term. Generate billing.</td></tr>
                @endif
                </tbody>
            </table>
        </div>
    </div>
@endsection