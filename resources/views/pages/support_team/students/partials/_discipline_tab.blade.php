<div class="card">
    <div class="card-header bg-white header-elements-inline">
        <h6 class="card-title">Discipline Records</h6>
        @if(Qs::userIsTeamSAT())
            <div class="header-elements">
                <a href="{{ route('students.discipline', Qs::hash($sr->id)) }}" class="btn btn-primary btn-sm">Manage Discipline</a>
            </div>
        @endif
    </div>
    <div class="card-body">
        @if($discipline->count())
            <table class="table table-bordered">
                <thead class="thead-light">
                    <tr>
                        <th>Date</th>
                        <th>Incident</th>
                        <th>Warning</th>
                        <th>Action Taken</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($discipline as $rec)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($rec->date)->format('d M Y') }}</td>
                            <td>{{ $rec->incident_type }}</td>
                            <td>
                                <span class="badge badge-{{ $rec->warning_level == 'Severe' ? 'danger' : ($rec->warning_level == 'Major' ? 'warning' : 'secondary') }}">{{ $rec->warning_level }}</span>
                            </td>
                            <td>{{ $rec->action_taken ?: '-' }}</td>
                            <td>{{ Str::limit($rec->description, 60) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p class="text-muted mb-0">No discipline records.</p>
        @endif
    </div>
</div>