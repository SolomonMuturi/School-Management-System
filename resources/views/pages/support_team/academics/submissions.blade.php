@extends('layouts.master')
@section('page_title', 'Assignment Submissions')
@section('content')

    <div class="card">
        <div class="card-header bg-white header-elements-inline">
            <h6 class="card-title">Submissions: {{ $assignment->title }}</h6>
            {!! Qs::getPanelOptions() !!}
        </div>

        <div class="card-body">
            <div class="alert alert-info">
                <strong>{{ $assignment->subject->name }}</strong> &mdash; {{ $assignment->myClass->name }}
                &nbsp;|&nbsp; Teacher: {{ $assignment->teacher->name }}
                &nbsp;|&nbsp; Due: {{ $assignment->due_date }} &nbsp;|&nbsp; Max Marks: {{ $assignment->max_marks }}
                @if($assignment->instructions)
                    <div class="mt-2">{{ $assignment->instructions }}</div>
                @endif
            </div>

            <ul class="nav nav-tabs nav-tabs-highlight">
                <li class="nav-item"><a href="#submissions" class="nav-link active" data-toggle="tab">Submitted ({{ $submissions->count() }})</a></li>
                @if(Qs::userIsTeamSAT())
                    <li class="nav-item"><a href="#record" class="nav-link" data-toggle="tab"><i class="icon-plus2"></i> Record Submission</a></li>
                @endif
            </ul>

            <div class="tab-content">
                <div class="tab-pane fade show active" id="submissions">
                    <table class="table datatable-responsive">
                        <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Student</th>
                            <th>Submitted</th>
                            <th>Attachment</th>
                            <th>Marks</th>
                            <th>Feedback</th>
                            @if(Qs::userIsTeamSAT())<th>Grade</th>@endif
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($submissions as $sub)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $sub->student->name }}</td>
                                <td>{{ $sub->submitted_at ? $sub->submitted_at->format('d M Y H:i') : '-' }}</td>
                                <td>
                                    @if($sub->attachment_path)
                                        <span class="badge badge-primary">file</span>
                                    @else
                                        <span class="badge badge-light">text</span>
                                    @endif
                                    {{ $sub->submission_text ? Str::limit($sub->submission_text, 40) : '' }}
                                </td>
                                <td>{{ $sub->marks !== NULL ? $sub->marks.' / '.$assignment->max_marks : '-' }}</td>
                                <td>{{ $sub->feedback }}</td>
                                @if(Qs::userIsTeamSAT())
                                <td>
                                    <form method="post" action="{{ route('academic.homework.grade') }}" class="form-inline">
                                        @csrf
                                        <input type="hidden" name="submission_id" value="{{ $sub->id }}">
                                        <input type="number" name="marks" class="form-control form-control-sm mr-1" style="width:70px" min="0" max="{{ $assignment->max_marks }}" placeholder="Marks" value="{{ $sub->marks }}">
                                        <input type="text" name="feedback" class="form-control form-control-sm mr-1" placeholder="Feedback" value="{{ $sub->feedback }}">
                                        <button class="btn btn-sm btn-primary"><i class="icon-checkmark3"></i></button>
                                    </form>
                                </td>
                                @endif
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center text-muted">No submissions yet.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                @if(Qs::userIsTeamSAT())
                <div class="tab-pane fade" id="record">
                    <form method="post" action="{{ route('academic.homework.submit') }}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="assignment_id" value="{{ $assignment->id }}">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Student: </label>
                                    <select name="student_id" class="select-search form-control" data-placeholder="Select Student" required>
                                        <option value=""></option>
                                        @foreach($students as $st)
                                            <option value="{{ $st->user_id }}">{{ $st->user->name }} - {{ $st->adm_no }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Attachment (optional): </label>
                                    <input type="file" name="attachment" class="form-input-styled" data-fouc>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Submission Text: </label>
                                    <textarea name="submission_text" class="form-control" rows="3"></textarea>
                                </div>
                            </div>
                        </div>
                        <button class="btn btn-primary">Record Submission</button>
                    </form>
                </div>
                @endif

            </div>
        </div>
    </div>

@endsection