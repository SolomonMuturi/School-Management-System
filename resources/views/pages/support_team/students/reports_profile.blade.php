<html>
<head>
    <title>Student Profile Report - {{ $sr->user->name }}</title>
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/my_print.css') }}" />
</head>
<body>
<div class="container">
    <div id="print">
        <table width="100%">
            <tr>
                <td><img src="{{ Qs::getSetting('logo') }}" style="max-height : 100px;"></td>
                <td style="text-align: center; ">
                    <strong><span style="color: #1b0c80; font-size: 25px;">{{ strtoupper(Qs::getSetting('system_name')) }}</span></strong><br/>
                    <strong><span style="color: #000; font-size: 15px;"><i>{{ ucwords(Qs::getSetting('address')) }}</i></span></strong><br/>
                    <strong><span style="color: #000; font-size: 15px;">STUDENT PROFILE REPORT</span></strong>
                </td>
                <td style="width: 100px; height: 100px; float: left;">
                    <img src="{{ $sr->user->photo }}" width="100" height="100" alt="photo">
                </td>
            </tr>
        </table>
        <br/>

        <table border="1" cellpadding="5" style="width:100%; border-collapse: collapse;">
            <tr><td style="width:30%"><strong>Student Name</strong></td><td>{{ $sr->user->name }}</td></tr>
            <tr><td><strong>Student ID / Adm No</strong></td><td>{{ $sr->adm_no }}</td></tr>
            <tr><td><strong>Class</strong></td><td>{{ $sr->my_class->name }}</td></tr>
            <tr><td><strong>Status</strong></td><td>{{ ucfirst($sr->status ?: 'active') }}</td></tr>
            <tr><td><strong>Session</strong></td><td>{{ $sr->session }}</td></tr>
            <tr><td><strong>Year Admitted</strong></td><td>{{ $sr->year_admitted }}</td></tr>
            <tr><td><strong>Admission Date</strong></td><td>{{ $sr->admission_date ? \Carbon\Carbon::parse($sr->admission_date)->format('d M Y') : 'N/A' }}</td></tr>
            <tr><td><strong>Previous School</strong></td><td>{{ $sr->previous_school ?: 'N/A' }}</td></tr>
            <tr><td><strong>Gender</strong></td><td>{{ $sr->user->gender }}</td></tr>
            <tr><td><strong>Date of Birth</strong></td><td>{{ $sr->user->dob }}</td></tr>
            <tr><td><strong>Address</strong></td><td>{{ $sr->user->address }}</td></tr>
            <tr><td><strong>Phone</strong></td><td>{{ $sr->user->phone.' '.$sr->user->phone2 }}</td></tr>
            <tr><td><strong>Email</strong></td><td>{{ $sr->user->email }}</td></tr>
            @if($sr->user->blood_group)
                <tr><td><strong>Blood Group</strong></td><td>{{ $sr->user->blood_group->name }}</td></tr>
            @endif
            @if($sr->my_parent)
                <tr><td><strong>Primary Guardian</strong></td><td>{{ $sr->my_parent->name.' ('.$sr->my_parent->phone.')' }}</td></tr>
            @endif
        </table>

        <h4>Attendance Summary</h4>
        <table border="1" cellpadding="5" style="width:100%; border-collapse: collapse;">
            <tr>
                <th>Present</th>
                <th>Late</th>
                <th>Absent</th>
                <th>Attendance Rate</th>
            </tr>
            <tr>
                <td>{{ $att_present }}</td>
                <td>{{ $att_late }}</td>
                <td>{{ $att_absent }}</td>
                <td>{{ $att_rate ? $att_rate.'%' : 'N/A' }}</td>
            </tr>
        </table>

        <h4>Fee Summary</h4>
        <table border="1" cellpadding="5" style="width:100%; border-collapse: collapse;">
            <tr>
                <th>Total Charged</th>
                <th>Discount</th>
                <th>Paid</th>
                <th>Outstanding Balance</th>
            </tr>
            <tr>
                <td>{{ number_format($fees->sum('amount_due'), 2) }}</td>
                <td>{{ number_format($fees->sum('discount'), 2) }}</td>
                <td>{{ number_format($fees->sum('amount_paid'), 2) }}</td>
                <td>{{ number_format($fees->sum('balance'), 2) }}</td>
            </tr>
        </table>

        <h4>Exam Results</h4>
        <table border="1" cellpadding="5" style="width:100%; border-collapse: collapse;">
            <thead>
                <tr style="background:#ddd;">
                    <th>Exam</th>
                    <th>Term</th>
                    <th>Year</th>
                    <th>Average</th>
                    <th>Position</th>
                </tr>
            </thead>
            <tbody>
                @foreach($exam_records as $er)
                    <?php $exam = isset($exams[$er->exam_id]) ? $exams[$er->exam_id] : NULL; ?>
                    <tr>
                        <td>{{ $exam ? $exam->name : 'Exam #'.$er->exam_id }}</td>
                        <td>{{ $exam ? $exam->term : 'N/A' }}</td>
                        <td>{{ $er->year }}</td>
                        <td>{{ $er->ave }}</td>
                        <td>{{ $er->pos }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <p style="text-align:right; margin-top: 40px;">______________________<br/>Signature & Date</p>
    </div>
</div>
</body>
</html>