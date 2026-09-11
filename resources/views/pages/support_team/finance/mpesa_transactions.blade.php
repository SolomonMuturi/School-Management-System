@extends('layouts.master')
@section('page_title', 'M-Pesa Transactions')
@section('content')
    @include('partials.finance.finance_tabs')

    <div class="row">
        <div class="col-sm-6 col-xl-3 mb-3">
            <div class="dashboard-stat stat-admins">
                <h3>{{ $transactions->where('status', 'completed')->count() }}</h3>
                <span class="stat-label">Completed</span>
                <i class="icon-checkmark-circle2 stat-icon"></i>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3 mb-3">
            <div class="dashboard-stat stat-teachers">
                <h3>{{ $transactions->where('status', 'pending')->count() + $transactions->where('status', 'processing')->count() }}</h3>
                <span class="stat-label">Pending</span>
                <i class="icon-hourglass2 stat-icon"></i>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3 mb-3">
            <div class="dashboard-stat stat-parents">
                <h3>{{ $transactions->where('status', 'failed')->count() }}</h3>
                <span class="stat-label">Failed</span>
                <i class="icon-cross2 stat-icon"></i>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3 mb-3">
            <div class="dashboard-stat stat-students">
                <h3 class="text-success">{{ number_format($transactions->where('status', 'completed')->sum('amount'), 2) }}</h3>
                <span class="stat-label">Total Collected</span>
                <i class="icon-cash3 stat-icon"></i>
            </div>
        </div>
    </div>

    @if(!$configured)
        <div class="alert alert-warning alert-styled-left alert-dismissible">
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            <span class="font-weight-semibold">M-Pesa is not configured yet.</span> Ask an administrator to set the M-Pesa (Daraja) credentials in Finance &gt; Settings.
        </div>
    @endif

    <div class="card">
        <div class="card-header header-elements-inline">
            <h5 class="card-title"><i class="icon-phone mr-2 text-primary"></i>M-Pesa Transactions</h5>
            <div>
                <a href="{{ route('finance.payments') }}" class="btn btn-sm btn-primary mr-1"><i class="icon-cash3 mr-1"></i>Pay via M-Pesa</a>
                <button type="button" class="btn btn-sm btn-light" onclick="refreshStatuses()"><i class="icon-sync mr-1"></i>Refresh</button>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-3">
                    <select class="form-control select" onchange="if(this.value){window.location='{{ route('finance.mpesa') }}?status='+this.value}else{window.location='{{ route('finance.mpesa') }}'}">
                        <option value="">All Statuses</option>
                        @foreach(['pending', 'processing', 'completed', 'failed'] as $s)
                            <option value="{{ $s }}" {{ $selected_status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table datatable-basic table-bordered table-striped">
                    <thead>
                    <tr>
                        <th>S/N</th>
                        <th>Phone</th>
                        <th>Student</th>
                        <th>Reference</th>
                        <th class="text-right">Amount</th>
                        <th>Receipt No</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($transactions as $t)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $t->phone }}</td>
                            <td>{{ $t->student->name ?? 'N/A' }}</td>
                            <td>{{ $t->reference ?: ($t->checkout_request_id ? substr($t->checkout_request_id, 0, 16) . '...' : '---') }}</td>
                            <td class="text-right font-weight-semibold">{{ number_format($t->amount, 2) }}</td>
                            <td>{{ $t->mpesa_receipt_number ?: '---' }}</td>
                            <td>
                                @if($t->status === 'completed')
                                    <span class="badge badge-success">Completed</span>
                                @elseif($t->status === 'failed')
                                    <span class="badge badge-danger">Failed</span>
                                @else
                                    <span class="badge badge-secondary">{{ ucfirst($t->status) }}</span>
                                @endif
                                @if($t->payment)
                                    <br><a href="{{ route('finance.receipts.show', $t->payment->id) }}" class="font-size-xs">Receipt</a>
                                @endif
                            </td>
                            <td>{{ $t->created_at ? $t->created_at->format('d M Y H:i') : '' }}</td>
                            <td>
                                @if(in_array($t->status, ['pending', 'processing']))
                                    <button type="button" class="btn btn-sm btn-primary" onclick="checkStatus({{ $t->id }}, this)"><i class="icon-sync mr-1"></i>Check</button>
                                @else
                                    <span class="text-muted">---</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    @if($transactions->count() < 1)
                        <tr><td colspan="9" class="text-center text-muted">No M-Pesa transactions yet.</td></tr>
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function checkStatus(id, btn) {
            if (btn) { btn.disabled = true; btn.innerHTML = '<i class="icon-spinner spinner"></i> Checking...'; }
            $.post('{{ route('finance.mpesa.status', [':id']) }}'.replace(':id', id), {
                _token: '{{ csrf_token() }}'
            }, function (res) {
                if (res.redirect) {
                    window.location.href = res.redirect;
                    return;
                }
                location.reload();
            }).fail(function () {
                if (btn) btn.disabled = false;
                alert('Could not reach M-Pesa. Try again.');
            });
        }

        function refreshStatuses() {
            var $btns = $('button[onclick^="checkStatus"]');
            $btns.each(function () {
                var m = $(this).attr('onclick').match(/checkStatus\((\d+)/);
                if (m) $(this).trigger('click');
            });
            if ($btns.length === 0) { location.reload(); }
        }
    </script>
@endsection