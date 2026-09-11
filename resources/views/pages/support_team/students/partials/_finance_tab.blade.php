<div class="row mb-3">
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body py-3">
                <h4 class="font-weight-semibold mb-1 text-dark">KES {{ number_format($fees->sum('amount_due')) }}</h4>
                <span class="text-muted">Total Fees</span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body py-3">
                <h4 class="font-weight-semibold mb-1 text-info">KES {{ number_format($fees->sum('discount')) }}</h4>
                <span class="text-muted">Discounts</span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body py-3">
                <h4 class="font-weight-semibold mb-1 text-success">KES {{ number_format($fees->sum('amount_paid')) }}</h4>
                <span class="text-muted">Paid</span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body py-3">
                <h4 class="font-weight-semibold mb-1 {{ $fees->sum('balance') > 0 ? 'text-danger' : 'text-success' }}">KES {{ number_format($fees->sum('balance')) }}</h4>
                <span class="text-muted">Balance</span>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white header-elements-inline">
        <h6 class="card-title"><i class="icon-cash3 mr-2 text-primary"></i>Fee Breakdown</h6>
        <div class="header-elements">
            @if(Qs::userIsTeamAccount())
                <form method="get" action="{{ route('finance.statement.pdf', $sr->user_id) }}" class="d-inline">
                    <button class="btn btn-info btn-sm"><i class="icon-file-download mr-1"></i>Download Statement PDF</button>
                </form>
                <a href="{{ route('finance.statement', $sr->user_id) }}" class="btn btn-secondary btn-sm ml-1">Fee Statement</a>
            @endif
        </div>
    </div>
    <div class="card-body">
        @if($fees->count())
            <table class="table table-bordered">
                <thead class="thead-light">
                    <tr>
                        <th>Fee Type</th>
                        <th>Term</th>
                        <th>Year</th>
                        <th>Amount Due</th>
                        <th>Discount</th>
                        <th>Paid</th>
                        <th>Balance</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($fees as $f)
                        <tr>
                            <td>{{ $f->fee_type_name }}</td>
                            <td>{{ $f->term }}</td>
                            <td>{{ $f->year }}</td>
                            <td>{{ number_format($f->amount_due, 2) }}</td>
                            <td>{{ number_format($f->discount, 2) }}</td>
                            <td>{{ number_format($f->amount_paid, 2) }}</td>
                            <td class="font-weight-bold">{{ number_format($f->balance, 2) }}</td>
                            <td>
                                <span class="badge badge-{{ $f->status == 'paid' ? 'success' : ($f->status == 'overdue' ? 'danger' : ($f->status == 'partial' ? 'warning' : 'secondary')) }}">{{ ucfirst($f->status) }}</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p class="text-muted mb-0">No fee records for this student yet.</p>
        @endif
    </div>
</div>

<div class="card">
    <div class="card-header bg-white header-elements-inline">
        <h6 class="card-title"><i class="icon-file-text3 mr-2 text-success"></i>Payment History</h6>
    </div>
    <div class="card-body">
        @if($payments->count())
            <table class="table table-bordered">
                <thead class="thead-light">
                    <tr>
                        <th>Date</th>
                        <th>Receipt</th>
                        <th>Method</th>
                        <th>Reference</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payments as $pay)
                        <tr>
                            <td>{{ $pay->payment_date ? \Carbon\Carbon::parse($pay->payment_date)->format('d M Y') : '-' }}</td>
                            <td>
                                @if($pay->receipt)
                                    @if(Qs::userIsTeamAccount())
                                        <a href="{{ route('finance.receipts.show', $pay->receipt->id) }}">{{ $pay->receipt->receipt_no }}</a>
                                    @else
                                        {{ $pay->receipt->receipt_no }}
                                    @endif
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>{{ $pay->method_label }}</td>
                            <td>{{ $pay->reference_no ?: '-' }}</td>
                            <td class="font-weight-bold">KES {{ number_format($pay->amount, 2) }}</td>
                            <td>
                                <span class="badge badge-{{ $pay->status == 'void' ? 'danger' : 'success' }}">{{ ucfirst($pay->status ?: 'active') }}</span>
                            </td>
                            <td>
                                @if($pay->receipt && Qs::userIsTeamAccount())
                                    <a href="{{ route('finance.receipts.pdf', $pay->receipt->id) }}" class="btn btn-sm btn-info"><i class="icon-file-download"></i></a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p class="text-muted mb-0">No payments recorded yet.</p>
        @endif
    </div>
</div>