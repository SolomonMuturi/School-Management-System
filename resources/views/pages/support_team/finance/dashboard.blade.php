@extends('layouts.master')
@section('page_title', 'Finance Dashboard')
@section('content')
    @include('partials.finance.finance_tabs')

    <style>
        .dashboard-stat h3 { font-size: 1.25rem; margin-bottom: 0.1rem; }
        .finance-card h3 { font-size: 1.15rem; margin-bottom: 0.1rem; }
        .finance-card .text-uppercase { font-size: 11px; }
    </style>

    <div class="row">
        <div class="col-sm-6 col-xl-3 mb-3">
            <div class="dashboard-stat stat-students">
                <h3>{{ number_format($total_fees, 2) }}</h3>
                <span class="stat-label">Total Fees Charged</span>
                <i class="icon-stack3 stat-icon"></i>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3 mb-3">
            <div class="dashboard-stat stat-admins">
                <h3>{{ number_format($fees_collected, 2) }}</h3>
                <span class="stat-label">Fees Collected</span>
                <i class="icon-cash3 stat-icon"></i>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3 mb-3">
            <div class="dashboard-stat stat-teachers">
                <h3>{{ number_format($outstanding, 2) }}</h3>
                <span class="stat-label">Outstanding Fees</span>
                <i class="icon-user-block stat-icon"></i>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3 mb-3">
            <div class="dashboard-stat stat-parents">
                <h3>{{ number_format($overdue, 2) }}</h3>
                <span class="stat-label">Overdue Fees</span>
                <i class="icon-alarm stat-icon"></i>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6 col-xl-3 mb-3">
            <div class="card finance-card">
                <div class="card-body">
                    <div class="media">
                        <div class="media-body">
                            <h3 class="mb-0 font-weight-semibold">{{ number_format($fees_discount, 2) }}</h3>
                            <span class="text-uppercase font-size-xs text-muted">Discounts</span>
                        </div>
                        <div class="ml-3 align-self-center"><i class="icon-percent icon-2x text-primary"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3 mb-3">
            <div class="card finance-card">
                <div class="card-body">
                    <div class="media">
                        <div class="media-body">
                            <h3 class="mb-0 font-weight-semibold text-danger">{{ number_format($expenses, 2) }}</h3>
                            <span class="text-uppercase font-size-xs text-muted">Expenses</span>
                        </div>
                        <div class="ml-3 align-self-center"><i class="icon-cart5 icon-2x text-danger"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3 mb-3">
            <div class="card finance-card">
                <div class="card-body">
                    <div class="media">
                        <div class="media-body">
                            <h3 class="mb-0 font-weight-semibold text-warning">{{ number_format($refunds, 2) }}</h3>
                            <span class="text-uppercase font-size-xs text-muted">Refunds</span>
                        </div>
                        <div class="ml-3 align-self-center"><i class="icon-undo2 icon-2x text-warning"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3 mb-3">
            <div class="card finance-card">
                <div class="card-body">
                    <div class="media">
                        <div class="media-body">
                            <h3 class="mb-0 font-weight-semibold text-success">{{ number_format($net_income, 2) }}</h3>
                            <span class="text-uppercase font-size-xs text-muted">Net Income</span>
                        </div>
                        <div class="ml-3 align-self-center"><i class="icon-stats-growth icon-2x text-success"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-7">
            <div class="card">
                <div class="card-header header-elements-inline">
                    <h5 class="card-title"><i class="icon-user-block mr-2 text-danger"></i>Outstanding Balances</h5>
                    <a href="{{ route('finance.payments') }}" class="btn btn-sm btn-light">View Payments</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th>Student</th>
                                <th>Class</th>
                                <th>Amount Owed</th>
                                <th class="text-center">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($outstanding_students as $os)
                                <tr>
                                    <td>
                                        <a href="{{ route('finance.payments', ['student_id' => $os->student_id]) }}" class="font-weight-semibold">{{ $os->name }}</a>
                                        @if($os->adm_no)
                                            <br><small class="text-muted">{{ $os->adm_no }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $os->class ?: 'N/A' }}</td>
                                    <td class="text-danger font-weight-bold">{{ number_format($os->balance, 2) }}</td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#outstandingModal"
                                                data-student-id="{{ $os->student_id }}">
                                            <i class="icon-eye mr-1"></i>View
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                            @if($outstanding_students->count() < 1)
                                <tr><td colspan="4" class="text-center text-muted">No outstanding balances. Well done!</td></tr>
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card">
                <div class="card-header header-elements-inline">
                    <h5 class="card-title"><i class="icon-wallet mr-2 text-primary"></i>Account Balances</h5>
                    <a href="{{ route('finance.accounts') }}" class="btn btn-sm btn-light">Manage</a>
                </div>
                <div class="card-body">
                    @foreach($accounts as $acc)
                        <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                            <div>
                                <span class="font-weight-semibold">{{ $acc->name }}</span>
                                <span class="text-muted ml-2">{{ $acc->type_label }}</span>
                            </div>
                            <span class="font-weight-bold text-primary">{{ number_format($acc->current_balance, 2) }}</span>
                        </div>
                    @endforeach
                    @if($accounts->count() < 1)
                        <p class="text-muted mb-0">No accounts yet.</p>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-header header-elements-inline">
                    <h5 class="card-title"><i class="icon-stats-bars3 mr-2 text-primary"></i>Quick Stats</h5>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped mb-0">
                        <tbody>
                        <tr><td>Active Students</td><td class="text-right font-weight-semibold">{{ $stat_counts['students'] }}</td></tr>
                        <tr><td>Payments ({{ $year }})</td><td class="text-right font-weight-semibold">{{ $stat_counts['payments'] }}</td></tr>
                        <tr><td>Receipts ({{ $year }})</td><td class="text-right font-weight-semibold">{{ $stat_counts['receipts'] }}</td></tr>
                        <tr><td>Expenses ({{ $year }})</td><td class="text-right font-weight-semibold">{{ $stat_counts['expenses'] }}</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Outstanding Detail Modal --}}
    <div class="modal fade" id="outstandingModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="icon-user mr-2 text-primary"></i>Outstanding Detail</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="text-center py-4" id="outstandingLoading"><i class="icon-spinner spinner"></i> Loading...</div>
                    <div id="outstandingBody" style="display:none;"></div>
                </div>
                <div class="modal-footer">
                    <a href="#" id="outstandingPay" class="btn btn-primary"><i class="icon-cash3 mr-1"></i>Record Payment</a>
                    <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function fmt(n) {
            return Number(n || 0).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
        }

        $('#outstandingModal').on('show.bs.modal', function (e) {
            var sid = $(e.relatedTarget).data('student-id');
            var $body = $('#outstandingBody');
            var $load = $('#outstandingLoading');
            $body.hide();
            $load.show();

            $.getJSON('{{ route('finance.outstanding.detail', [':id']) }}'.replace(':id', sid), function (d) {
                var rows = '';
                rows += '<div class="row mb-3">';
                rows += '<div class="col-md-6"><strong>Student:</strong> ' + (d.name || 'N/A') + '</div>';
                rows += '<div class="col-md-6"><strong>Adm No:</strong> ' + (d.adm_no || 'N/A') + '</div>';
                rows += '<div class="col-md-6"><strong>Class:</strong> ' + (d.class || 'N/A') + '</div>';
                rows += '<div class="col-md-6"><strong>Session:</strong> {{ $year }}</div>';
                rows += '<div class="col-md-6 mt-1"><strong>Parent / Guardian:</strong> ' + (d.parent || 'N/A');
                if (d.parent_phone) { rows += ' (' + d.parent_phone + ')'; }
                rows += '</div>';
                rows += '</div>';

                rows += '<div class="row mb-3">';
                rows += '<div class="col-3"><span class="text-muted">Invoiced</span><h5 class="mb-0">' + fmt(d.due) + '</h5></div>';
                rows += '<div class="col-3"><span class="text-muted">Discount</span><h5 class="mb-0">' + fmt(d.discount) + '</h5></div>';
                rows += '<div class="col-3"><span class="text-muted">Paid</span><h5 class="mb-0 text-success">' + fmt(d.paid) + '</h5></div>';
                rows += '<div class="col-3"><span class="text-muted">Owed</span><h5 class="mb-0 text-danger">' + fmt(d.balance) + '</h5></div>';
                rows += '</div>';

                rows += '<h6 class="font-weight-bold text-uppercase">What the balance is for</h6>';
                rows += '<div class="table-responsive"><table class="table table-bordered"><thead><tr><th>Fee Type</th><th>Term</th><th class="text-right">Due</th><th class="text-right">Discount</th><th class="text-right">Paid</th><th class="text-right">Balance</th></tr></thead><tbody>';
                (d.breakdown || []).forEach(function (b) {
                    rows += '<tr>';
                    rows += '<td>' + (b.fee || 'N/A') + '</td>';
                    rows += '<td>' + (b.term || '') + '</td>';
                    rows += '<td class="text-right">' + fmt(b.due) + '</td>';
                    rows += '<td class="text-right">' + fmt(b.discount) + '</td>';
                    rows += '<td class="text-right">' + fmt(b.paid) + '</td>';
                    rows += '<td class="text-right font-weight-bold">' + fmt(b.balance) + '</td>';
                    rows += '</tr>';
                });
                rows += '</tbody></table></div>';

                if ((d.recent_payments || []).length) {
                    rows += '<h6 class="font-weight-bold text-uppercase mt-3">Most Recent Payments</h6>';
                    rows += '<div class="table-responsive"><table class="table table-striped table-bordered"><thead><tr><th>Date</th><th>Receipt</th><th>Method</th><th class="text-right">Amount</th></tr></thead><tbody>';
                    (d.recent_payments).forEach(function (p) {
                        rows += '<tr><td>' + (p.date || '-') + '</td><td>' + (p.receipt || '---') + '</td><td>' + (p.method || '') + '</td><td class="text-right">' + fmt(p.amount) + '</td></tr>';
                    });
                    rows += '</tbody></table></div>';
                }

                $body.html(rows).show();
                $load.hide();
                $('#outstandingPay').attr('href', '{{ route('finance.payments') }}?student_id=' + d.student_id);
            }).fail(function () {
                $load.html('<p class="text-danger mb-0">Could not load details. Please try again.</p>');
            });
        });
    </script>
@endsection