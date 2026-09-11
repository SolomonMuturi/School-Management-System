@extends('layouts.master')
@section('page_title', 'Receipt ' . $receipt->receipt_no)
@section('content')
    @include('partials.finance.finance_tabs')

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header header-elements-inline">
                    <h5 class="card-title"><i class="icon-file-text mr-2 text-primary"></i>{{ $receipt->receipt_no }}</h5>
                    <div>
                        <a href="{{ route('finance.receipts.pdf', $receipt->id) }}" class="btn btn-sm btn-info"><i class="icon-file-download mr-1"></i> Download</a>
                        <a href="{{ route('finance.receipts') }}" class="btn btn-sm btn-light ml-1"><i class="icon-arrow-left mr-1"></i> Back</a>
                    </div>
                </div>
                <div class="card-body" id="print-area">
                    <div class="text-center mb-4">
                        <h4 class="mb-0 font-weight-bold">{{ $settings['system_name'] ?? 'School' }}</h4>
                        <span class="text-muted">{{ $settings['address'] ?? '' }}</span><br>
                        <h5 class="mt-2 mb-0 text-primary">{{ $settings['system_title'] ?? '' }}</h5>
                        @if(!empty($settings['system_email']))
                            <span>{{ $settings['system_email'] }}</span>
                        @elseif(!empty($settings['alt_email']))
                            <span>{{ $settings['alt_email'] }}</span>
                        @elseif(Qs::getSystemName())
                            <span>{{ Qs::getSystemName() }}</span>
                        @endif
                        <hr>
                        <h4 class="text-uppercase font-weight-bold mb-0">Official Payment Receipt</h4>
                    </div>

                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th colspan="2">
                                <span class="font-weight-bold">Receipt No: {{ $receipt->receipt_no }}</span>
                                <span class="float-right">{{ $receipt->created_at ? $receipt->created_at->format('Y-m-d') : '-' }}</span>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        @php $rStd = $receipt->payment && $receipt->payment->student ? $receipt->payment->student : null; @endphp
                        <tr>
                            <td class="font-weight-semibold" style="width:30%">Student Name</td>
                            <td>{{ $rStd ? $rStd->name : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-semibold">Admission No</td>
                            <td>{{ ($rStd && $rStd->admission) ? $rStd->admission->adm_no : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-semibold">Class</td>
                            <td>{{ ($rStd && $rStd->admission && $rStd->admission->my_class) ? $rStd->admission->my_class->name : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-semibold">Fee Description</td>
                            <td>
                                @if($receipt->payment && $receipt->payment->studentFee && $receipt->payment->studentFee->feeStructure)
                                    {{ $receipt->payment->studentFee->feeStructure->feeType->name ?? 'N/A' }} - {{ $receipt->payment->studentFee->feeStructure->term }}
                                @else
                                    N/A
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="font-weight-semibold">Amount Paid</td>
                            <td class="font-weight-bold text-success">
                                {{ $settings['currency_code'] ?? 'NGN' }} {{ number_format($receipt->payment->amount ?? 0, 2) }}
                            </td>
                        </tr>
                        <tr>
                            <td class="font-weight-semibold">Payment Method</td>
                            <td>{{ optional($receipt->payment)->method_label ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-semibold">Reference</td>
                            <td>{{ optional($receipt->payment)->reference_no ?? '---' }}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-semibold">Account</td>
                            <td>{{ optional($receipt->payment)->account->name ?? '---' }}</td>
                        </tr>
                        </tbody>
                    </table>

                    <div class="row mt-5">
                        <div class="col-6 text-center">
                            <hr class="mb-2" style="border-top:1px solid #999; width:80%">
                            <small class="text-muted">Recorded By / Officer</small>
                        </div>
                        <div class="col-6 text-center">
                            <hr class="mb-2" style="border-top:1px solid #999; width:80%">
                            <small class="text-muted">Received By / Student Guardian</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @if(isset($print) && $print)
        <script>window.onload = function(){ window.print(); }</script>
    @endif
@endsection