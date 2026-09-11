@extends('layouts.master')
@section('page_title', 'Report Cards')
@section('content')

    <div class="card">
        <div class="card-header bg-white header-elements-inline">
            <h6 class="card-title">Generate Report Card</h6>
            {!! Qs::getPanelOptions() !!}
        </div>

        <div class="card-body">
            <form method="post" action="{{ route('academic.report_cards.show') }}">
                @csrf
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Exam / Term: </label>
                            <select name="exam_id" class="select-search form-control" data-placeholder="Select Exam" required>
                                <option value=""></option>
                                @foreach($exams as $ex)
                                    <option value="{{ $ex->id }}">{{ $ex->name }} (Term {{ $ex->term }} - {{ $ex->year }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Class: </label>
                            <select name="class_id" id="rc_class" class="select-search form-control" data-placeholder="Select Class" required>
                                <option value=""></option>
                                @foreach($classes as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Student: </label>
                            <select name="student_id" id="rc_stud" class="select-search form-control" data-placeholder="Select Student" required>
                                <option value=""></option>
                            </select>
                        </div>
                    </div>
                </div>
                <button class="btn btn-primary"><i class="icon-eye mr-1"></i> Generate Report Card</button>
                <button type="submit" formaction="{{ route('academic.report_cards.pdf') }}" class="btn btn-danger"><i class="icon-file-download mr-1"></i> Download PDF</button>
            </form>
        </div>
    </div>

    <div class="alert alert-info border-0">
        <span>Report cards list total average, class average, class position (if enabled), teacher and principal comments, and are print-ready.</span>
    </div>

    <div class="card">
        <div class="card-header header-elements-inline">
            <h6 class="card-title">Academic Settings That Affect Report Cards</h6>
        </div>
        <div class="card-body">
            <table class="table">
                <tr>
                    <td>Show class position / ranking on report card</td>
                    <td>
                        @if(!empty($settings['report_card_position']))
                            <span class="badge badge-success">Enabled</span>
                        @else
                            <span class="badge badge-secondary">Disabled</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>Teacher comments</td>
                    <td>
                        @if(!empty($settings['enable_teacher_comments']))
                            <span class="badge badge-success">Enabled</span>
                        @else
                            <span class="badge badge-secondary">Disabled</span>
                        @endif
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const rcClass = document.getElementById('rc_class');
            const rcStud = document.getElementById('rc_stud');
            rcClass.addEventListener('change', function () {
                rcStud.innerHTML = '<option value=""></option>';
                if (!rcClass.value) return;
                fetch('{{ route('academic.report_cards.students') }}?class_id=' + rcClass.value)
                    .then(r => r.json())
                    .then(data => {
                        data.forEach(s => {
                            const o = document.createElement('option');
                            o.value = s.id;
                            o.textContent = s.name + ' (' + s.adm_no + ')';
                            rcStud.appendChild(o);
                        });
                    });
            });
        });
    </script>

@endsection