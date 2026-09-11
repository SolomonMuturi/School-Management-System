@extends('layouts.master')
@section('page_title', 'Student List')
@section('content')

<div class="card">
    <div class="card-header bg-white header-elements-inline">
        <h6 class="card-title">All Students</h6>
    </div>

    <form method="get" action="{{ route('students.index') }}" autocomplete="off">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Search</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Name or Student ID" class="form-control">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Class</label>
                        <select name="class_id" class="select-search form-control">
                            <option value="">All Classes</option>
                            @foreach($my_classes as $c)
                                <option {{ request('class_id') == $c->id ? 'selected' : '' }} value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="select form-control">
                            <option value="">Current</option>
                            @foreach($statuses as $st)
                                <option {{ request('status') == $st ? 'selected' : '' }} value="{{ $st }}">{{ ucfirst($st) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>Admission Year</label>
                        <select name="year" class="select form-control">
                            <option value="">All Years</option>
                            @foreach($years as $y)
                                <option {{ request('year') == $y ? 'selected' : '' }} value="{{ $y }}">{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group mt-4">
                        <button type="submit" class="btn btn-primary">Filter</button>
                        <a href="{{ route('students.index') }}" class="btn btn-secondary">Reset</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<div class="card">
    <div class="card-body">
        <table id="student-list" class="table table-bordered table-striped">
            <thead class="thead-light">
                <tr>
                    <th>Photo</th>
                    <th>Name</th>
                    <th>Student ID / Adm No</th>
                    <th>Class</th>
                    <th>Year Admitted</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($students as $s)
                    <tr>
                        <td><img src="{{ $s->user->photo }}" class="rounded-circle" style="width:40px; height:40px" alt="photo"></td>
                        <td>{{ $s->user->name }}</td>
                        <td>{{ $s->adm_no }}</td>
                        <td>{{ $s->my_class->name }}</td>
                        <td>{{ $s->year_admitted }}</td>
                        <td>
                            @if($s->status == 'active')
                                <span class="badge badge-success">{{ ucfirst($s->status) }}</span>
                            @elseif(in_array($s->status, ['suspended', 'withdrawn']))
                                <span class="badge badge-danger">{{ ucfirst($s->status) }}</span>
                            @elseif($s->status == 'graduated')
                                <span class="badge badge-primary">{{ ucfirst($s->status) }}</span>
                            @else
                                <span class="badge badge-secondary">{{ $s->status ? ucfirst($s->status) : 'Active' }}</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('students.show', Qs::hash($s->id)) }}" class="btn btn-secondary btn-sm">Profile</a>
                            @if(Qs::userIsTeamSA())
                                <a href="{{ route('students.edit', Qs::hash($s->id)) }}" class="btn btn-primary btn-sm">Edit</a>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection