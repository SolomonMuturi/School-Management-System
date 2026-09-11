@extends('layouts.master')
@section('page_title', 'Classes Dashboard')
@section('content')

    <div class="card">
        <div class="card-header header-elements-inline">
            <h6 class="card-title">Classes Dashboard</h6>
            {!! Qs::getPanelOptions() !!}
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="card bg-teal-400">
                        <div class="card-body text-center">
                            <h3 class="font-weight-bold mb-0">{{ $classes->count() }}</h3>
                            <span>Total Classes</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-primary-400">
                        <div class="card-body text-center">
                            <h3 class="font-weight-bold mb-0">{{ $active_classes }}</h3>
                            <span>Active Classes</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success-400">
                        <div class="card-body text-center">
                            <h3 class="font-weight-bold mb-0">{{ $total_students }}</h3>
                            <span>Active Students</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-indigo-400">
                        <div class="card-body text-center">
                            <h3 class="font-weight-bold mb-0">{{ $classes->where('teacher_id', '!=', null)->count() }}</h3>
                            <span>Classes With Class Teacher</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-12">
                    <a href="{{ route('classes.index') }}" class="btn btn-outline-primary btn-sm">Manage Classes</a>
                    <a href="{{ route('students.create') }}" class="btn btn-outline-success btn-sm">Add Student</a>
                </div>
            </div>

            <table class="table table-bordered datatable-responsive">
                <thead>
                <tr>
                    <th>S/N</th>
                    <th>Class</th>
                    <th>Code</th>
                    <th>Type</th>
                    <th>Class Teacher</th>
                    <th>Students</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                @foreach($classes as $c)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td><a href="{{ route('classes.show', $c->id) }}">{{ $c->name }}</a></td>
                        <td>{{ $c->code ?: '-' }}</td>
                        <td>{{ $c->class_type ? $c->class_type->name : '-' }}</td>
                        <td>{{ $c->teacher ? $c->teacher->name : '-' }}</td>
                        <td><span class="badge badge-success">{{ $c->student_count }}</span> active</td>
                        <td><span class="badge {{ $c->status == 'active' ? 'badge-success' : 'badge-secondary' }}">{{ $c->status ?: 'active' }}</span></td>
                        <td class="text-center">
                            <div class="list-icons">
                                <div class="dropdown">
                                    <a href="#" class="list-icons-item" data-toggle="dropdown"><i class="icon-menu9"></i></a>
                                    <div class="dropdown-menu dropdown-menu-left">
                                        <a href="{{ route('classes.show', $c->id) }}" class="dropdown-item"><i class="icon-eye"></i> View Class</a>
                                        <a href="{{ route('classes.edit', $c->id) }}" class="dropdown-item"><i class="icon-pencil"></i> Edit</a>
                                        @if(Qs::userIsTeamSA())
                                        <form method="post" action="{{ route('classes.status', $c->id) }}">@csrf @method('put')
                                            <button type="submit" class="dropdown-item"><i class="icon-switch2"></i> {{ $c->status == 'active' ? 'Deactivate' : 'Activate' }}</button>
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