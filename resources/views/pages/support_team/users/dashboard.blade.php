@extends('layouts.master')
@section('page_title', 'Users Dashboard')
@section('content')

    {{--Stat Cards--}}
    <div class="row">
        <div class="col-sm-6 col-xl-3 mb-3">
            <div class="dashboard-stat stat-admins">
                <h3>{{ $total_users }}</h3>
                <span class="stat-label">Total Users</span>
                <i class="icon-users stat-icon"></i>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3 mb-3">
            <div class="dashboard-stat stat-parents">
                <h3>{{ $active_users }}</h3>
                <span class="stat-label">Active Users</span>
                <i class="icon-user-check stat-icon"></i>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3 mb-3">
            <div class="dashboard-stat stat-students">
                <h3>{{ $inactive_users }}</h3>
                <span class="stat-label">Inactive Users</span>
                <i class="icon-user-block stat-icon"></i>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3 mb-3">
            <div class="dashboard-stat stat-teachers">
                <h3>{{ $roles->count() }}</h3>
                <span class="stat-label">Roles</span>
                <i class="icon-shield2 stat-icon"></i>
            </div>
        </div>
    </div>

    {{--Quick Actions--}}
    <div class="card mb-3">
        <div class="card-header header-elements-inline">
            <h6 class="card-title font-weight-semibold"><i class="icon-cog3 mr-2 text-primary"></i>Quick Actions</h6>
            {!! Qs::getPanelOptions() !!}
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-2 mb-md-0">
                    <a href="{{ route('users.index') }}" class="btn btn-outline-primary btn-block"><i class="icon-users4 mr-2"></i> Manage Users</a>
                </div>
                <div class="col-md-4 mb-2 mb-md-0">
                    <a href="{{ route('users.roles') }}" class="btn btn-outline-secondary btn-block"><i class="icon-shield2 mr-2"></i> Manage Roles</a>
                </div>
                <div class="col-md-4">
                    <a href="{{ route('users.permissions') }}" class="btn btn-outline-info btn-block"><i class="icon-key mr-2"></i> Permissions</a>
                </div>
            </div>
        </div>
    </div>

    {{--Detail Cards--}}
    <div class="row">
        {{--Users by Role--}}
        <div class="col-md-4 mb-3">
            <div class="card">
                <div class="card-header header-elements-inline">
                    <h6 class="card-title font-weight-semibold"><i class="icon-users mr-2 text-primary"></i>Users by Role</h6>
                    {!! Qs::getPanelOptions() !!}
                </div>
                <div class="card-body">
                    @foreach($roles as $role)
                        @php
                            $role_count = isset($users_by_role[$role->title]) ? $users_by_role[$role->title] : 0;
                        @endphp
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="font-weight-semibold">
                                    {{ $role->name }}
                                    @if($role->status != 'active')
                                        <span class="badge badge-secondary">inactive</span>
                                    @endif
                                </span>
                                <span class="text-muted">{{ $role_count }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{--Recently Created Users--}}
        <div class="col-md-4 mb-3">
            <div class="card">
                <div class="card-header header-elements-inline">
                    <h6 class="card-title font-weight-semibold"><i class="icon-user-plus mr-2 text-success"></i>Recently Created Users</h6>
                    {!! Qs::getPanelOptions() !!}
                </div>
                <div class="card-body">
                    @if(!$recent_users->count())
                        <p class="text-muted text-center mb-0">No users yet</p>
                    @else
                        <ul class="media-list pl-0 mb-0" style="list-style:none;">
                            @foreach($recent_users as $u)
                                <li class="media mb-3">
                                    <div class="mr-3">
                                        <img src="{{ $u->photo ?: Qs::getDefaultUserImage() }}" class="rounded-circle" width="42" height="42" style="object-fit: cover;">
                                    </div>
                                    <div class="media-body">
                                        <a href="{{ route('users.show', Qs::hash($u->id)) }}" class="font-weight-semibold media-title d-block">{{ $u->name }}</a>
                                        <span class="badge badge-flat border text-muted">{{ ucwords(str_replace('_', ' ', $u->user_type)) }}</span>
                                    </div>
                                    <div class="ml-3 align-self-center text-muted font-size-sm">{{ $u->created_at ? $u->created_at->format('d M') : '-' }}</div>
                                </li>
                            @endforeach
                        </ul>
                        <a href="{{ route('users.index') }}" class="text-primary d-block text-center font-weight-semibold">View all users <i class="icon-arrow-right14 ml-1"></i></a>
                    @endif
                </div>
            </div>
        </div>

        {{--Recent Login Activity--}}
        <div class="col-md-4 mb-3">
            <div class="card">
                <div class="card-header header-elements-inline">
                    <h6 class="card-title font-weight-semibold"><i class="icon-alarm mr-2 text-warning"></i>Recent Login Activity</h6>
                    {!! Qs::getPanelOptions() !!}
                </div>
                <div class="card-body">
                    @if(!$recent_logins->count())
                        <p class="text-muted text-center mb-0">No login activity yet</p>
                    @else
                        <ul class="media-list pl-0 mb-0" style="list-style:none;">
                            @foreach($recent_logins as $u)
                                <li class="media mb-3">
                                    <div class="mr-3">
                                        <img src="{{ $u->photo ?: Qs::getDefaultUserImage() }}" class="rounded-circle" width="42" height="42" style="object-fit: cover;">
                                    </div>
                                    <div class="media-body">
                                        <a href="{{ route('users.show', Qs::hash($u->id)) }}" class="font-weight-semibold media-title d-block">{{ $u->name }}</a>
                                        <span class="badge badge-flat border text-muted">{{ ucwords(str_replace('_', ' ', $u->user_type)) }}</span>
                                    </div>
                                    <div class="ml-3 align-self-center text-muted font-size-sm">{{ $u->last_login ? $u->last_login->format('d M, H:i') : '-' }}</div>
                                </li>
                            @endforeach
                        </ul>
                        <a href="{{ route('users.activity') }}" class="text-primary d-block text-center font-weight-semibold">View activity logs <i class="icon-arrow-right14 ml-1"></i></a>
                    @endif
                </div>
            </div>
        </div>
    </div>

@endsection