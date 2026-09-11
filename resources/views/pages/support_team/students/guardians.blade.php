@extends('layouts.master')
@section('page_title', 'Parents & Guardians')
@section('content')

<div class="row">
    <div class="col-md-5">
        <div class="card">
            <div class="card-header bg-white header-elements-inline">
                <h6 class="card-title">Add New Guardian</h6>
            </div>
            <form method="post" action="{{ route('students.guardians.store') }}" autocomplete="off">
                @csrf
                <div class="card-body">
                    <div class="form-group">
                        <label>Full Name <span class="text-danger">*</span></label>
                        <input required type="text" name="name" value="{{ old('name') }}" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Phone <span class="text-danger">*</span></label>
                        <input required type="text" name="phone" value="{{ old('phone') }}" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Gender</label>
                        <select name="gender" class="select form-control">
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Save Guardian</button>
                </div>
            </form>
        </div>

        <div class="card">
            <div class="card-header bg-white header-elements-inline">
                <h6 class="card-title">Link Guardian to Student</h6>
            </div>
            <form method="post" action="{{ route('students.guardians.link') }}" autocomplete="off">
                @csrf
                <div class="card-body">
                    <div class="form-group">
                        <label>Student <span class="text-danger">*</span></label>
                        <select required name="student_id" class="select-search form-control">
                            <option value="">Select Student</option>
                            @foreach($students as $s)
                                <option value="{{ $s->id }}">{{ $s->user->name.' - '.$s->my_class->name.' ('.$s->adm_no.')' }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Guardian <span class="text-danger">*</span></label>
                        <select required name="user_id" class="select-search form-control">
                            <option value="">Select Guardian</option>
                            @foreach($guardians as $g)
                                <option value="{{ $g->id }}">{{ $g->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Relationship <span class="text-danger">*</span></label>
                        <select required name="relationship" class="select form-control">
                            <option value="">Choose..</option>
                            @foreach($statuses as $r)
                                <option value="{{ $r }}">{{ $r }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-check">
                            <input type="checkbox" name="is_primary" value="1" class="form-check-input-styled" data-fouc>
                            <span class="form-check-label">Set as Primary Guardian</span>
                        </label>
                    </div>
                    <button type="submit" class="btn btn-primary">Link Student</button>
                </div>
            </form>
        </div>
    </div>

    <div class="col-md-7">
        <div class="card">
            <div class="card-header bg-white header-elements-inline">
                <h6 class="card-title">Guardians & Linked Students</h6>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <thead class="thead-light">
                        <tr>
                            <th>Guardian</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Linked Students</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($guardians as $g)
                            <tr>
                                <td>{{ $g->name }}</td>
                                <td>{{ $g->phone }}</td>
                                <td>{{ $g->email ?: '-' }}</td>
                                <td>
                                    @foreach($students->filter(function($s) use ($g) {
                                        return $s->my_parent_id == $g->id || $s->guardians->contains('user_id', $g->id);
                                    }) as $s)
                                        <a href="{{ route('students.show', Qs::hash($s->id)) }}">{{ $s->user->name }}</a> ({{ $s->my_class->name }})<br>
                                    @endforeach
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        @if($links->count())
            <div class="card">
                <div class="card-header bg-white header-elements-inline">
                    <h6 class="card-title">All Guardian Links</h6>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead class="thead-light">
                            <tr>
                                <th>Student</th>
                                <th>Guardian</th>
                                <th>Relationship</th>
                                <th>Primary</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($links as $l)
                                <tr>
                                    <td>{{ $l->student ? $l->student->name : 'N/A' }}</td>
                                    <td>{{ $l->user ? $l->user->name : 'N/A' }}</td>
                                    <td>{{ $l->relationship }}</td>
                                    <td>
                                        @if($l->is_primary)
                                            <span class="badge badge-success">Yes</span>
                                        @else
                                            <span class="badge badge-secondary">No</span>
                                        @endif
                                    </td>
                                    <td>
                                        <form class="d-inline" method="post" action="{{ route('students.guardians.destroy', $l->id) }}" onsubmit="return confirm('Remove this guardian link?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">Remove</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection