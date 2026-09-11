@extends('layouts.master')
@section('page_title', 'Receipts')
@section('content')
    @include('partials.finance.finance_tabs')

    <div class="row">
        <div class="col-sm-6 col-xl-3 mb-3">
            <div class="dashboard-stat stat-admins">
                <h3>{{ $receipts->count() }}</h3>
                <span class="stat-label">Receipts Issued</span>
                <i class="icon-receipt stat-icon"></i>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3 mb-3">
            <div class="dashboard-stat stat-parents">
                <h3>{{ number_format($receipts->reduce(fn($c, $r) => $c + ($r->payment->amount ?? 0), 0), 2) }}</h3>
                <span class="stat-label">Total Receipted</span>
                <i class="icon-cash3 stat-icon"></i>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header header-elements-inline">
            <h5 class="card-title"><i class="icon-receipt mr-2 text-primary"></i>Receipts</h5>
            {!! Qs::getPanelOptions() !!}
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table datatable-button-html5-columns">
                    <thead>
                    <tr>
                        <th>S/N</th>
                        <th>Receipt No</th>
                        <th>Student</th>
                        <th>Fee Type</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($receipts as $r)
                        @php $active = $r->payment && $r->payment->status === 'completed'; @endphp
                        <tr class="{{ $active ? '' : 'opacity-50' }}">
                            <td>{{ $loop->iteration }}</td>
                            <td class="font-weight-semibold">{{ $r->receipt_no }}</td>
                            <td>{{ optional($r->payment)->student ? $r->payment->student->name : 'N/A' }}</td>
                            <td>{{ optional(optional(optional($r->payment)->studentFee)->feeStructure)->feeType->name ?? 'N/A' }}</td>
                            <td class="text-success font-weight-bold">{{ number_format($r->payment->amount ?? 0, 2) }}</td>
                            <td>{{ optional($r->payment)->method_label ?? 'N/A' }}</td>
                            <td>{{ optional($r->payment)->payment_date }}</td>
                            <td>
                                @if($active)
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-danger">Voided</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex">
                                    <a href="{{ route('finance.receipts.show', $r->id) }}" class="btn btn-sm btn-primary mr-1"><i class="icon-eye mr-1"></i>View</a>
                                    <a href="{{ route('finance.receipts.pdf', $r->id) }}" class="btn btn-sm btn-info"><i class="icon-file-download mr-1"></i>Download</a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    @if($receipts->count() < 1)
                        <tr><td colspan="9" class="text-center text-muted">No receipts yet.</td></tr>
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection