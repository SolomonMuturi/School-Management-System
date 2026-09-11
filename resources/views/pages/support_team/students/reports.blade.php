@extends('layouts.master')
@section('page_title', 'Student Reports')
@section('content')

<div class="card">
    <div class="card-header bg-white">
        <h6 class="card-title">Available Student Reports</h6>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Full Student List</h6>
                        <p class="text-muted">Printable list of all current students with their classes and statuses.</p>
                        <a href="{{ route('students.reports.students') }}" class="btn btn-primary">Generate</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Class Report</h6>
                        <p class="text-muted">Printable class list for a selected class.</p>
                        <form method="get" action="{{ route('students.reports.class', '__') }}" onsubmit="this.action = this.action.replace('__', document.getElementById('rep_class_id').value); return true;">
                            <div class="form-group">
                                <select id="rep_class_id" class="select-search form-control">
                                    <option value="">Select Class</option>
                                    @foreach($my_classes as $c)
                                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">Generate</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Student Profile Report</h6>
                        <p class="text-muted">Printable full profile for one student. Also accessible from the Profile page.</p>
                        <div class="form-group">
                            <select id="rep_student_hash" class="select-search form-control">
                                <option value="">Select Student</option>
                                @foreach($students as $s)
                                    <option value="{{ Qs::hash($s->id) }}">{{ $s->user->name.' - '.$s->my_class->name.' ('.$s->adm_no.')' }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="button" class="btn btn-primary" onclick="window.open('{{ route('students.reports.profile', '__') }}'.replace('__', document.getElementById('rep_student_hash').value), '_blank')">Generate</button>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Financial Reports</h6>
                        <p class="text-muted">Fee statements and student bills are managed in the Finance module.</p>
                        <a href="{{ route('finance.reports') }}" class="btn btn-secondary">Finance Reports</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection