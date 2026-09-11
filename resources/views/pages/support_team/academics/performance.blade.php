@extends('layouts.master')
@section('page_title', 'Academic Performance')
@section('content')

    <div class="card">
        <div class="card-header bg-white header-elements-inline">
            <h6 class="card-title">Academic Performance</h6>
            {!! Qs::getPanelOptions() !!}
        </div>

        <div class="card-body">
            <div class="alert alert-info border-0">
                Current Session Performance Summary ({{ $current_year->name }}) -
                Average Test: <strong>{{ $summary['avg_tca'] }}</strong> |
                Average Exam: <strong>{{ $summary['avg_exm'] }}</strong> |
                Overall: <strong>{{ $summary['avg_total'] }}</strong> |
                Records: <strong>{{ $summary['count'] }}</strong>
            </div>

            <ul class="nav nav-tabs nav-tabs-highlight">
                <li class="nav-item"><a href="#by-class" class="nav-link active" data-toggle="tab">Class Performance</a></li>
                <li class="nav-item"><a href="#by-student" class="nav-link" data-toggle="tab">Student Performance</a></li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane fade show active" id="by-class">
                    <form method="get" action="{{ route('academic.performance.class') }}">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Class: </label>
                                    <select name="class_id" class="select-search form-control" data-placeholder="Select Class" required>
                                        <option value=""></option>
                                        @foreach($classes as $c)
                                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Academic Year: </label>
                                    <select name="year" class="select form-control">
                                        @foreach($years as $y)
                                            <option {{ $y->id == $current_year->id ? 'selected' : '' }} value="{{ $y->name }}">{{ $y->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4 align-self-center">
                                <button class="btn btn-primary">View Class Performance</button>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="tab-pane fade" id="by-student">
                    <form method="get" action="{{ route('academic.performance.student') }}">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Student: </label>
                                    <select name="student_id" class="select-search form-control" data-placeholder="Search Student by Name" required>
                                        <option value=""></option>
                                        @foreach($students as $st)
                                            <option value="{{ Qs::hash($st->id) }}">{{ $st->user->name }} ({{ $st->adm_no }})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4 align-self-center">
                                <button class="btn btn-primary">View Student Performance</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection