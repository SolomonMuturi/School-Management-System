@extends('layouts.master')
@section('page_title', 'Academic Reports')
@section('content')

    <div class="card">
        <div class="card-header bg-white header-elements-inline">
            <h6 class="card-title">Academic Reports</h6>
            {!! Qs::getPanelOptions() !!}
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="card card-body border-top-primary">
                        <h6 class="font-weight-semibold"><i class="icon-windows2"></i> Class Lists</h6>
                        <form method="get" action="{{ route('academic.reports.class') }}">
                            <div class="form-group">
                                <select name="class_id" class="select-search form-control" data-placeholder="Select Class" required>
                                    <option value=""></option>
                                    @foreach($classes as $c)
                                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button class="btn btn-primary btn-sm">View Class List</button>
                        </form>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card card-body border-top-info">
                        <h6 class="font-weight-semibold"><i class="icon-pin"></i> Subject Reports</h6>
                        <form method="get" action="{{ route('academic.reports.subject') }}">
                            <div class="form-group">
                                <select name="subject_id" class="select-search form-control" data-placeholder="Select Subject" required>
                                    <option value=""></option>
                                    @foreach($subjects as $s)
                                        <option value="{{ $s->id }}">{{ $s->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <input type="text" name="year" class="form-control" placeholder="Session e.g. {{ \Qs::getCurrentSession() }}" value="{{ \Qs::getCurrentSession() }}">
                            </div>
                            <button class="btn btn-info btn-sm">View Subject Report</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="card card-body border-top-success">
                        <h6 class="font-weight-semibold"><i class="icon-books"></i> Exam Reports</h6>
                        <form method="get" action="{{ route('academic.reports.exam') }}">
                            <div class="form-group">
                                <select name="exam_id" class="select-search form-control" data-placeholder="Select Exam" required>
                                    <option value=""></option>
                                    @foreach($exams as $ex)
                                        <option value="{{ $ex->id }}">{{ $ex->name }} (Term {{ $ex->term }} - {{ $ex->year }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <button class="btn btn-success btn-sm">View Exam Report</button>
                        </form>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card card-body border-top-danger">
                        <h6 class="font-weight-semibold"><i class="icon-copy"></i> Existing Academic Reports</h6>
                        <div class="list-group">
                            <a href="{{ route('marks.tabulation') }}" class="list-group-item list-group-item-action">Tabulation Sheet <span class="float-right text-muted">&rarr;</span></a>
                            <a href="{{ route('marks.bulk') }}" class="list-group-item list-group-item-action">Marksheet <span class="float-right text-muted">&rarr;</span></a>
                            <a href="{{ route('students.reports') }}" class="list-group-item list-group-item-action">Student Reports (Student Module) <span class="float-right text-muted">&rarr;</span></a>
                            <a href="{{ route('academic.report_cards') }}" class="list-group-item list-group-item-action">Report Cards <span class="float-right text-muted">&rarr;</span></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection