<div class="card">
    <div class="card-header bg-white header-elements-inline">
        <h6 class="card-title">Basic & Admission Information</h6>
    </div>
    <div class="card-body">
        <table class="table table-bordered">
            <tbody>
                <tr>
                    <td class="font-weight-bold" width="25%">Name</td>
                    <td>{{ $sr->user->name }}</td>
                </tr>
                <tr>
                    <td class="font-weight-bold">Student ID / Adm No</td>
                    <td>{{ $sr->adm_no }}</td>
                </tr>
                <tr>
                    <td class="font-weight-bold">Current Class</td>
                    <td>{{ $sr->my_class->name }}</td>
                </tr>
                <tr>
                    <td class="font-weight-bold">Student Status</td>
                    <td>
                        @if($sr->status == 'active')
                            <span class="badge badge-success">{{ ucfirst($sr->status) }}</span>
                        @elseif($sr->status == 'graduated')
                            <span class="badge badge-primary">{{ ucfirst($sr->status) }}</span>
                        @elseif(in_array($sr->status, ['suspended', 'withdrawn']))
                            <span class="badge badge-danger">{{ ucfirst($sr->status) }}</span>
                        @else
                            <span class="badge badge-secondary">{{ ucfirst($sr->status ?: 'Active') }}</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="font-weight-bold">Admission Number</td>
                    <td>{{ $sr->adm_no }}</td>
                </tr>
                <tr>
                    <td class="font-weight-bold">Admission Date</td>
                    <td>{{ $sr->admission_date ? \Carbon\Carbon::parse($sr->admission_date)->format('d M Y') : 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="font-weight-bold">Admission Class</td>
                    <td>{{ $sr->my_class ? $sr->my_class->name : 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="font-weight-bold">Academic Year / Session</td>
                    <td>{{ $sr->session }}</td>
                </tr>
                <tr>
                    <td class="font-weight-bold">Year Admitted</td>
                    <td>{{ $sr->year_admitted }}</td>
                </tr>
                <tr>
                    <td class="font-weight-bold">Previous School</td>
                    <td>{{ $sr->previous_school ?: 'N/A' }}</td>
                </tr>
                @if(isset($sr->my_parent_id) && $sr->my_parent_id)
                    <tr>
                        <td class="font-weight-bold">Primary Guardian</td>
                        <td><a href="{{ route('users.show', Qs::hash($sr->my_parent_id)) }}">{{ $sr->my_parent->name }}</a></td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white header-elements-inline">
        <h6 class="card-title">Personal & Contact Information</h6>
    </div>
    <div class="card-body">
        <table class="table table-bordered">
            <tbody>
                <tr>
                    <td class="font-weight-bold" width="25%">Gender</td>
                    <td>{{ $sr->user->gender }}</td>
                </tr>
                <tr>
                    <td class="font-weight-bold">Date of Birth</td>
                    <td>{{ $sr->user->dob }}</td>
                </tr>
                <tr>
                    <td class="font-weight-bold">Address</td>
                    <td>{{ $sr->user->address ?: 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="font-weight-bold">Phone</td>
                    <td>{{ $sr->user->phone ? $sr->user->phone.' '.$sr->user->phone2 : 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="font-weight-bold">Email</td>
                    <td>{{ $sr->user->email ?: 'N/A' }}</td>
                </tr>
                @if($sr->user->bg_id)
                    <tr>
                        <td class="font-weight-bold">Blood Group</td>
                        <td>{{ $sr->user->blood_group->name }}</td>
                    </tr>
                @endif
                @if($sr->user->nal_id)
                    <tr>
                        <td class="font-weight-bold">Nationality</td>
                        <td>{{ $sr->user->nationality->name }}</td>
                    </tr>
                @endif
                @if($sr->user->state_id)
                    <tr>
                        <td class="font-weight-bold">State</td>
                        <td>{{ $sr->user->state->name }}</td>
                    </tr>
                @endif
                @if($sr->user->lga_id)
                    <tr>
                        <td class="font-weight-bold">LGA</td>
                        <td>{{ $sr->user->lga->name }}</td>
                    </tr>
                @endif
                @if($sr->house)
                    <tr>
                        <td class="font-weight-bold">Sport House</td>
                        <td>{{ $sr->house }}</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>