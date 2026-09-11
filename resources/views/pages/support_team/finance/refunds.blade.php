@extends('layouts.master')
@section('page_title', 'Refunds')
@section('content')
    @include('partials.finance.finance_tabs')

    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header header-elements-inline">
                    <h5 class="card-title"><i class="icon-undo2 mr-2 text-primary"></i>Issue Refund</h5>
                    {!! Qs::getPanelOptions() !!}
                </div>
                <div class="card-body">
                    <form method="post" action="{{ route('finance.refunds.store') }}" class="ajax-store">
                        @csrf
                        <div class="form-group">
                            <label>Student</label>
                            <select name="student_id" class="form-control select" required>
                                <option value="">Select Student</option>
                                @foreach($students as $s)
                                    <option value="{{ $s->id }}">{{ $s->admission->adm_no ?? '' }} - {{ $s->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Payment (Receipt to refund)</label>
                            <select name="finance_payment_id" class="form-control select" required>
                                <option value="">Select Payment</option>
                                @foreach($payments as $p)
                                    <option value="{{ $p->id }}">{{ $p->receipt->receipt_no ?? '---' }} - {{ $p->student->name ?? 'N/A' }} ({{ number_format($p->amount, 2) }})</option>
                                @endforeach
                            </select>
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
                                    <input type="date" name="refund_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Reason</label>
                            <textarea name="reason" class="form-control" rows="2" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Issue Refund <i class="icon-checkmark3 ml-2"></i></button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="card-header header-elements-inline">
                    <h5 class="card-title"><i class="icon-undo2 mr-2 text-primary"></i>Refunds</h5>
                    {!! Qs::getPanelOptions() !!}
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table datatable-button-html5-columns">
                            <thead>
                            <tr>
                                <th>S/N</th>
                                <th>Student</th>
                                <th>Receipt No</th>
                                <th>Amount</th>
                                <th>Date</th>
                                <th>Reason</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($refunds as $rf)
                                <tr class="{{ $rf->status == 'completed' ? '' : 'opacity-50' }}">
                                    <td>{{ $loop->iteration }}</td>
                                    <td class="font-weight-semibold">{{ $rf->student->name ?? 'N/A' }}</td>
                                    <td>{{ optional(optional($rf->payment)->receipt)->receipt_no ?? '---' }}</td>
                                    <td class="text-danger font-weight-bold">{{ number_format($rf->amount, 2) }}</td>
                                    <td>{{ $rf->refund_date }}</td>
                                    <td>{{ $rf->reason }}</td>
                                    <td>
                                        @if($rf->status == 'completed')
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-danger">Voided</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($rf->status == 'completed')
                                            <form method="post" action="{{ route('finance.refunds.void', $rf->id) }}" onsubmit="return confirm('Void this refund?')">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-danger"><i class="icon-x mr-1"></i>Void</button>
                                            </form>
                                        @else
                                            <span class="text-muted small">Voided</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            @if($refunds->count() < 1)
                                <tr><td colspan="8" class="text-center text-muted">No refunds yet.</td></tr>
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection