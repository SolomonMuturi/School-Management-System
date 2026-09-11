@extends('layouts.master')
@section('page_title', 'Finance Reports')
@section('content')
    @include('partials.finance.finance_tabs')

    <div class="card">
        <div class="card-header header-elements-inline">
            <h5 class="card-title"><i class="icon-stats-growth mr-2 text-primary"></i>Reports</h5>
            {!! Qs::getPanelOptions() !!}
        </div>
        <div class="card-body">
            <form method="get" action="{{ route('finance.reports') }}" class="mb-4">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Report Type</label>
                            <select name="type" class="form-control select" required>
                                <option value="">Select Report</option>
                                <option value="fees_collected" {{ $type == 'fees_collected' ? 'selected' : '' }}>Fees Collected</option>
                                <option value="outstanding" {{ $type == 'outstanding' ? 'selected' : '' }}>Outstanding Fees</option>
                                <option value="student_balances" {{ $type == 'student_balances' ? 'selected' : '' }}>Student Balances</option>
                                <option value="payments" {{ $type == 'payments' ? 'selected' : '' }}>All Payments</option>
                                <option value="expenses" {{ $type == 'expenses' ? 'selected' : '' }}>Expenses</option>
                                <option value="revenue" {{ $type == 'revenue' ? 'selected' : '' }}>Revenue by Method</option>
                                <option value="net_income" {{ $type == 'net_income' ? 'selected' : '' }}>Net Income</option>
                                <option value="payments_by_method" {{ $type == 'payments_by_method' ? 'selected' : '' }}>Payments by Method</option>
                                <option value="fees_by_class" {{ $type == 'fees_by_class' ? 'selected' : '' }}>Fees by Class</option>
                                <option value="fees_by_term" {{ $type == 'fees_by_term' ? 'selected' : '' }}>Fees by Term</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Term</label>
                            <select name="term" class="form-control select">
                                <option value="">All Terms</option>
                                @foreach($terms as $t)
                                    <option value="{{ $t }}" {{ $term == $t ? 'selected' : '' }}>{{ $t }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2 mt-4">
                        <button type="submit" class="btn btn-primary btn-block"><i class="icon-stats-growth mr-1"></i> Generate</button>
                    </div>
                    <div class="col-md-3 mt-4">
                        <button onclick="window.print()" class="btn btn-info btn-block"><i class="icon-file-download mr-1"></i> Download</button>
                    </div>
                </div>
            </form>

            @if(!isset($report) || !count($report['rows'] ?? []))
                @if(isset($report) && count($report['rows']) < 1)
                    <div class="text-center py-4"><h5 class="text-muted">No data found for this report.</h5></div>
                @else
                    <div class="text-center py-5"><h5 class="text-muted">Pick a report type above to generate a report.</h5></div>
                @endif
            @else
                <div class="mb-3">
                    <h5 class="mb-0 font-weight-bold">
                        Report - Session {{ $year }} {{ $term ? '(' . $term . ')' : '' }}
                    </h5>
                </div>
                <div class="table-responsive" id="print-area">
                    @if($type == 'fees_collected' || $type == 'payments')
                        <table class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>S/N</th>
                                <th>Student</th>
                                <th>Fee Type</th>
                                <th>Method</th>
                                <th>Amount</th>
                                <th>Date</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($report['rows'] as $i => $r)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $r->student->name ?? 'N/A' }}</td>
                                    <td>{{ optional(optional($r->studentFee)->feeStructure)->feeType->name ?? 'N/A' }}</td>
                                    <td>{{ $r->method_label }}</td>
                                    <td class="font-weight-semibold text-success">{{ number_format($r->amount, 2) }}</td>
                                    <td>{{ $r->payment_date }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                            <tfoot><tr class="table-light font-weight-bold"><td colspan="4">TOTAL</td><td>{{ number_format($report['total'], 2) }}</td><td></td></tr></tfoot>
                        </table>

                    @elseif($type == 'outstanding')
                        <table class="table table-bordered table-striped">
                            <thead>
                            <tr><th>S/N</th><th>Student</th><th>Fee Type</th><th>Status</th><th>Balance</th></tr>
                            </thead>
                            <tbody>
                            @foreach($report['rows'] as $r)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $r->student->name ?? 'N/A' }}</td>
                                    <td>{{ optional(optional($r->feeStructure)->feeType)->name ?? 'N/A' }}</td>
                                    <td>{{ ucwords($r->status) }}</td>
                                    <td class="font-weight-semibold text-danger">{{ number_format($r->balance, 2) }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                            <tfoot><tr class="table-light font-weight-bold"><td colspan="4">TOTAL OUTSTANDING</td><td>{{ number_format($report['total'], 2) }}</td></tr></tfoot>
                        </table>

                    @elseif($type == 'student_balances' || $type == 'fees_by_class')
                        <table class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>S/N</th>
                                <th>{{ $type == 'student_balances' ? 'Student' : 'Class' }}</th>
                                <th>Charged</th>
                                <th>Paid</th>
                                <th>Balance</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($report['rows'] as $r)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $type == 'student_balances' ? ($r['student']->name ?? 'N/A') : $r['class'] }}</td>
                                    <td>{{ number_format($r['total_charged'], 2) }}</td>
                                    <td class="text-success">{{ number_format($r['total_paid'], 2) }}</td>
                                    <td class="text-danger font-weight-semibold">{{ number_format($r['balance'], 2) }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                            <tfoot><tr class="table-light font-weight-bold"><td colspan="4">TOTAL BALANCE</td><td>{{ number_format($report['total'], 2) }}</td></tr></tfoot>
                        </table>

                    @elseif($type == 'expenses')
                        <table class="table table-bordered table-striped">
                            <thead>
                            <tr><th>S/N</th><th>Category</th><th>Description</th><th>Amount</th><th>Date</th></tr>
                            </thead>
                            <tbody>
                            @foreach($report['rows'] as $r)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $r->category->name ?? 'N/A' }}</td>
                                    <td>{{ $r->description }}</td>
                                    <td class="font-weight-semibold text-danger">{{ number_format($r->amount, 2) }}</td>
                                    <td>{{ $r->expense_date }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                            <tfoot><tr class="table-light font-weight-bold"><td colspan="3">TOTAL EXPENSES</td><td>{{ number_format($report['total'], 2) }}</td><td></td></tr></tfoot>
                        </table>

                    @elseif($type == 'revenue' || $type == 'payments_by_method')
                        <table class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>S/N</th>
                                <th>Method</th>
                                @if($type == 'payments_by_method')<th>Count</th>@endif
                                <th>Total</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($report['rows'] as $r)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ ucwords(str_replace('_', ' ', $r->method)) }}</td>
                                    @if($type == 'payments_by_method')<td>{{ $r->count }}</td>@endif
                                    <td class="font-weight-semibold text-success">{{ number_format($r->total, 2) }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                            @if($type != 'revenue')
                            <tfoot><tr class="table-light font-weight-bold"><td colspan="{{ $type == 'payments_by_method' ? 3 : 2 }}">TOTAL</td><td>{{ number_format($report['total'], 2) }}</td></tr></tfoot>
                            @endif
                        </table>

                    @elseif($type == 'net_income')
                        <table class="table table-bordered table-striped">
                            <thead>
                            <tr><th>S/N</th><th>Item</th><th>Amount</th></tr>
                            </thead>
                            <tbody>
                            @foreach($report['rows'] as $r)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $r['item'] }}</td>
                                    <td class="font-weight-semibold {{ $r['amount'] < 0 ? 'text-danger' : 'text-success' }}">{{ number_format($r['amount'], 2) }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                            <tfoot><tr class="table-light font-weight-bold"><td colspan="2">NET INCOME</td><td>{{ number_format($report['total'], 2) }}</td></tr></tfoot>
                        </table>

                    @elseif($type == 'fees_by_term')
                        <table class="table table-bordered table-striped">
                            <thead>
                            <tr><th>S/N</th><th>Term</th><th>Charged</th><th>Paid</th><th>Balance</th></tr>
                            </thead>
                            <tbody>
                            @foreach($report['rows'] as $r)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $r->term }}</td>
                                    <td>{{ number_format($r->charged, 2) }}</td>
                                    <td class="text-success">{{ number_format($r->paid, 2) }}</td>
                                    <td class="text-danger font-weight-semibold">{{ number_format($r->balance, 2) }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                            <tfoot><tr class="table-light font-weight-bold"><td colspan="2">TOTAL CHARGED</td><td>{{ number_format($report['total'], 2) }}</td><td></td><td></td></tr></tfoot>
                        </table>
                    @endif
                </div>
            @endif
        </div>
    </div>
@endsection