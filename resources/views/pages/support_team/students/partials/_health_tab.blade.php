<div class="card">
    <div class="card-header bg-white header-elements-inline">
        <h6 class="card-title">Health Information</h6>
        @if(Qs::userIsTeamSAT())
            <div class="header-elements">
                <a href="{{ route('students.health', Qs::hash($sr->id)) }}" class="btn btn-primary btn-sm">Manage Health</a>
            </div>
        @endif
    </div>
    <div class="card-body">
        @if($health)
            <table class="table table-bordered">
                <tbody>
                    <tr><td class="font-weight-bold" width="30%">Allergies</td><td>{{ $health->allergies ?: 'None' }}</td></tr>
                    <tr><td class="font-weight-bold">Medical Conditions</td><td>{{ $health->medical_conditions ?: 'None' }}</td></tr>
                    <tr><td class="font-weight-bold">Medications</td><td>{{ $health->medications ?: 'None' }}</td></tr>
                    <tr><td class="font-weight-bold">Emergency Contact</td><td>{{ $health->emergency_contact_name ?: 'N/A' }}{{ $health->emergency_contact_phone ? ' ('.$health->emergency_contact_phone.')' : '' }}</td></tr>
                    <tr><td class="font-weight-bold">Relationship</td><td>{{ $health->emergency_contact_relationship ?: 'N/A' }}</td></tr>
                    @if($health->health_notes)
                        <tr><td class="font-weight-bold">Notes</td><td>{{ $health->health_notes }}</td></tr>
                    @endif
                </tbody>
            </table>
        @else
            <p class="text-muted mb-0">No health information recorded.</p>
        @endif
    </div>
</div>