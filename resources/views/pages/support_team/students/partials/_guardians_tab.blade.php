<div class="card">
    <div class="card-header bg-white header-elements-inline">
        <h6 class="card-title">Parents & Guardians</h6>
        @if(Qs::userIsTeamSAT())
            <div class="header-elements">
                <a href="{{ route('students.guardians') }}" class="btn btn-primary btn-sm">Manage Guardians</a>
            </div>
        @endif
    </div>
    <div class="card-body">
        @if($guardians->count())
            <table class="table table-bordered">
                <thead class="thead-light">
                    <tr>
                        <th>Name</th>
                        <th>Relationship</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Primary</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($guardians as $g)
                        <tr>
                            <td>
                                <a href="{{ route('users.show', Qs::hash($g->user_id)) }}">{{ $g->user->name }}</a>
                                @if($g->user_id == $sr->my_parent_id)
                                    <span class="badge badge-primary ml-1">Primary</span>
                                @endif
                            </td>
                            <td>{{ $g->relationship }}</td>
                            <td>{{ $g->user->phone }}</td>
                            <td>{{ $g->user->email ?: '-' }}</td>
                            <td>
                                @if($g->is_primary || $g->user_id == $sr->my_parent_id)
                                    <span class="badge badge-success">Yes</span>
                                @else
                                    <span class="badge badge-secondary">No</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @elseif($sr->my_parent_id)
            <p>
                Primary Guardian: <a href="{{ route('users.show', Qs::hash($sr->my_parent_id)) }}">{{ $sr->my_parent->name }}</a>
            </p>
        @else
            <p class="text-muted mb-0">No guardian linked to this student yet.</p>
        @endif
    </div>
</div>