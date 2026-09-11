<html>
<head>
    <title>Student List</title>
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
                    <strong><span style="color: #000; font-size: 15px;">STUDENT LIST</span></strong>
                </td>
            </tr>
        </table>
        <br/>

        <table border="1" cellpadding="5" style="width:100%; border-collapse: collapse;">
            <thead>
                <tr style="background:#ddd;">
                    <th>#</th>
                    <th>Student Name</th>
                    <th>Student ID / Adm No</th>
                    <th>Class</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
            @foreach($students as $i => $s)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $s->user->name }}</td>
                    <td>{{ $s->adm_no }}</td>
                    <td>{{ $s->my_class->name }}</td>
                    <td>{{ ucfirst($s->status ?: 'active') }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <br/>
        <p>Total Number of Students on this list: <strong>{{ $students->count() }}</strong></p>
        <p style="text-align:right; margin-top: 40px;">______________________<br/>Signature & Date</p>
    </div>
</div>
</body>
</html>