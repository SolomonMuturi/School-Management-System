@extends('layouts.master')
@section('page_title', 'Class Details - '.$c->name)
@section('content')

    <div class="card">
        <div class="card-header header-elements-inline">
            <h6 class="card-title">{{ $c->name }} <span class="text-muted">({{ $c->code ?: 'No code' }})</span></h6>
            {!! Qs::getPanelOptions() !!}
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="card bg-secondary-400">
                        <div class="card-body text-center">
                            <h3 class="font-weight-bold mb-0">{{ $students->count() }}</h3>
                            <span>Active Students</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-primary-400">
                        <div class="card-body text-center">
                            <h3 class="font-weight-bold mb-0">{{ $subjects->count() }}</h3>
                            <span>Subjects</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success-400">
                        <div class="card-body text-center">
                            <h3 class="font-weight-bold mb-0">{{ $c->teacher ? $c->teacher->name : 'None' }}</h3>
                            <span>Class Teacher</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-teal-400">
                        <div class="card-body text-center">
                            <h3 class="font-weight-bold mb-0">{{ $c->class_type ? $c->class_type->name : '-' }}</h3>
                            <span>Class Type</span>
                        </div>
                    </div>
                </div>
            </div>

            @if(Qs::userIsTeamSAT())
            <div class="card mb-3">
                <div class="card-header header-elements-inline">
                    <h6 class="card-title">Assign Class Teacher</h6>
                </div>
                <div class="card-body">
                    <form method="post" action="{{ route('classes.assign_teacher', $c->id) }}" class="form-inline">
                        @csrf
                        <div class="form-group mx-sm-3">
                            <select name="teacher_id" id="teacher_id" class="form-control select-search" style="min-width: 260px;">
                                <option value="">Select Teacher</option>
                                @foreach($teachers as $t)
                                    <option {{ $c->teacher_id == $t->id ? 'selected' : '' }} value="{{ $t->id }}">{{ $t->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Class Teacher</button>
                    </form>
                </div>
            </div>
            @endif

            <div class="row">
                <div class="col-md-7">
                    <div class="card">
                        <div class="card-header header-elements-inline">
                            <h6 class="card-title">Students in {{ $c->name }}</h6>
                        </div>
                        <div class="card-body">
                            <table class="table datatable-responsive">
                                <thead>
                                <tr>
                                    <th>S/N</th>
                                    <th>Adm No</th>
                                    <th>Name</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($students as $s)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $s->adm_no }}</td>
                                        <td>{{ $s->user->name }}</td>
                                        <td><a href="{{ route('students.show', Qs::hash($s->id)) }}" class="btn btn-xs btn-secondary">Profile</a></td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-5">
                    <div class="card">
                        <div class="card-header header-elements-inline">
                            <h6 class="card-title">Subjects</h6>
                        </div>
                        <div class="card-body">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>S/N</th>
                                    <th>Subject</th>
                                    <th>Teacher</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($subjects as $i => $sub)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $sub->name }}</td>
                                        <td>{{ $sub->teacher ? $sub->teacher->name : '-' }}</td>
                                    </tr>
                                @endforeach
                                @if(!$subjects->count())
                                    <tr><td colspan="3" class="text-center text-muted">No subjects assigned yet</td></tr>
                                @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection