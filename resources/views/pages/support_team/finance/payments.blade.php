@extends('layouts.master')
@section('page_title', 'Payments')
@section('content')
    @include('partials.finance.finance_tabs')

    <div class="row">
        <div class="col-sm-6 col-xl-3 mb-3">
            <div class="dashboard-stat stat-admins">
                <h3>{{ number_format($today_total, 2) }}</h3>
                <span class="stat-label">Today's Collections</span>
                <i class="icon-calendar2 stat-icon"></i>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3 mb-3">
            <div class="dashboard-stat stat-parents">
                <h3>{{ number_format($total_collected, 2) }}</h3>
                <span class="stat-label">Total Collected</span>
                <i class="icon-piggy-bank stat-icon"></i>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3 mb-3">
            <div class="dashboard-stat stat-teachers">
                <h3>{{ number_format($refunds_total, 2) }}</h3>
                <span class="stat-label">Refunds</span>
                <i class="icon-undo2 stat-icon"></i>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3 mb-3">
            <div class="dashboard-stat stat-students">
                <h3>{{ number_format($total_collected - $refunds_total, 2) }}</h3>
                <span class="stat-label">Net Collections</span>
                <i class="icon-stats-bars3 stat-icon"></i>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header header-elements-inline">
            <h5 class="card-title"><i class="icon-cash3 mr-2 text-primary"></i>Payments</h5>
            <div>
                <button class="btn btn-success mr-1" data-toggle="modal" data-target="#mpesaModal"><i class="icon-phone mr-1"></i> Pay via M-Pesa</button>
                <button class="btn btn-primary" data-toggle="modal" data-target="#paymentModal"><i class="icon-plus3 mr-1"></i> Record Payment</button>
            </div>
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
                    @foreach($payments as $p)
                        @php $voided = $p->status === 'voided'; @endphp
                        <tr class="{{ $voided ? 'opacity-50' : '' }}">
                            <td>{{ $loop->iteration }}</td>
                            <td class="font-weight-semibold">
                                @if($p->receipt)
                                    <a href="{{ route('finance.receipts.show', $p->receipt->id) }}">{{ $p->receipt->receipt_no }}</a>
                                @else
                                    ---
                                @endif
                            </td>
                            <td>{{ $p->student->name ?? 'N/A' }}</td>
                            <td>{{ optional(optional($p->studentFee)->feeStructure)->feeType ? optional(optional($p->studentFee)->feeStructure)->feeType->name : 'N/A' }}</td>
                            <td class="text-success font-weight-bold">{{ number_format($p->amount, 2) }}</td>
                            <td>{{ $p->method_label }}</td>
                            <td>{{ $p->payment_date }}</td>
                            <td>
                                @if($voided)
                                    <span class="badge badge-danger">Voided</span>
                                @else
                                    <span class="badge badge-success">Active</span>
                                @endif
                            </td>
                            <td>
                                @if(!$voided)
                                    <div class="d-flex">
                                        @if($p->receipt)
                                            <a href="{{ route('finance.receipts.show', $p->receipt->id) }}" class="btn btn-sm btn-primary mr-1"><i class="icon-receipt mr-1"></i>Receipt</a>
                                        @else
                                            <span class="text-muted mr-1">No receipt</span>
                                        @endif
                                        <form method="post" action="{{ route('finance.payments.void', $p->id) }}" onsubmit="return confirm('Void this payment and reverse the receipt?')">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-danger"><i class="icon-x"></i></button>
                                        </form>
                                    </div>
                                @else
                                    <span class="text-muted">Voided</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    @if($payments->count() < 1)
                        <tr><td colspan="9" class="text-center text-muted">No payments yet.</td></tr>
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="paymentModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Record Payment</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form method="post" action="{{ route('finance.payments.store') }}" class="ajax-store" data-page-reload="1">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Class</label>
                                    <select id="pay_class_id" class="form-control select">
                                        <option value="all">All Classes</option>
                                        @foreach($my_classes as $mc)
                                            <option value="{{ $mc->id }}">{{ $mc->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label>Student <span class="text-danger">*</span></label>
                                    <select name="student_id" id="pay_student_id" class="form-control pay-search" required>
                                        @if(isset($selected_student))
                                            <option value="{{ $selected_student->id }}" selected>{{ $selected_student->name }}</option>
                                        @endif
                                    </select>
                                    <small class="form-text text-muted">Type a student's name or admission number to search.</small>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Fee <span class="text-danger">*</span></label>
                                    <select name="student_fee_id" id="pay_student_fee_id" class="form-control pay-search" required>
                                        <option value="">Select Student First</option>
                                        @if(isset($selected_fees))
                                            @foreach($selected_fees as $f)
                                                <option value="{{ $f->id }}">{{ ($f->fee_type_name ?: 'Fee') }} - {{ $f->term }} (KES {{ number_format($f->balance, 2) }})</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Amount</label>
                                    <input type="number" step="0.01" min="0.01" name="amount" class="form-control" placeholder="0.00" required>
                                </div>
                            </div>
                            <div class="col-md-3">
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
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Reference No.</label>
                                    <input type="text" name="reference_no" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
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
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Notes</label>
                                    <input type="text" name="notes" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Payment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="mpesaModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="icon-phone text-success mr-1"></i>Pay via M-Pesa</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form id="mpesa-form">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Class</label>
                            <select id="mpesa_class_id" class="form-control select">
                                <option value="all">All Classes</option>
                                @foreach($my_classes as $mc)
                                    <option value="{{ $mc->id }}">{{ $mc->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Student <span class="text-danger">*</span></label>
                            <select name="student_id" id="mpesa_student_id" class="form-control mpesa-search" required></select>
                            <small class="form-text text-muted">Type a student's name or admission number to search.</small>
                        </div>
                        <div class="form-group">
                            <label>Fee <span class="text-danger">*</span></label>
                            <select name="student_fee_id" id="mpesa_fee_id" class="form-control mpesa-search" required disabled>
                                <option value="">Select Student First</option>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Amount <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" min="1" name="amount" id="mpesa_amount" class="form-control" placeholder="0.00" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Reference (optional)</label>
                                    <input type="text" name="reference_no" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>M-Pesa Phone Number <span class="text-danger">*</span></label>
                            <input type="text" name="phone" id="mpesa_phone" class="form-control" placeholder="e.g. 07XXXXXXXX or 2547XXXXXXXX" required>
                        </div>
                        <div id="mpesa-status" class="alert alert-success" style="display: none;">
                            <i class="icon-spinner spinner mr-1"></i> Waiting for you to enter your M-Pesa PIN on your phone...
                        </div>
                        <div id="mpesa-error" class="alert alert-danger" style="display: none;"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                        <button type="submit" id="mpesa-submit" class="btn btn-success"><i class="icon-phone mr-1"></i>Send STK Push</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        var studentsUrl = '{{ route('finance.payments.students') }}';
        var feesUrl = '{{ route('finance.payments.fees') }}';

        function initPaySelects() {
            $('#pay_student_id').select2({
                ajax: {
                    url: studentsUrl,
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            q: params.term || '',
                            class_id: $('#pay_class_id').val() || 'all'
                        };
                    },
                    processResults: function (data) {
                        return {results: data.results};
                    },
                    cache: true
                },
                minimumInputLength: 0,
                placeholder: 'Search student by name or adm no...',
                allowClear: true,
                width: '100%'
            });

            $('#pay_student_fee_id').select2({
                ajax: {
                    url: feesUrl,
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {student_id: $('#pay_student_id').val()};
                    },
                    processResults: function (data) {
                        return {results: data.results};
                    },
                    cache: true
                },
                minimumInputLength: 0,
                placeholder: 'Select Student First',
                width: '100%'
            });
        }

        function reloadPayFees() {
            var $sel = $('#pay_student_fee_id');
            $sel.empty().val(null).trigger('change');

            if ($('#pay_student_id').val()) {
                $sel.removeAttr('disabled').prop('disabled', false);
            } else {
                $sel.prop('disabled', true);
            }
        }

        function reloadPayStudents() {
            var cur = $('#pay_student_id').val();
            $('#pay_student_id').val(null).trigger('change');
            if (cur) reloadPayFees();
        }

        $(function () {
            initPaySelects();
            $('#pay_class_id').on('change', reloadPayStudents);
            $('#pay_student_id').on('change', reloadPayFees);
            if (!$('#pay_student_id').val()) {
                $('#pay_student_fee_id').prop('disabled', true);
            }
        });

        /* ============ M-Pesa STK Push ============ */
        var mpesaPollTimer = null;

        $('#mpesa_student_id').select2({
            ajax: {
                url: studentsUrl,
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {q: params.term || '', class_id: $('#mpesa_class_id').val() || 'all'};
                },
                processResults: function (data) {
                    return {results: data.results};
                },
                cache: true
            },
            minimumInputLength: 0,
            placeholder: 'Search student by name or adm no...',
            allowClear: true,
            width: '100%'
        });

        $('#mpesa_fee_id').select2({
            ajax: {
                url: feesUrl,
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {student_id: $('#mpesa_student_id').val()};
                },
                processResults: function (data) {
                    return {results: data.results};
                },
                cache: true
            },
            minimumInputLength: 0,
            placeholder: 'Select Student First',
            width: '100%'
        });

        $('#mpesa_class_id').on('change', function () {
            $('#mpesa_student_id').val(null).trigger('change');
            $('#mpesa_fee_id').val(null).trigger('change').prop('disabled', true);
        });

        $('#mpesa_student_id').on('change', function () {
            var $fee = $('#mpesa_fee_id');
            $fee.empty().val(null).trigger('change');
            if ($(this).val()) {
                $fee.prop('disabled', false);
            } else {
                $fee.prop('disabled', true);
            }
        });

        $('#mpesa_fee_id').on('change', function () {
            var txt = $(this).find('option:selected').text();
            var m = txt.match(/\(KES\s*([\d,.]+)\)/);
            if (m) $('#mpesa_amount').val(m[1].replace(/,/g, ''));
        });

        $('#mpesa-form').on('submit', function (e) {
            e.preventDefault();
            var $err = $('#mpesa-error');
            var $status = $('#mpesa-status');
            $err.hide();
            $status.hide();
            $('#mpesa-submit').prop('disabled', true).html('<i class="icon-spinner spinner mr-1"></i>Sending...');

            $.post('{{ route('finance.mpesa.stk_push') }}', $(this).serialize(), function (res) {
                if (!res.ok) {
                    $err.text(res.msg).show();
                    $('#mpesa-submit').prop('disabled', false).html('<i class="icon-phone mr-1"></i>Send STK Push');
                    return;
                }
                $status.show();
                $('#mpesa-submit').hide();
                pollMpesa(res.id);
            }).fail(function (xhr) {
                var msg = 'M-Pesa request failed. Try again.';
                try {
                    var j = JSON.parse(xhr.responseText);
                    msg = j.msg || msg;
                } catch (err) {}
                $err.text(msg).show();
                $('#mpesa-submit').prop('disabled', false).html('<i class="icon-phone mr-1"></i>Send STK Push');
            });
        });

        function pollMpesa(id) {
            mpesaPollTimer = setInterval(function () {
                $.post('{{ route('finance.mpesa.status', [':id']) }}'.replace(':id', id), {
                    _token: '{{ csrf_token() }}'
                }, function (res) {
                    if (!res.ok || res.status === 'failed') {
                        clearInterval(mpesaPollTimer);
                        $('#mpesa-status').hide();
                        $('#mpesa-error').text(res.msg || 'Payment failed.').show();
                        $('#mpesa-submit').prop('disabled', false).show().html('<i class="icon-phone mr-1"></i>Send STK Push');
                        return;
                    }
                    if (res.status === 'completed') {
                        clearInterval(mpesaPollTimer);
                        if (res.redirect) {
                            window.location.href = res.redirect;
                        }
                    }
                }).fail(function () {
                    clearInterval(mpesaPollTimer);
                    $('#mpesa-status').hide();
                    $('#mpesa-error').text('Could not reach M-Pesa. The payment will still be confirmed by the callback.').show();
                    $('#mpesa-submit').prop('disabled', false).show().html('<i class="icon-phone mr-1"></i>Send STK Push');
                });
            }, 4000);
        }

        $('#mpesaModal').on('hidden.bs.modal', function () {
            if (mpesaPollTimer) clearInterval(mpesaPollTimer);
            $('#mpesa-status').hide();
            $('#mpesa-error').hide();
            $('#mpesa-submit').show().prop('disabled', false).html('<i class="icon-phone mr-1"></i>Send STK Push');
            $('#mpesa-form')[0].reset();
            $('#mpesa_class_id').val('all').trigger('change');
        });
    </script>
@endsection