<div class="card">
    <div class="card-header bg-white header-elements-inline">
        <h6 class="card-title">Attendance</h6>
        @if(Qs::userIsTeamSAT())
            <div class="header-elements">
                <a href="{{ route('students.attendance') }}" class="btn btn-primary btn-sm">Mark Attendance</a>
            </div>
        @endif
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-3"><span class="badge badge-success">Present: {{ $att_present }}</span></div>
            <div class="col-md-3"><span class="badge badge-warning">Late: {{ $att_late }}</span></div>
            <div class="col-md-3"><span class="badge badge-danger">Absent: {{ $att_absent }}</span></div>
            <div class="col-md-3"><strong>Attendance Rate: {{ $att_rate ? $att_rate.'%' : 'N/A' }}</strong></div>
        </div>

        @if($attendance->count())
            <table class="table table-bordered">
                <thead class="thead-light">
                    <tr>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Note</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($attendance->sortByDesc('date')->take(20) as $att)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($att->date)->format('d M Y') }}</td>
                            <td>
                                <span class="badge badge-{{ $att->status == 'present' ? 'success' : ($att->status == 'late' ? 'warning' : 'danger') }}">{{ ucfirst($att->status) }}</span>
                            </td>
                            <td>{{ $att->note ?: '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p class="text-muted mb-0">No attendance records yet.</p>
        @endif
    </div>
</div>