@extends('layouts.master')
@section('page_title', 'Manage Roles')
@section('content')

    <div class="card">
        <div class="card-header header-elements-inline">
            <h6 class="card-title">Manage Roles</h6>
            {!! Qs::getPanelOptions() !!}
        </div>

        <div class="card-body">
            @if(Qs::userIsSuperAdmin())
            <ul class="nav nav-tabs nav-tabs-highlight">
                <li class="nav-item"><a href="#all-roles" class="nav-link active" data-toggle="tab">All Roles</a></li>
                <li class="nav-item"><a href="#new-role" class="nav-link" data-toggle="tab"><i class="icon-plus2"></i> Create New Role</a></li>
            </ul>
            @endif

            <div class="tab-content">
                <div class="tab-pane fade show active" id="all-roles">
                    <table class="table datatable-button-html5-columns">
                        <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Role</th>
                            <th>Title</th>
                            <th>Level</th>
                            <th>Users</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($roles as $role)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $role->name }}</td>
                                <td>{{ $role->title }}</td>
                                <td>{{ $role->level }}</td>
                                <td><span class="badge badge-primary">{{ $role->users_count }}</span></td>
                                <td><span class="badge {{ $role->status == 'active' ? 'badge-success' : 'badge-secondary' }}">{{ $role->status ?: 'active' }}</span></td>
                                <td class="text-center">
                                    <div class="list-icons">
                                        <div class="dropdown">
                                            <a href="#" class="list-icons-item" data-toggle="dropdown"><i class="icon-menu9"></i></a>
                                            <div class="dropdown-menu dropdown-menu-left">
                                                <a href="{{ route('users.index').'#ut-'.Qs::hash($role->id) }}" class="dropdown-item"><i class="icon-users2"></i> View Users</a>
                                                @if(Qs::userIsSuperAdmin())
                                                <a href="{{ route('users.roles.edit', $role->id) }}" class="dropdown-item"><i class="icon-pencil"></i> Edit</a>
                                                <form method="post" action="{{ route('users.roles.toggle', $role->id) }}">@csrf @method('put')
                                                    <button type="submit" class="dropdown-item"><i class="icon-switch2"></i> {{ $role->status == 'active' ? 'Deactivate' : 'Activate' }}</button>
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

                @if(Qs::userIsSuperAdmin())
                <div class="tab-pane fade" id="new-role">
                    <form method="post" action="{{ route('users.roles.store') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Role Name <span class="text-danger">*</span></label>
                                    <input id="name" required name="name" value="{{ old('name') }}" type="text" class="form-control" placeholder="Eg. Games Master">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="title">Role Title / Key <span class="text-danger">*</span></label>
                                    <input id="title" required name="title" value="{{ old('title') }}" type="text" class="form-control" placeholder="Eg. games_master" >
                                    <span class="form-text text-muted">Unique lowercase key used internally. Spaces are replaced with underscores.</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="level">Role Level <span class="text-danger">*</span></label>
                                    <input id="level" required name="level" value="{{ old('level') ?: 6 }}" type="number" min="1" max="20" class="form-control">
                                    <span class="form-text text-muted">Higher numbers = lower privilege. Super Admin is level 1.</span>
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            <button type="submit" class="btn btn-primary">Create Role</button>
                        </div>
                    </form>
                </div>
                @endif
            </div>
        </div>
    </div>

@endsection