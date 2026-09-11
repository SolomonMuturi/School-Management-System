@extends('layouts.master')
@section('page_title', 'Statement - ' . $student->name)
@section('content')
    @include('partials.finance.finance_tabs')

    <div class="card">
        <div class="card-header header-elements-inline">
            <h5 class="card-title"><i class="icon-user mr-2 text-primary"></i>{{ $student->name }} - Statement</h5>
            <div>
                <form method="get" action="{{ route('finance.statement', $student->id) }}" class="d-inline-flex">
                    <select name="term" class="form-control form-control-sm select mr-2">
                        @foreach($terms as $t)
                            <option value="{{ $t }}" {{ $selected == $t ? 'selected' : '' }}>{{ $t }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-sm btn-light mr-2">Go</button>
                </form>
                <a href="{{ route('finance.statement.pdf', $student->id) }}?term={{ $selected }}" class="btn btn-sm btn-info mr-1"><i class="icon-file-download mr-1"></i> Download PDF</a>
            </div>
        </div>
        <div class="card-body" id="print-area">
            <div class="row mb-3">
                <div class="col-md-4"><strong>Adm No:</strong> {{ $sr->adm_no ?? 'N/A' }}</div>
                <div class="col-md-4"><strong>Class:</strong> {{ $sr->my_class->name ?? 'N/A' }}</div>
                <div class="col-md-4"><strong>&nbsp;</strong> Session: {{ $year }}</div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th colspan="6" class="text-center font-weight-bold">Fee Summary ({{ $year }}) - {{ $selected}}</th>
                    </tr>
                    <tr>
                        <th>Term</th>
                        <th>Fee Type</th>
                        <th>Invoiced</th>
                        <th>Discount</th>
                        <th>Paid</th>
                        <th>Balance</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($fees as $f)
                        <tr>
                            <td>{{ optional($f->feeStructure)->term }}</td>
                            <td>{{ optional(optional($f->feeStructure)->feeType)->name ?? 'N/A' }}</td>
                            <td>{{ number_format($f->amount_due, 2) }}</td>
                            <td>{{ number_format($f->discount, 2) }}</td>
                            <td class="text-success">{{ number_format($f->amount_paid, 2) }}</td>
                            <td class="text-danger font-weight-bold">{{ number_format(max(0, $f->amount_due - $f->discount - $f->amount_paid), 2) }}</td>
                        </tr>
                    @endforeach
                    @if($fees->count() < 1)
                        <tr><td colspan="6" class="text-center text-muted">No invoices for this session.</td></tr>
                    @endif
                    <tr class="table-light font-weight-bold">
                        <td colspan="2" class="text-right">TOTALS:</td>
                        <td>{{ number_format($fees->sum('amount_due'), 2) }}</td>
                        <td>{{ number_format($fees->sum('discount'), 2) }}</td>
                        <td>{{ number_format($fees->sum('amount_paid'), 2) }}</td>
                        <td>{{ number_format($fees->sum(function($f){ return max(0, $f->amount_due - $f->discount - $f->amount_paid); }), 2) }}</td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                <h6 class="font-weight-bold text-uppercase">Payments</h6>
                <table class="table table-striped table-bordered">
                    <thead>
                    <tr><th>Date</th><th>Receipt No</th><th>Fee Type</th><th>Method</th><th>Amount</th><th>Status</th></tr>
                    </thead>
                    <tbody>
                    @foreach($payments as $p)
                        <tr class="{{ $p->is_active ? '' : 'opacity-50' }}">
                            <td>{{ $p->payment_date }}</td>
                            <td>{{ $p->receipt->receipt_no ?? '---' }}</td>
                            <td>{{ optional(optional($p->studentFee)->feeStructure)->feeType->name ?? 'N/A' }}</td>
                            <td>{{ $p->method_label }}</td>
                            <td class="text-success">{{ number_format($p->amount, 2) }}</td>
                            <td>{{ $p->is_active ? 'Active' : 'Voided' }}</td>
                        </tr>
                    @endforeach
                    @if($payments->count() < 1)
                        <tr><td colspan="6" class="text-center text-muted">No payments.</td></tr>
                    @endif
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                <h6 class="font-weight-bold text-uppercase">Refunds</h6>
                <table class="table table-striped table-bordered">
                    <thead>
                    <tr><th>Date</th><th>Reason</th><th>Amount</th><th>Status</th></tr>
                    </thead>
                    <tbody>
                    @foreach($refunds as $rf)
                        <tr class="{{ $rf->status == 'completed' ? '' : 'opacity-50' }}">
                            <td>{{ $rf->refund_date }}</td>
                            <td>{{ $rf->reason }}</td>
                            <td class="text-danger">{{ number_format($rf->amount, 2) }}</td>
                            <td>{{ $rf->status == 'completed' ? 'Active' : 'Voided' }}</td>
                        </tr>
                    @endforeach
                    @if($refunds->count() < 1)
                        <tr><td colspan="4" class="text-center text-muted">No refunds.</td></tr>
                    @endif
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                <h6 class="font-weight-bold text-uppercase">Discounts</h6>
                <table class="table table-striped table-bordered">
                    <thead>
                    <tr><th>Date</th><th>Fee Type</th><th>Type</th><th>Amount</th><th>Status</th></tr>
                    </thead>
                    <tbody>
                    @foreach($discounts as $d)
                        <tr class="{{ $d->status == 'active' ? '' : 'opacity-50' }}">
                            <td>{{ $d->created_at ? $d->created_at->format('Y-m-d') : '-' }}</td>
                            <td>{{ optional(optional($d->studentFee)->feeStructure)->feeType->name ?? 'N/A' }}</td>
                            <td>{{ ucwords($d->type) }}</td>
                            <td class="text-warning font-weight-semibold">
                                {{ number_format($d->amount, 2) }}
                            </td>
                            <td>{{ $d->status == 'active' ? 'Active' : 'Revoked' }}</td>
                        </tr>
                    @endforeach
                    @if($discounts->count() < 1)
                        <tr><td colspan="5" class="text-center text-muted">No discounts.</td></tr>
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection