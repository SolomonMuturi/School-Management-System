<div class="card">
    <div class="card-header bg-white header-elements-inline">
        <h6 class="card-title">Transport</h6>
        @if(Qs::userIsTeamSAT())
            <div class="header-elements">
                <a href="{{ route('students.transport', Qs::hash($sr->id)) }}" class="btn btn-primary btn-sm">Manage Transport</a>
            </div>
        @endif
    </div>
    <div class="card-body">
        @if($transport)
            <table class="table table-bordered">
                <tbody>
                    <tr><td class="font-weight-bold" width="30%">Status</td><td><span class="badge badge-{{ $transport->status == 'active' ? 'success' : 'secondary' }}">{{ ucfirst($transport->status) }}</span></td></tr>
                    <tr><td class="font-weight-bold">Route</td><td>{{ $transport->route_name ?: 'N/A' }}</td></tr>
                    <tr><td class="font-weight-bold">Pickup Point</td><td>{{ $transport->pickup_point ?: 'N/A' }}</td></tr>
                    <tr><td class="font-weight-bold">Vehicle No</td><td>{{ $transport->vehicle_no ?: 'N/A' }}</td></tr>
                    <tr><td class="font-weight-bold">Driver</td><td>{{ $transport->driver_name ?: 'N/A' }}{{ $transport->driver_phone ? ' ('.$transport->driver_phone.')' : '' }}</td></tr>
                    @if($transport->notes)
                        <tr><td class="font-weight-bold">Notes</td><td>{{ $transport->notes }}</td></tr>
                    @endif
                </tbody>
            </table>
        @else
            <p class="text-muted mb-0">No transport information recorded.</p>
        @endif
    </div>
</div>