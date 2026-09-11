@extends('layouts.master')
@section('page_title', 'Exams Dashboard')
@section('content')

    <div class="card">
        <div class="card-header header-elements-inline">
            <h6 class="card-title">Exams Dashboard <span class="text-muted">({{ $session }})</span></h6>
            {!! Qs::getPanelOptions() !!}
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="card bg-teal-400">
                        <div class="card-body text-center">
                            <h3 class="font-weight-bold mb-0">{{ $total_exams }}</h3>
                            <span>Total Exams</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success-400">
                        <div class="card-body text-center">
                            <h3 class="font-weight-bold mb-0">{{ $published }}</h3>
                            <span>Published</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-primary-400">
                        <div class="card-body text-center">
                            <h3 class="font-weight-bold mb-0">{{ $current_exams->count() }}</h3>
                            <span>Exams This Session</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-{{ $locked ? 'warning' : 'success' }}-400">
                        <div class="card-body text-center">
                            <h3 class="font-weight-bold mb-0 text-uppercase">{{ $locked ? 'Locked' : 'Open' }}</h3>
                            <span>Exam Entry</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-12">
                    <a href="{{ route('exams.index') }}" class="btn btn-outline-primary btn-sm">Manage Exams</a>
                    <a href="{{ route('marks.index') }}" class="btn btn-outline-success btn-sm">Manage Marks</a>
                    <a href="{{ route('tt.index') }}" class="btn btn-outline-secondary btn-sm">Manage Timetable</a>
                    <a href="{{ route('pins.index') }}" class="btn btn-outline-warning btn-sm">Manage PINs</a>
                </div>
            </div>

            <table class="table table-bordered datatable-responsive">
                <thead>
                <tr>
                    <th>S/N</th>
                    <th>Exam</th>
                    <th>Type</th>
                    <th>Term</th>
                    <th>Session</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                @foreach($exams as $ex)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td><a href="{{ route('exams.show', $ex->id) }}">{{ $ex->name }}</a></td>
                        <td>{{ ucfirst($ex->type ?: 'term') }}</td>
                        <td>{{ 'Term '.$ex->term }}</td>
                        <td>{{ $ex->year }}</td>
                        <td>{{ $ex->start_date ?: '-' }}</td>
                        <td>{{ $ex->end_date ?: '-' }}</td>
                        <td><span class="badge {{ $ex->status == 'published' ? 'badge-success' : ($ex->status == 'closed' ? 'badge-secondary' : 'badge-warning') }}">{{ $ex->status ?: 'pending' }}</span></td>
                        <td class="text-center">
                            <div class="list-icons">
                                <div class="dropdown">
                                    <a href="#" class="list-icons-item" data-toggle="dropdown"><i class="icon-menu9"></i></a>
                                    <div class="dropdown-menu dropdown-menu-left">
                                        <a href="{{ route('exams.show', $ex->id) }}" class="dropdown-item"><i class="icon-eye"></i> View Exam</a>
                                        <a href="{{ route('exams.edit', $ex->id) }}" class="dropdown-item"><i class="icon-pencil"></i> Edit</a>
                                        @if(Qs::userIsTeamSA())
                                        <form method="post" action="{{ route('exams.publish', $ex->id) }}">@csrf
                                            <button type="submit" class="dropdown-item"><i class="icon-checkmark-circle2"></i> Publish</button>
                                        </form>
                                        <form method="post" action="{{ route('exams.close', $ex->id) }}">@csrf
                                            <button type="submit" class="dropdown-item"><i class="icon-cross2"></i> Close</button>
                                        </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

@endsection