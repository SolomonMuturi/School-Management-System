@extends('layouts.master')
@section('page_title', 'Subjects Dashboard')
@section('content')

    <div class="card">
        <div class="card-header header-elements-inline">
            <h6 class="card-title">Subjects Dashboard</h6>
            {!! Qs::getPanelOptions() !!}
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="card bg-teal-400">
                        <div class="card-body text-center">
                            <h3 class="font-weight-bold mb-0">{{ $total_subjects }}</h3>
                            <span>Total Subjects</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-success-400">
                        <div class="card-body text-center">
                            <h3 class="font-weight-bold mb-0">{{ $active_subjects }}</h3>
                            <span>Active Subjects</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-primary-400">
                        <div class="card-body text-center">
                            <h3 class="font-weight-bold mb-0">{{ $my_classes->count() }}</h3>
                            <span>Classes</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-12">
                    <a href="{{ route('subjects.index') }}" class="btn btn-outline-primary btn-sm">Manage Subjects</a>
                </div>
            </div>

            <table class="table table-bordered datatable-responsive">
                <thead>
                <tr>
                    <th>S/N</th>
                    <th>Subject</th>
                    <th>Code</th>
                    <th>Short Name</th>
                    <th>Class</th>
                    <th>Teacher</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                @foreach($subjects as $s)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td><a href="{{ route('subjects.show', $s->id) }}">{{ $s->name }}</a></td>
                        <td>{{ $s->code ?: '-' }}</td>
                        <td>{{ $s->slug }}</td>
                        <td>{{ $s->my_class ? $s->my_class->name : '-' }}</td>
                        <td>{{ $s->teacher ? $s->teacher->name : '-' }}</td>
                        <td><span class="badge {{ $s->status == 'active' ? 'badge-success' : 'badge-secondary' }}">{{ $s->status ?: 'active' }}</span></td>
                        <td class="text-center">
                            <div class="list-icons">
                                <div class="dropdown">
                                    <a href="#" class="list-icons-item" data-toggle="dropdown"><i class="icon-menu9"></i></a>
                                    <div class="dropdown-menu dropdown-menu-left">
                                        <a href="{{ route('subjects.show', $s->id) }}" class="dropdown-item"><i class="icon-eye"></i> View Subject</a>
                                        <a href="{{ route('subjects.edit', $s->id) }}" class="dropdown-item"><i class="icon-pencil"></i> Edit</a>
                                        @if(Qs::userIsTeamSA())
                                        <form method="post" action="{{ route('subjects.status', $s->id) }}">@csrf @method('put')
                                            <button type="submit" class="dropdown-item"><i class="icon-switch2"></i> {{ $s->status == 'active' ? 'Deactivate' : 'Activate' }}</button>
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