@extends('layouts.master')
@section('page_title', 'Teacher & Subject Assignment')
@section('content')

    <div class="card">
        <div class="card-header bg-white header-elements-inline">
            <h6 class="card-title">Teacher &amp; Subject Assignment</h6>
            {!! Qs::getPanelOptions() !!}
        </div>

        <div class="card-body">
            <ul class="nav nav-tabs nav-tabs-highlight">
                <li class="nav-item"><a href="#assignments" class="nav-link active" data-toggle="tab">Teaching Assignments</a></li>
                <li class="nav-item"><a href="#teachers" class="nav-link" data-toggle="tab">By Teacher</a></li>
                @if(Qs::userIsTeamSA())
                    <li class="nav-item"><a href="#add-assignment" class="nav-link" data-toggle="tab"><i class="icon-plus2"></i> Assign Teacher</a></li>
                @endif
            </ul>

            <div class="tab-content">
                <div class="tab-pane fade show active" id="assignments">
                    <table class="table datatable-responsive">
                        <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Teacher</th>
                            <th>Subject</th>
                            <th>Class</th>
                            <th>Year</th>
                            <th>Term</th>
                            <th>Status</th>
                            @if(Qs::userIsTeamSA())<th>Action</th>@endif
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($assignments_ta as $ta)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $ta->teacher->name }}</td>
                                <td>{{ $ta->subject->name }}</td>
                                <td>{{ $ta->myClass ? $ta->myClass->name : 'All' }}</td>
                                <td>{{ $ta->academicYear ? $ta->academicYear->name : 'Current' }}</td>
                                <td>{{ $ta->academicTerm ? $ta->academicTerm->name : '' }}</td>
                                <td>
                                    <span class="badge {{ $ta->status == 'active' ? 'badge-success' : 'badge-secondary' }}">{{ $ta->status }}</span>
                                </td>
                                @if(Qs::userIsTeamSA())
                                <td class="text-center">
                                    <form method="post" action="{{ route('academic.assign.destroy', $ta->id) }}">
                                        @csrf @method('delete')
                                        <button type="submit" class="btn btn-xs btn-link text-danger"><i class="icon-trash"></i></button>
                                    </form>
                                </td>
                                @endif
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="tab-pane fade" id="teachers">
                    @foreach($teachers as $t)
                        <div class="row">
                            <div class="col-md-12">
                                <h6 class="font-weight-semibold text-primary">{{ $t->name }}</h6>
                                @php $tsubs = \App\Models\Subject::where('teacher_id', $t->id)->with('my_class')->get(); @endphp
                                @if($tsubs->count())
                                    <table class="table table-sm table-bordered">
                                        <thead>
                                        <tr><th>Subject</th><th>Class (Subject)</th></tr>
                                        </thead>
                                        <tbody>
                                        @foreach($tsubs as $s)
                                            <tr>
                                                <td>{{ $s->name }}</td>
                                                <td>{{ $s->my_class ? $s->my_class->name : 'All' }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                @else
                                    <p class="text-muted">No subjects assigned.</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                @if(Qs::userIsTeamSA())
                <div class="tab-pane fade" id="add-assignment">
                    <form method="post" action="{{ route('academic.assign.store') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Teacher: </label>
                                    <select name="teacher_id" class="select-search form-control" data-placeholder="Select Teacher" required>
                                        <option value=""></option>
                                        @foreach($teachers as $t)
                                            <option value="{{ $t->id }}">{{ $t->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Subject: </label>
                                    <select name="subject_id" class="select-search form-control" data-placeholder="Select Subject" required>
                                        <option value=""></option>
                                        @foreach($subjects as $s)
                                            <option value="{{ $s->id }}">{{ $s->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Class: </label>
                                    <select name="my_class_id" class="select form-control" data-placeholder="Select Class">
                                        <option value=""></option>
                                        @foreach($classes as $mc)
                                            <option value="{{ $mc->id }}">{{ $mc->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Academic Year: </label>
                                    <select name="academic_year_id" class="select form-control">
                                        @foreach($years as $y)
                                            <option {{ $y->id == $current_year->id ? 'selected' : '' }} value="{{ $y->id }}">{{ $y->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Academic Term: </label>
                                    <select name="academic_term_id" class="select form-control">
                                        @foreach($terms as $t)
                                            <option {{ $t->is_current ? 'selected' : '' }} value="{{ $t->id }}">{{ $t->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <button class="btn btn-primary">Assign Teacher</button>
                    </form>
                </div>
                @endif
            </div>
        </div>
    </div>

@endsection