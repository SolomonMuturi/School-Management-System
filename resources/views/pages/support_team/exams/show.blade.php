@extends('layouts.master')
@section('page_title', 'Exam Details - '.$ex->name)
@section('content')

    <div class="card">
        <div class="card-header header-elements-inline">
            <h6 class="card-title">{{ $ex->name }} <span class="text-muted">({{ ucfirst($ex->type ?: 'term') }} - Term {{ $ex->term }})</span></h6>
            {!! Qs::getPanelOptions() !!}
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="card bg-primary-400">
                        <div class="card-body text-center">
                            <h3 class="font-weight-bold mb-0">{{ $ex->year }}</h3>
                            <span>Session</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-teal-400">
                        <div class="card-body text-center">
                            <h3 class="font-weight-bold mb-0">{{ $ex->start_date ?: '-' }}</h3>
                            <span>Start Date</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-indigo-400">
                        <div class="card-body text-center">
                            <h3 class="font-weight-bold mb-0">{{ $ex->end_date ?: '-' }}</h3>
                            <span>End Date</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-{{ $ex->status == 'published' ? 'success' : ($ex->status == 'closed' ? 'secondary' : 'warning') }}-400">
                        <div class="card-body text-center">
                            <h3 class="font-weight-bold mb-0 text-uppercase">{{ $ex->status ?: 'pending' }}</h3>
                            <span>Status</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-12">
                    @if(Qs::userIsTeamSA())
                        @if($ex->status != 'published')
                            <form method="post" action="{{ route('exams.publish', $ex->id) }}" class="d-inline">@csrf
                                <button type="submit" class="btn btn-success btn-sm"><i class="icon-checkmark-circle2"></i> Publish Exam</button>
                            </form>
                        @endif
                        @if($ex->status != 'closed')
                            <form method="post" action="{{ route('exams.close', $ex->id) }}" class="d-inline">@csrf
                                <button type="submit" class="btn btn-secondary btn-sm"><i class="icon-cross2"></i> Close Exam</button>
                            </form>
                        @endif
                    @endif
                    <a href="{{ route('marks.index') }}" class="btn btn-primary btn-sm"><i class="icon-pencil6"></i> Manage Marks</a>
                    <a href="{{ route('tt.index') }}" class="btn btn-info btn-sm"><i class="icon-calendar3"></i> Manage Timetable</a>
                    <a href="{{ route('exams.edit', $ex->id) }}" class="btn btn-warning btn-sm"><i class="icon-pencil"></i> Edit Exam</a>
                </div>
            </div>

            @if($locked)
                <div class="alert alert-warning border-0">
                    <i class="icon-lock mr-2"></i> Exam entry is currently <strong>locked</strong>. Mark entry is restricted to Super Admin / Academic Admin.
                </div>
            @endif

            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header header-elements-inline">
                            <h6 class="card-title">Subject Averages ({{ $ex->year }})</h6>
                        </div>
                        <div class="card-body">
                            <table class="table datatable-responsive">
                                <thead>
                                <tr>
                                    <th>S/N</th>
                                    <th>Subject</th>
                                    <th>Class</th>
                                    <th>Teacher</th>
                                    <th>Marks</th>
                                    <th>Average</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($subjects as $s)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $s->name }}</td>
                                        <td>{{ $s->my_class ? $s->my_class->name : '-' }}</td>
                                        <td>{{ $s->teacher ? $s->teacher->name : '-' }}</td>
                                        <td>{{ $s->submitted }}</td>
                                        <td>{{ $s->average }}</td>
                                    </tr>
                                @endforeach
                                @if(!$subjects->count())
                                    <tr><td colspan="6" class="text-center text-muted">No marks recorded for this exam yet</td></tr>
                                @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header header-elements-inline">
                            <h6 class="card-title">Exam Records</h6>
                        </div>
                        <div class="card-body text-center">
                            <h1 class="font-weight-bold">{{ $records->count() }}</h1>
                            <span>{{ $records->count() == 1 ? 'student record' : 'student records' }}</span>
                            <hr>
                            <table class="table table-sm">
                                <tbody>
                                @foreach($classes as $c)
                                    <tr>
                                        <td>{{ $c->name }}</td>
                                        <td><span class="badge badge-primary">{{ $records->where('my_class_id', $c->id)->count() }}</span></td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection