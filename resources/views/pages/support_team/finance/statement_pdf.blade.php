<html>
<head>
    <meta charset="utf-8">
    <title>Fee Statement - {{ $student->name }}</title>
    <style>
        * { font-family: DejaVu Sans, sans-serif; }
        body { font-size: 12px; color: #111; }
        .school { text-align: center; margin-bottom: 12px; }
        .school h2 { margin: 0; }
        .school p { margin: 0; font-size: 11px; }
        h3 { text-align: center; margin: 6px 0 14px; text-decoration: underline; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        table, th, td { border: 1px solid #444; }
        th, td { padding: 5px 6px; font-size: 11px; }
        th { background: #eee; text-align: left; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .total td { font-weight: bold; background: #f5f5f5; }
        .info td { border: 1px solid #444; }
        .amount { text-align: right; white-space: nowrap; }
        .mt { margin-top: 6px; }
        small { color: #555; }
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

    <h3>FEE STATEMENT</h3>

    <table class="info">
        <tr>
            <td><strong>Student:</strong> {{ $student->name }}</td>
            <td><strong>Adm No:</strong> {{ $sr->adm_no ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td><strong>Class:</strong> {{ $sr->my_class->name ?? 'N/A' }}</td>
            <td><strong>Session / Term:</strong> {{ $year }} - {{ $selected }}</td>
        </tr>
    </table>

    <table>
        <thead>
        <tr>
            <th>Fee Type</th>
            <th>Term</th>
            <th class="amount">Invoiced</th>
            <th class="amount">Discount</th>
            <th class="amount">Paid</th>
            <th class="amount">Balance</th>
        </tr>
        </thead>
        <tbody>
        @foreach($fees as $f)
            <tr>
                <td>{{ $f->fee_type_name }}</td>
                <td>{{ $f->term }}</td>
                <td class="amount">{{ number_format($f->amount_due, 2) }}</td>
                <td class="amount">{{ number_format($f->discount, 2) }}</td>
                <td class="amount">{{ number_format($f->amount_paid, 2) }}</td>
                <td class="amount">{{ number_format(max(0, $f->amount_due - $f->discount - $f->amount_paid), 2) }}</td>
            </tr>
        @endforeach
        @if($fees->count() < 1)
            <tr><td colspan="6" class="text-center">No invoices for this session.</td></tr>
        @endif
        <tr class="total">
            <td colspan="2" class="text-right">TOTALS:</td>
            <td class="amount">{{ number_format($fees->sum('amount_due'), 2) }}</td>
            <td class="amount">{{ number_format($fees->sum('discount'), 2) }}</td>
            <td class="amount">{{ number_format($fees->sum('amount_paid'), 2) }}</td>
            <td class="amount">{{ number_format($fees->sum(function($f){ return max(0, $f->amount_due - $f->discount - $f->amount_paid); }), 2) }}</td>
        </tr>
        </tbody>
    </table>

    <table>
        <thead>
        <tr>
            <th>Date</th>
            <th>Receipt No</th>
            <th>Fee Type</th>
            <th>Method</th>
            <th class="amount">Amount</th>
        </tr>
        </thead>
        <tbody>
        @foreach($payments as $p)
            <tr>
                <td>{{ $p->payment_date }}</td>
                <td>{{ $p->receipt->receipt_no ?? '---' }}</td>
                <td>{{ optional(optional($p->studentFee)->feeStructure)->feeType->name ?? 'N/A' }}</td>
                <td>{{ $p->method_label }}</td>
                <td class="amount">{{ number_format($p->amount, 2) }}</td>
            </tr>
        @endforeach
        @if($payments->count() < 1)
            <tr><td colspan="5" class="text-center">No payments.</td></tr>
        @endif
        </tbody>
    </table>

    @if($discounts->count())
        <table>
            <thead>
            <tr>
                <th>Date</th>
                <th>Fee Type</th>
                <th>Type</th>
                <th class="amount">Amount</th>
            </tr>
            </thead>
            <tbody>
            @foreach($discounts as $d)
                <tr>
                    <td>{{ $d->created_at ? $d->created_at->format('Y-m-d') : '-' }}</td>
                    <td>{{ optional(optional($d->studentFee)->feeStructure)->feeType->name ?? 'N/A' }}</td>
                    <td>{{ ucwords($d->type) }}</td>
                    <td class="amount">{{ number_format($d->amount, 2) }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif

    <p class="mt"><small>Generated on {{ now()->format('d M Y, H:i') }} by {{ $settings['currency_code'] ?? 'NGN' }} amounts.</small></p>
</body>
</html>