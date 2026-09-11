<html>
<head>
    <meta charset="utf-8">
    <title>Marksheet - {{ $sr->user->name }}</title>
    <style>
        * { font-family: DejaVu Sans, sans-serif; }
        body { font-size: 12px; color: #111; }
        .school { text-align: center; margin-bottom: 12px; }
        .school h2 { margin: 0; color: #1b0c80; }
        .school p { margin: 0; font-size: 11px; }
        .title { text-align: center; margin: 6px 0 14px; }
        .title h3 { margin: 0; text-transform: uppercase; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
        table, th, td { border: 1px solid #444; }
        th, td { padding: 5px 6px; font-size: 11px; }
        th { background: #eee; }
        .info table { border: 1px solid #444; }
        .info td { border: 1px solid #444; }
        .amount { text-align: right; }
        .center { text-align: center; }
        .comment { height: 70px; vertical-align: top; }
        .comments { width: 50%; vertical-align: top; }
        .sign td { border: none; text-align: center; font-size: 11px; }
        .sign .line { border-top: 1px solid #444; padding-top: 4px; width: 200px; margin: 0 auto; }
        small { color: #555; }
    </style>
</head>
<body>
    <div class="school">
        @if($pdf_logo = Qs::getPdfLogoDataUri())
            <img src="{{ $pdf_logo }}" style="max-height:70px; max-width:180px; margin-bottom:6px;">
        @endif
        <h2>{{ strtoupper($s['system_name'] ?? 'School') }}</h2>
        <p>{{ $s['address'] ?? '' }}</p>
        <p style="margin-top:4px;"><strong>REPORT SHEET ({{ strtoupper($class_type->name) }})</strong></p>
    </div>

    <div class="title">
        <h3>{{ $ex->name }} (Term {{ $ex->term }}) - {{ $ex->year }} Session</h3>
    </div>

    <table class="info">
        <tr>
            <td><strong>Student:</strong> {{ $sr->user->name }}</td>
            <td><strong>Admission No:</strong> {{ $sr->adm_no }}</td>
        </tr>
        <tr>
            <td><strong>Class:</strong> {{ $my_class->name }}</td>
            <td><strong>Exam:</strong> {{ $ex->name }} (Term {{ $ex->term }})</td>
        </tr>
    </table>

    <table>
        <thead>
        <tr>
            <th style="width:5%">S/N</th>
            <th>Subject</th>
            <th>Test (TCA)</th>
            <th>Exam (EXM)</th>
            <th class="amount">Total</th>
            <th>Grade</th>
            <th>Position</th>
        </tr>
        </thead>
        <tbody>
        @foreach($marks as $m)
            <tr>
                <td class="center">{{ $loop->iteration }}</td>
                <td>{{ $m->subject ? $m->subject->name : 'Subject #' . $m->subject_id }}</td>
                <td class="center">{{ $m->tca }}</td>
                <td class="center">{{ $m->exm }}</td>
                <td class="amount"><strong>{{ $m->$tex ?? (($m->tca ?: 0) + ($m->exm ?: 0)) }}</strong></td>
                <td class="center">{{ $m->grade ? $m->grade->name : 'N/A' }}</td>
                <td class="center">{{ $m->sub_pos ?: '-' }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <table>
        <tr>
            <td class="comments">
                <p><strong>Total Average:</strong> {{ $exr->ave ?? '-' }}</p>
                <p><strong>Position in Class:</strong> {{ $exr->pos ?? '-' }}</p>
            </td>
            <td class="comments">
                <p><strong>Teacher's Comment:</strong></p>
                <p class="comment">{{ $exr->t_comment ?? '' }}</p>
            </td>
        </tr>
        <tr>
            <td class="comments">
                <p><strong>Principal's Comment:</strong></p>
                <p class="comment">{{ $exr->p_comment ?? '' }}</p>
            </td>
            <td class="comments">
                <p><strong>Editor's / School's Remark:</strong></p>
                <p class="comment"></p>
            </td>
        </tr>
    </table>

    <table class="sign">
        <tr>
            <td><div class="line">Class Teacher</div></td>
            <td><div class="line">Principal</div></td>
        </tr>
    </table>

    <p><small>Generated on {{ now()->format('d M Y, H:i') }}.</small></p>
</body>
</html>