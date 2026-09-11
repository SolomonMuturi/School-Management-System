@extends('layouts.master')
@section('page_title', 'Student Fees / Billing')
@section('content')
    @include('partials.finance.finance_tabs')

    <div class="card">
        <div class="card-header header-elements-inline">
            <h5 class="card-title"><i class="icon-file-text3 mr-2 text-primary"></i>Generate Billing</h5>
            {!! Qs::getPanelOptions() !!}
        </div>
        <div class="card-body">
            <form method="post" action="{{ route('finance.billing.generate') }}">
                @csrf
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Class</label>
                            <select name="my_class_id" class="form-control select" required>
                                <option value="">Select Class</option>
                                @foreach($my_classes as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Term</label>
                            <select name="term" class="form-control select" required>
                                @foreach($terms as $term)
                                    <option value="{{ $term }}">{{ $term }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Due Date</label>
                            <input type="date" name="due_date" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-3 mt-4">
                        <button type="submit" class="btn btn-primary btn-block"><i class="icon-magic-wand mr-1"></i> Generate</button>
                    </div>
                </div>
                <small class="text-muted">Generates billable fees for all active students in the selected class, based on active fee structures.</small>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header header-elements-inline">
            <h5 class="card-title"><i class="icon-file-text2 mr-2 text-primary"></i>Billing Records</h5>
            {!! Qs::getPanelOptions() !!}
        </div>
        <div class="card-body">
            <form method="get" action="{{ route('finance.billing') }}" class="mb-3">
                <div class="row">
                    <div class="col-md-3">
                        <select name="term" class="form-control select">
                            @foreach($terms as $term)
                                <option value="{{ $term }}" {{ $selected == $term ? 'selected' : '' }}>{{ $term }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="my_class_id" class="form-control select">
                            <option value="">All Classes</option>
                            @foreach($my_classes as $c)
                                <option value="{{ $c->id }}" {{ $selected_class == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-control select">
                            <option value="">All Status</option>
                            <option value="unpaid" {{ $selected_status == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                            <option value="partial" {{ $selected_status == 'partial' ? 'selected' : '' }}>Partial</option>
                            <option value="paid" {{ $selected_status == 'paid' ? 'selected' : '' }}>Paid</option>
                            <option value="overdue" {{ $selected_status == 'overdue' ? 'selected' : '' }}>Overdue</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary btn-block">Filter</button>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table datatable-button-html5-columns">
                    <thead>
                    <tr>
                        <th>S/N</th>
                        <th>Student</th>
                        <th>Fee Type</th>
                        <th>Amount Due</th>
                        <th>Discount</th>
                        <th>Paid</th>
                        <th>Balance</th>
                        <th>Due Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($fees as $fee)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td class="font-weight-semibold">{{ $fee->student->name ?? 'N/A' }}</td>
                            <td>{{ optional(optional($fee->feeStructure)->feeType)->name ?? 'N/A' }}</td>
                            <td>{{ number_format($fee->amount_due, 2) }}</td>
                            <td>{{ number_format($fee->discount, 2) }}</td>
                            <td class="text-success font-weight-semibold">{{ number_format($fee->amount_paid, 2) }}</td>
                            <td class="text-danger font-weight-semibold">{{ number_format($fee->balance, 2) }}</td>
                            <td>{{ $fee->due_date }}</td>
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
                            <td>
                                <div class="d-flex">
                                    <a href="{{ route('finance.student_bill', $fee->student_id) }}" class="btn btn-sm btn-primary mr-1"><i class="icon-file-text2"></i> Bill</a>
                                    <a href="{{ route('finance.statement', $fee->student_id) }}" class="btn btn-sm btn-info"><i class="icon-history"></i> Statement</a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    @if($fees->count() < 1)
                        <tr><td colspan="10" class="text-center text-muted">No billing records yet. Generate billing above.</td></tr>
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection