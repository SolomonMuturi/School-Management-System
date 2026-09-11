@extends('layouts.master')
@section('page_title', 'Student Attendance')
@section('content')

<div class="card">
    <div class="card-header bg-white header-elements-inline">
        <h6 class="card-title">Mark Daily Attendance</h6>
    </div>
    <form method="get" action="{{ route('students.attendance') }}" autocomplete="off">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Class <span class="text-danger">*</span></label>
                        <select name="class_id" id="class_id" class="select-search form-control">
                            <option value="">Select Class</option>
                            @foreach($my_classes as $c)
                                <option {{ $class_id == $c->id ? 'selected' : '' }} value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Date <span class="text-danger">*</span></label>
                        <input type="date" name="date" value="{{ $date }}" class="form-control">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group mt-4">
                        <button type="submit" class="btn btn-primary">Load</button>
                    </div>
                </div>
            </div>

            @if($students->count())
                <div class="row">
                    <div class="col-md-12">
                        <h6 class="mb-1">Summary for {{ \Carbon\Carbon::parse($date)->format('d M Y') }}:
                            <span class="badge badge-secondary">Marked: {{ $summary['marked'] }} / {{ $students->count() }}</span>
                            <span class="badge badge-success">Present: {{ $summary['present'] }}</span>
                            <span class="badge badge-warning">Late: {{ $summary['late'] }}</span>
                            <span class="badge badge-danger">Absent: {{ $summary['absent'] }}</span>
                        </h6>
                        <hr>
                    </div>
                </div>
            @endif
        </div>
    </form>

    @if($students->count())
        <form method="post" action="{{ route('students.attendance.store') }}" autocomplete="off">
            @csrf
            <input type="hidden" name="class_id" value="{{ $class_id }}">
            <input type="hidden" name="date" value="{{ $date }}">
            <div class="card-body pt-0">
                <table class="table table-bordered table-striped">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Student</th>
                            <th>Adm No</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($students as $i => $s)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $s->user->name }}</td>
                                <td>{{ $s->adm_no }}</td>
                                <td>
                                    <select name="status[{{ $s->user_id }}]" class="select form-control">
                                        <option value="present" {{ isset($attendance[$s->user_id]) && $attendance[$s->user_id]->status == 'present' ? 'selected' : '' }}>Present</option>
                                        <option value="late" {{ isset($attendance[$s->user_id]) && $attendance[$s->user_id]->status == 'late' ? 'selected' : '' }}>Late</option>
                                        <option value="absent" {{ isset($attendance[$s->user_id]) && $attendance[$s->user_id]->status == 'absent' ? 'selected' : '' }}>Absent</option>
                                    </select>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="text-right mt-3">
                    <button type="submit" class="btn btn-primary">Save Attendance</button>
                </div>
            </div>
        </form>
    @endif
</div>
@endsection