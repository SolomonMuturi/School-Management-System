@extends('layouts.master')
@section('page_title', 'Discounts')
@section('content')
    @include('partials.finance.finance_tabs')

    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header header-elements-inline">
                    <h5 class="card-title"><i class="icon-percent mr-2 text-primary"></i>Add Discount</h5>
                    {!! Qs::getPanelOptions() !!}
                </div>
                <div class="card-body">
                    <form method="post" action="{{ route('finance.discounts.store') }}" class="ajax-store">
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
                            <label>Fee</label>
                            <select name="student_fee_id" class="form-control select" required>
                                <option value="">Select Fee</option>
                                @foreach($student_fees as $f)
                                    <option value="{{ $f->id }}">
                                        {{ $f->student->name ?? 'N/A' }} - {{ optional(optional($f->feeStructure)->feeType)->name ?? 'N/A' }} (Balance: {{ number_format($f->balance, 2) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Type</label>
                                    <select name="type" class="form-control select">
                                        <option value="discount">Discount</option>
                                        <option value="scholarship">Scholarship</option>
                                        <option value="waiver">Waiver</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Amount</label>
                                    <input type="number" step="0.01" min="0.01" name="amount" class="form-control" placeholder="0.00" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Reason</label>
                            <input type="text" name="reason" class="form-control" placeholder="Optional">
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Add Discount <i class="icon-checkmark3 ml-2"></i></button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="card-header header-elements-inline">
                    <h5 class="card-title"><i class="icon-percent mr-2 text-primary"></i>Discounts</h5>
                    {!! Qs::getPanelOptions() !!}
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table datatable-button-html5-columns">
                            <thead>
                            <tr>
                                <th>S/N</th>
                                <th>Student</th>
                                <th>Fee Type</th>
                                <th>Type</th>
                                <th>Value</th>
                                <th>Reason</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($discounts as $d)
                                <tr class="{{ $d->status == 'active' ? '' : 'opacity-50' }}">
                                    <td>{{ $loop->iteration }}</td>
                                    <td class="font-weight-semibold">{{ $d->student->name ?? 'N/A' }}</td>
                                    <td>{{ optional(optional($d->studentFee)->feeStructure)->feeType->name ?? 'N/A' }}</td>
                                    <td>{{ ucwords($d->type) }}</td>
                                    <td class="font-weight-semibold text-warning">{{ number_format($d->amount, 2) }}</td>
                                    <td>{{ $d->reason }}</td>
                                    <td>
                                        @if($d->status == 'active')
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-danger">Revoked</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($d->status == 'active')
                                            <form method="post" action="{{ route('finance.discounts.revoke', $d->id) }}" onsubmit="return confirm('Revoke this discount?')">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-danger"><i class="icon-x mr-1"></i>Revoke</button>
                                            </form>
                                        @else
                                            <span class="text-muted small">Revoked</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            @if($discounts->count() < 1)
                                <tr><td colspan="8" class="text-center text-muted">No discounts yet.</td></tr>
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection