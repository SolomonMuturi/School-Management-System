<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-white header-elements-inline">
                <h6 class="card-title"><i class="icon-user mr-2 text-primary"></i>Personal Information</h6>
            </div>
            <div class="card-body">
                <table class="table table-bordered mb-0">
                    <tbody>
                        <tr>
                            <td class="font-weight-bold" width="40%">Full Name</td>
                            <td>{{ $sr->user->name }}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Gender</td>
                            <td>{{ $sr->user->gender }}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Date of Birth</td>
                            <td>{{ $sr->user->dob }}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Age</td>
                            <td>{{ $sr->user->dob ? \Carbon\Carbon::parse($sr->user->dob)->age . ' years' : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Student ID</td>
                            <td>{{ $sr->user->code }}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Admission No</td>
                            <td>{{ $sr->adm_no }}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Admission Date</td>
                            <td>{{ $sr->admission_date ? \Carbon\Carbon::parse($sr->admission_date)->format('d M Y') : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Year Admitted</td>
                            <td>{{ $sr->year_admitted ?: 'N/A' }}</td>
                        </tr>
                        @if($sr->house)
                            <tr>
                                <td class="font-weight-bold">Sport House</td>
                                <td>{{ $sr->house }}</td>
                            </tr>
                        @endif
                        @if($sr->previous_school)
                            <tr>
                                <td class="font-weight-bold">Previous School</td>
                                <td>{{ $sr->previous_school }}</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-white header-elements-inline">
                <h6 class="card-title"><i class="icon-graduation2 mr-2 text-success"></i>Academic Placement</h6>
            </div>
            <div class="card-body">
                <table class="table table-bordered mb-0">
                    <tbody>
                        <tr>
                            <td class="font-weight-bold" width="40%">Class</td>
                            <td>{{ $sr->my_class->name }}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Class Code</td>
                            <td>{{ $sr->my_class->code }}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Class Teacher</td>
                            <td>{{ $sr->my_class->teacher ? $sr->my_class->teacher->name : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Academic Year</td>
                            <td>{{ $sr->session }}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Current Term</td>
                            <td>{{ $current_term }}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Status</td>
                            <td>
                                @if($sr->status == 'active')
                                    <span class="badge badge-success">Active</span>
                                @elseif($sr->status == 'graduated')
                                    <span class="badge badge-primary">Graduated</span>
                                @elseif(in_array($sr->status, ['suspended', 'withdrawn']))
                                    <span class="badge badge-danger">{{ ucfirst($sr->status) }}</span>
                                @else
                                    <span class="badge badge-secondary">{{ ucfirst($sr->status ?: 'Active') }}</span>
                                @endif
                            </td>
                        </tr>
                        @if($sr->grad_date)
                            <tr>
                                <td class="font-weight-bold">Graduation Date</td>
                                <td>{{ \Carbon\Carbon::parse($sr->grad_date)->format('d M Y') }}</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-white header-elements-inline">
                <h6 class="card-title"><i class="icon-users2 mr-2 text-info"></i>Parent / Guardian</h6>
            </div>
            <div class="card-body">
                @if($sr->my_parent_id)
                    <table class="table table-bordered mb-0">
                        <tbody>
                            <tr>
                                <td class="font-weight-bold" width="40%">Name</td>
                                <td><a href="{{ route('users.show', Qs::hash($sr->my_parent_id)) }}">{{ $sr->my_parent->name }}</a></td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">Phone</td>
                                <td>{{ $sr->my_parent->phone ?: 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">Email</td>
                                <td>{{ $sr->my_parent->email ?: 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">Address</td>
                                <td>{{ $sr->my_parent->address ?: 'N/A' }}</td>
                            </tr>
                        </tbody>
                    </table>
                @else
                    <p class="text-muted mb-0">No primary guardian linked.</p>
                @endif

                @if(isset($guardians) && $guardians->count())
                    <div class="mt-3">
                        <strong>Other Guardians:</strong>
                        @foreach($guardians as $g)
                            <div class="mt-2">
                                <span class="badge badge-light">{{ $g->name }}</span> — {{ $g->relationship ?: 'Guardian' }}
                                @if($g->phone)<small class="text-muted"> ({{ $g->phone }})</small>@endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-white header-elements-inline">
                <h6 class="card-title"><i class="icon-phone mr-2 text-warning"></i>Contact / Emergency</h6>
            </div>
            <div class="card-body">
                <table class="table table-bordered mb-0">
                    <tbody>
                        <tr>
                            <td class="font-weight-bold" width="40%">Address</td>
                            <td>{{ $sr->user->address ?: 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Phone</td>
                            <td>{{ $sr->user->phone ?: 'N/A' }} {{ $sr->user->phone2 ? ' / ' . $sr->user->phone2 : '' }}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Email</td>
                            <td>{{ $sr->user->email ?: 'N/A' }}</td>
                        </tr>
                        @if($sr->user->bg_id)
                            <tr>
                                <td class="font-weight-bold">Blood Group</td>
                                <td>{{ $sr->user->blood_group ? $sr->user->blood_group->name : 'N/A' }}</td>
                            </tr>
                        @endif
                        @if($sr->user->nal_id)
                            <tr>
                                <td class="font-weight-bold">Nationality</td>
                                <td>{{ $sr->user->nationality ? $sr->user->nationality->name : 'N/A' }}</td>
                            </tr>
                        @endif
                        @if($sr->user->state_id)
                            <tr>
                                <td class="font-weight-bold">State</td>
                                <td>{{ $sr->user->state ? $sr->user->state->name : 'N/A' }}</td>
                            </tr>
                        @endif
                        @if($sr->user->lga_id)
                            <tr>
                                <td class="font-weight-bold">LGA</td>
                                <td>{{ $sr->user->lga ? $sr->user->lga->name : 'N/A' }}</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>