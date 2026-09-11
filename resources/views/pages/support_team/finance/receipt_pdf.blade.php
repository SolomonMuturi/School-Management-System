<html>
<head>
    <meta charset="utf-8">
    <title>Receipt - {{ $receipt->receipt_no }}</title>
    <style>
        * { font-family: DejaVu Sans, sans-serif; }
        body { font-size: 12px; color: #111; }
        .school { text-align: center; margin-bottom: 14px; }
        .school h2 { margin: 0; }
        .school p { margin: 0; font-size: 11px; }
        .title { text-align: center; margin: 8px 0 14px; }
        .title h3 { margin: 0; text-transform: uppercase; letter-spacing: 1px; text-decoration: underline; }
        table { width: 100%; border-collapse: collapse; }
        table, th, td { border: 1px solid #444; }
        th, td { padding: 5px 8px; font-size: 12px; }
        th { background: #eee; text-align: left; width: 30%; }
        .header-row td { background: #f5f5f5; font-weight: bold; }
        .amount { font-size: 15px; font-weight: bold; }
        .sign { margin-top: 40px; }
        .sign td { border: none; text-align: center; font-size: 11px; padding: 0 10px; }
        .sign .line { border-top: 1px solid #444; padding-top: 4px; }
    </style>
</head>
<body>
    <div class="school">
        @if($pdf_logo = Qs::getPdfLogoDataUri())
            <img src="{{ $pdf_logo }}" style="max-height:70px; max-width:180px; margin-bottom:6px;">
        @endif
        <h2>{{ strtoupper($settings['system_name'] ?? 'School') }}</h2>
        <p>{{ $settings['address'] ?? '' }}</p>
        @if(!empty($settings['system_email']))
            <p>{{ $settings['system_email'] }}</p>
        @elseif(!empty($settings['alt_email']))
            <p>{{ $settings['alt_email'] }}</p>
        @endif
    </div>

    <div class="title"><h3>Official Payment Receipt</h3></div>

    <table>
        <tr class="header-row">
            <td>Receipt No: {{ $receipt->receipt_no }}</td>
            <td style="width:40%; text-align:right;">Date: {{ $receipt->created_at ? $receipt->created_at->format('d M Y') : '-' }}</td>
        </tr>
        <tr>
            <th>Student Name</th>
            <td>{{ optional($receipt->student)->name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Admission No</th>
            <td>{{ $receipt->student && $receipt->student->admission ? $receipt->student->admission->adm_no : 'N/A' }}</td>
        </tr>
        <tr>
            <th>Class</th>
            <td>{{ ($receipt->student && $receipt->student->admission && $receipt->student->admission->my_class) ? $receipt->student->admission->my_class->name : 'N/A' }}</td>
        </tr>
        <tr>
            <th>Fee Description</th>
            <td>
                @if($receipt->payment && $receipt->payment->studentFee && $receipt->payment->studentFee->feeStructure)
                    {{ $receipt->payment->studentFee->feeStructure->feeType->name ?? 'N/A' }} - {{ $receipt->payment->studentFee->feeStructure->term }}
                @else
                    N/A
                @endif
            </td>
        </tr>
        <tr>
            <th>Amount Paid</th>
            <td class="amount">{{ $settings['currency_code'] ?? 'NGN' }} {{ number_format($receipt->amount ?? 0, 2) }}</td>
        </tr>
        <tr>
            <th>Balance After Payment</th>
            <td>{{ $settings['currency_code'] ?? 'NGN' }} {{ number_format($receipt->balance_after ?? 0, 2) }}</td>
        </tr>
        <tr>
            <th>Payment Method</th>
            <td>{{ optional($receipt->payment)->method_label ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Reference</th>
            <td>{{ optional($receipt->payment)->reference_no ?: '---' }}</td>
        </tr>
        <tr>
            <th>Account</th>
            <td>{{ $receipt->payment && $receipt->payment->account ? $receipt->payment->account->name : '---' }}</td>
        </tr>
        <tr>
            <th>Recorded By</th>
            <td>{{ optional($receipt->payment)->received_by ?: '---' }}</td>
        </tr>
    </table>

    <table class="sign">
        <tr>
            <td>
                <div class="line" style="width:220px; margin:0 auto;">Recorded By / Officer</div>
            </td>
            <td>
                <div class="line" style="width:220px; margin:0 auto;">Received By / Student Guardian</div>
            </td>
        </tr>
    </table>

    <p style="margin-top:20px; font-size:10px; color:#555;">Thank you. This receipt is computer-generated and requires no signature.</p>
</body>
</html>