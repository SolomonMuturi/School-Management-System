<html>
<head>
    <title>Report Card - {{ $sr->user->name }}</title>
    @if(!isset($pdf) || !$pdf)
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/my_print.css') }}" />
    @endif
    <style>
        @media print { body { -webkit-print-color-adjust: exact; } }
        .student-info td, .marks-table th, .marks-table td, .summary-table td { border: 1px solid #000; padding: 4px 6px; }
        .marks-table th { background: #eee; }
        table { border-collapse: collapse; }
        .summary-table td { vertical-align: top; }
    </style>
</head>
<body>
<div class="container">
    <div id="print">
        <div class="row text-center mb-2">
            <div class="col-md-12">
                @if($pdf_logo = Qs::getPdfLogoDataUri())
                    <img src="{{ $pdf_logo }}" style="max-height:70px; max-width:180px; margin-bottom:4px;">
                @endif
                <h3 style="margin:0;"><strong>{{ strtoupper(Qs::getSetting('system_name')) }}</strong></h3>
                <h5 style="margin:0;">{{ ucwords(Qs::getSetting('address')) }}</h5>
                <h4 style="margin:5px 0;"><strong>REPORT CARD</strong></h4>
                <h5 style="margin:0;">{{ $exam->year }} Session - {{ $exam->name }} (Term {{ $exam->term }})</h5>
            </div>
        </div>

        <table width="100%" class="student-info">
            <tr>
                <td><strong>Name:</strong> {{ $sr->user->name }}</td>
                <td><strong>Admission No:</strong> {{ $sr->adm_no }}</td>
            </tr>
            <tr>
                <td><strong>Class:</strong> {{ $sr->my_class->name }}</td>
                <td><strong>Exam:</strong> {{ $exam->name }} (Term {{ $exam->term }})</td>
            </tr>
        </table>
        <br/>

        <table class="table table-bordered marks-table">
            <thead>
            <tr>
                <th style="width:4%;">S/N</th>
                <th>Subject</th>
                <th>Test Score (TCA)</th>
                <th>Exam Score</th>
                <th>Total</th>
                <th>Grade</th>
                <th>Position</th>
            </tr>
            </thead>
            <tbody>
            @foreach($marks as $m)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $m->subject->name }}</td>
                    <td>{{ $m->tca }}</td>
                    <td>{{ $m->exm }}</td>
                    <td><strong>{{ ($m->tca ?: 0) + ($m->exm ?: 0) }}</strong></td>
                    <td>{{ $m->grade->name }}</td>
                    <td>{{ $m->sub_pos }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <br/>
        <table width="100%" class="summary-table">
            <tr>
                <td style="width:50%;">
                    <p><strong>Total Average:</strong> {{ $er->ave ?? '-' }}</p>
                    <p><strong>Class Average:</strong> {{ round($class_avg, 1) }}</p>
                    <p><strong>Position in Class:</strong>
                        @if(!empty($settings['report_card_position']))
                            {{ $er ? ($er->pos ? $er->pos.' of '.$class_count : '-') : '-' }}
                        @else
                            <em>Not displayed</em>
                        @endif
                    </p>
                    <p><strong>Total Students in Class:</strong> {{ $class_count }}</p>
                </td>
                <td style="width:50%;">
                    <p><strong>Teacher's Comment:</strong></p>
                    <p style="height:60px;">{{ $er->t_comment ?? '' }}</p>
                    <p><strong>Principal's Comment:</strong></p>
                    <p style="height:60px;">{{ $er->p_comment ?? '' }}</p>
                </td>
            </tr>
        </table>

        <br/>
        <table width="100%">
            <tr>
                <td style="width:50%; text-align:center;">
                    <br/><br/><br/>
                    <p style="border-top:1px solid #000; padding-top:5px;"><strong>Class Teacher</strong></p>
                </td>
                <td style="width:50%; text-align:center;">
                    <br/><br/><br/>
                    <p style="border-top:1px solid #000; padding-top:5px;"><strong>Principal</strong></p>
                </td>
            </tr>
        </table>
    </div>
</div>
</body>
</html>