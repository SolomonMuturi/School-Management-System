@extends('layouts.master')
@section('page_title', 'Student Health - '.$sr->user->name)
@section('content')

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-white header-elements-inline">
                <h6 class="card-title">{{ $sr->user->name }} - Health Information</h6>
                <div class="header-elements">
                    <a href="{{ route('students.show', Qs::hash($sr->id)) }}" class="btn btn-secondary btn-sm">Back to Profile</a>
                </div>
            </div>
            <form method="post" action="{{ route('students.health.store', Qs::hash($sr->id)) }}" autocomplete="off">
                @csrf
                <div class="card-body">
                    <div class="form-group">
                        <label>Allergies</label>
                        <input type="text" name="allergies" value="{{ $health ? $health->allergies : '' }}" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Medical Conditions</label>
                        <input type="text" name="medical_conditions" value="{{ $health ? $health->medical_conditions : '' }}" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Medications</label>
                        <input type="text" name="medications" value="{{ $health ? $health->medications : '' }}" class="form-control">
                    </div>
                    <hr>
                    <h6 class="font-weight-semibold">Emergency Contact</h6>
                    <div class="form-group">
                        <label>Emergency Contact Name</label>
                        <input type="text" name="emergency_contact_name" value="{{ $health ? $health->emergency_contact_name : '' }}" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Emergency Contact Phone</label>
                        <input type="text" name="emergency_contact_phone" value="{{ $health ? $health->emergency_contact_phone : '' }}" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Relationship to Student</label>
                        <input type="text" name="emergency_contact_relationship" value="{{ $health ? $health->emergency_contact_relationship : '' }}" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Health Notes</label>
                        <textarea name="health_notes" rows="2" class="form-control">{{ $health ? $health->health_notes : '' }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Save Health Info</button>
                </div>
            </form>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-white">
                <h6 class="card-title">Current Health Record</h6>
            </div>
            <div class="card-body">
                @if($health)
                    <table class="table table-bordered">
                        <tbody>
                            <tr><td class="font-weight-bold" width="35%">Blood Group</td><td>{{ $sr->user->blood_group ? $sr->user->blood_group->name : 'N/A' }}</td></tr>
                            <tr><td class="font-weight-bold">Allergies</td><td>{{ $health->allergies ?: 'None' }}</td></tr>
                            <tr><td class="font-weight-bold">Medical Conditions</td><td>{{ $health->medical_conditions ?: 'None' }}</td></tr>
                            <tr><td class="font-weight-bold">Medications</td><td>{{ $health->medications ?: 'None' }}</td></tr>
                            <tr><td class="font-weight-bold">Emergency Contact</td><td>{{ $health->emergency_contact_name ?: 'N/A' }} {{ $health->emergency_contact_phone ? '('.$health->emergency_contact_phone.')' : '' }}</td></tr>
                            <tr><td class="font-weight-bold">Relationship</td><td>{{ $health->emergency_contact_relationship ?: 'N/A' }}</td></tr>
                            <tr><td class="font-weight-bold">Notes</td><td>{{ $health->health_notes ?: 'N/A' }}</td></tr>
                        </tbody>
                    </table>
                @else
                    <p class="text-muted mb-0">No health information recorded yet.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection