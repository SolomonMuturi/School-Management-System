@extends('layouts.master')
@section('page_title', 'Activity Logs')
@section('content')

    <div class="card">
        <div class="card-header header-elements-inline">
            <h6 class="card-title">System Activity Logs</h6>
            {!! Qs::getPanelOptions() !!}
        </div>

        <div class="card-body">
            <form method="get" action="{{ route('users.activity') }}" class="mb-3">
                <div class="row">
                    <div class="col-md-2">
                        <select name="action" class="form-control select" data-placeholder="All Actions">
                            <option value="">All Actions</option>
                            @foreach($actions as $act)
                                <option value="{{ $act }}" {{ ($query['action'] ?? '') == $act ? 'selected' : '' }}>{{ $act }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="user_id" class="form-control select-search" data-placeholder="All Users">
                            <option value="">All Users</option>
                            @foreach($users as $u)
                                <option value="{{ $u->id }}" {{ ($query['user_id'] ?? '') == $u->id ? 'selected' : '' }}>{{ $u->name }} ({{ ucfirst($u->user_type) }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="date_from" value="{{ $query['date_from'] ?? '' }}" class="form-control" title="From Date">
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="date_to" value="{{ $query['date_to'] ?? '' }}" class="form-control" title="To Date">
                    </div>
                    <div class="col-md-3">
                        <div class="input-group">
                            <input type="text" name="search" value="{{ $query['search'] ?? '' }}" class="form-control" placeholder="Search activity...">
                            <span class="input-group-append">
                                <button class="btn btn-primary" type="submit"><i class="icon-search4"></i></button>
                            </span>
                        </div>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                    <tr>
                        <th>S/N</th>
                        <th>Date &amp; Time</th>
                        <th>User</th>
                        <th>Action</th>
                        <th>Module</th>
                        <th>Description</th>
                        <th>IP Address</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $log->created_at ? $log->created_at->format('d M Y, H:i:s') : '-' }}</td>
                            <td>
                                @if($log->user)
                                    <a href="{{ route('users.show', Qs::hash($log->user_id)) }}">{{ $log->user->name }}</a>
                                    <br><small class="text-muted">{{ ucfirst($log->user->user_type) }}</small>
                                @else
                                    <span class="text-muted">System / Deleted</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-{{ $log->action == 'Deleted' ? 'danger' : (in_array($log->action, ['Logged in', 'Logged out']) ? 'secondary' : ($log->action == 'Created / Saved' || $log->action == 'Saved' ? 'success' : 'info')) }}">{{ $log->action }}</span>
                            </td>
                            <td>{{ $log->module }}</td>
                            <td>{{ $log->description }}</td>
                            <td><small class="text-muted">{{ $log->ip ?: '-' }}</small></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">No activity recorded yet.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-3">
                {{ $logs->appends($query)->links() }}
            </div>
        </div>
    </div>

@endsection