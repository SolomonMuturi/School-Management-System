@extends('layouts.master')
@section('page_title', 'Student Activities - '.$sr->user->name)
@section('content')

<div class="row">
    <div class="col-md-5">
        <div class="card">
            <div class="card-header bg-white header-elements-inline">
                <h6 class="card-title">Add Activity</h6>
                <div class="header-elements">
                    <a href="{{ route('students.show', Qs::hash($sr->id)) }}" class="btn btn-secondary btn-sm">Back to Profile</a>
                </div>
            </div>
            <form method="post" action="{{ route('students.activities.store', Qs::hash($sr->id)) }}" autocomplete="off">
                @csrf
                <div class="card-body">
                    <div class="form-group">
                        <label>Activity Type <span class="text-danger">*</span></label>
                        <select required name="activity_type" class="select form-control">
                            <option value="Club">Club</option>
                            <option value="Sport">Sport</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Activity Name <span class="text-danger">*</span></label>
                        <input required type="text" name="activity_name" class="form-control" placeholder="e.g. Debate Club, Football">
                    </div>
                    <div class="form-group">
                        <label>Role</label>
                        <input type="text" name="role" class="form-control" placeholder="e.g. President, Captain, Member">
                    </div>
                    <div class="form-group">
                        <label>Session / Academic Year</label>
                        <select name="session" class="select-search form-control">
                            <option value="">Choose..</option>
                            @for($y = date('Y'); $y >= (date('Y') - 8); $y--)
                                @for($y2 = $y + 1; $y2 <= $y + 1; $y2++)
                                    <option value="{{ $y.'/'.$y2 }}">{{ $y.'/'.$y2 }}</option>
                                @endfor
                            @endfor
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Achievements</label>
                        <input type="text" name="achievements" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Notes</label>
                        <input type="text" name="notes" class="form-control" maxlength="200">
                    </div>
                    <button type="submit" class="btn btn-primary">Save Activity</button>
                </div>
            </form>
        </div>
    </div>

    <div class="col-md-7">
        <div class="card">
            <div class="card-header bg-white">
                <h6 class="card-title">{{ $sr->user->name }} - Participation History</h6>
            </div>
            <div class="card-body">
                @if($activities->count())
                    <table class="table table-bordered table-striped">
                        <thead class="thead-light">
                            <tr>
                                <th>Type</th>
                                <th>Name</th>
                                <th>Role</th>
                                <th>Session</th>
                                <th>Achievements</th>
                                <th></th>
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
                                    <td>
                                        <form method="post" action="{{ route('students.activities.destroy', [Qs::hash($sr->id), $act->id]) }}" onsubmit="return confirm('Remove this activity?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">Remove</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-muted mb-0">No activities recorded yet.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection