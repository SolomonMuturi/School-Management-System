@extends('layouts.master')
@section('page_title', 'Student Discipline - '.$sr->user->name)
@section('content')

<div class="row">
    <div class="col-md-5">
        <div class="card">
            <div class="card-header bg-white header-elements-inline">
                <h6 class="card-title">Add Discipline Record</h6>
                <div class="header-elements">
                    <a href="{{ route('students.show', Qs::hash($sr->id)) }}" class="btn btn-secondary btn-sm">Back to Profile</a>
                </div>
            </div>
            <form method="post" action="{{ route('students.discipline.store', Qs::hash($sr->id)) }}" autocomplete="off">
                @csrf
                <div class="card-body">
                    <div class="form-group">
                        <label>Incident Type <span class="text-danger">*</span></label>
                        <input required type="text" name="incident_type" class="form-control" placeholder="e.g. Lateness, Fight, Disobedience">
                    </div>
                    <div class="form-group">
                        <label>Date <span class="text-danger">*</span></label>
                        <input required type="date" name="date" value="{{ date('Y-m-d') }}" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Description <span class="text-danger">*</span></label>
                        <textarea required name="description" rows="2" class="form-control"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Action Taken</label>
                        <input type="text" name="action_taken" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Warning Level <span class="text-danger">*</span></label>
                        <select required name="warning_level" class="select form-control">
                            <option value="Minor">Minor</option>
                            <option value="Warning">Warning</option>
                            <option value="Major">Major</option>
                            <option value="Severe">Severe</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Notes</label>
                        <input type="text" name="notes" class="form-control" maxlength="200">
                    </div>
                    <button type="submit" class="btn btn-primary">Save Record</button>
                </div>
            </form>
        </div>
    </div>

    <div class="col-md-7">
        <div class="card">
            <div class="card-header bg-white">
                <h6 class="card-title">{{ $sr->user->name }} - Discipline History</h6>
            </div>
            <div class="card-body">
                @if($discipline->count())
                    <table class="table table-bordered table-striped">
                        <thead class="thead-light">
                            <tr>
                                <th>Date</th>
                                <th>Incident</th>
                                <th>Warning</th>
                                <th>Action Taken</th>
                                <th>Description</th>
                                <th></th>
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
                                    <td>{{ $rec->description }}</td>
                                    <td>
                                        <form method="post" action="{{ route('students.discipline.destroy', [Qs::hash($sr->id), $rec->id]) }}" onsubmit="return confirm('Delete this record?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-muted mb-0">No discipline records.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection