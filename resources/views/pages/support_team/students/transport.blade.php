@extends('layouts.master')
@section('page_title', 'Student Transport - '.$sr->user->name)
@section('content')

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-white header-elements-inline">
                <h6 class="card-title">{{ $sr->user->name }} - Transport Information</h6>
                <div class="header-elements">
                    <a href="{{ route('students.show', Qs::hash($sr->id)) }}" class="btn btn-secondary btn-sm">Back to Profile</a>
                </div>
            </div>
            <form method="post" action="{{ route('students.transport.store', Qs::hash($sr->id)) }}" autocomplete="off">
                @csrf
                <div class="card-body">
                    <div class="form-group">
                        <label>Transport Status</label>
                        <select name="status" class="select form-control">
                            <option value="active" {{ $transport && $transport->status == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ $transport && $transport->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Route</label>
                        <input type="text" name="route_name" value="{{ $transport ? $transport->route_name : '' }}" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Pickup Point</label>
                        <input type="text" name="pickup_point" value="{{ $transport ? $transport->pickup_point : '' }}" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Vehicle No</label>
                        <input type="text" name="vehicle_no" value="{{ $transport ? $transport->vehicle_no : '' }}" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Driver Name</label>
                        <input type="text" name="driver_name" value="{{ $transport ? $transport->driver_name : '' }}" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Driver Phone</label>
                        <input type="text" name="driver_phone" value="{{ $transport ? $transport->driver_phone : '' }}" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Notes</label>
                        <input type="text" name="notes" value="{{ $transport ? $transport->notes : '' }}" class="form-control" maxlength="200">
                    </div>
                    <button type="submit" class="btn btn-primary">Save Transport Info</button>
                </div>
            </form>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-white">
                <h6 class="card-title">Current Transport Details</h6>
            </div>
            <div class="card-body">
                @if($transport)
                    <table class="table table-bordered">
                        <tbody>
                            <tr><td class="font-weight-bold" width="35%">Status</td><td><span class="badge badge-{{ $transport->status == 'active' ? 'success' : 'secondary' }}">{{ ucfirst($transport->status) }}</span></td></tr>
                            <tr><td class="font-weight-bold">Route</td><td>{{ $transport->route_name ?: 'N/A' }}</td></tr>
                            <tr><td class="font-weight-bold">Pickup Point</td><td>{{ $transport->pickup_point ?: 'N/A' }}</td></tr>
                            <tr><td class="font-weight-bold">Vehicle No</td><td>{{ $transport->vehicle_no ?: 'N/A' }}</td></tr>
                            <tr><td class="font-weight-bold">Driver</td><td>{{ $transport->driver_name ?: 'N/A' }} {{ $transport->driver_phone ? '('.$transport->driver_phone.')' : '' }}</td></tr>
                            <tr><td class="font-weight-bold">Notes</td><td>{{ $transport->notes ?: 'N/A' }}</td></tr>
                        </tbody>
                    </table>
                @else
                    <p class="text-muted mb-0">No transport information recorded yet.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection