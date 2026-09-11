<div class="card">
    <div class="card-header bg-white header-elements-inline">
        <h6 class="card-title">Activities & Extracurricular</h6>
        @if(Qs::userIsTeamSAT())
            <div class="header-elements">
                <a href="{{ route('students.activities', Qs::hash($sr->id)) }}" class="btn btn-primary btn-sm">Manage Activities</a>
            </div>
        @endif
    </div>
    <div class="card-body">
        @if($activities->count())
            <table class="table table-bordered">
                <thead class="thead-light">
                    <tr>
                        <th>Type</th>
                        <th>Name</th>
                        <th>Role</th>
                        <th>Session</th>
                        <th>Achievements</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($activities as $act)
                        <tr>
                            <td><span class="badge badge-secondary">{{ $act->activity_type }}</span></td>
                            <td>{{ $act->activity_name }}</td>
                            <td>{{ $act->role ?: '-' }}</td>
                            <td>{{ $act->session ?: '-' }}</td>
                            <td>{{ $act->achievements ?: '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p class="text-muted mb-0">No activities recorded.</p>
        @endif
    </div>
</div>