@extends('layouts.master')
@section('page_title', 'Assignments & Homework')
@section('content')

    <div class="card">
        <div class="card-header bg-white header-elements-inline">
            <h6 class="card-title">Assignments &amp; Homework</h6>
            {!! Qs::getPanelOptions() !!}
        </div>

        <div class="card-body">
            <ul class="nav nav-tabs nav-tabs-highlight">
                <li class="nav-item"><a href="#assignments" class="nav-link active" data-toggle="tab">All Assignments</a></li>
                @if(Qs::userIsTeamSAT())
                    <li class="nav-item"><a href="#add-assignment" class="nav-link" data-toggle="tab"><i class="icon-plus2"></i> Create Assignment</a></li>
                @endif
            </ul>

            <div class="tab-content">
                <div class="tab-pane fade show active" id="assignments">
                    <table class="table datatable-responsive">
                        <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Title</th>
                            <th>Subject</th>
                            <th>Class</th>
                            <th>Teacher</th>
                            <th>Due Date</th>
                            <th>Max Marks</th>
                            <th>Submissions</th>
                            <th>Year</th>
                            <th>Term</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($assignments_hm as $a)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <a href="{{ route('academic.homework.show', $a->id) }}"><strong>{{ $a->title }}</strong></a>
                                    @if($a->instructions)
                                        <div class="text-muted"><small>{{ Str::limit($a->instructions, 60) }}</small></div>
                                    @endif
                                </td>
                                <td>{{ $a->subject->name }}</td>
                                <td>{{ $a->myClass->name }}</td>
                                <td>{{ $a->teacher->name }}</td>
                                <td>{{ $a->due_date }}</td>
                                <td>{{ $a->max_marks }}</td>
                                <td>
                                    <span class="badge badge-info">{{ $a->submissions->count() }}</span>
                                    <a href="{{ route('academic.homework.show', $a->id) }}">view / grade</a>
                                </td>
                                <td>{{ $a->academicYear ? $a->academicYear->name : '-' }}</td>
                                <td>{{ $a->academicTerm ? $a->academicTerm->name : '-' }}</td>
                                <td class="text-center">
                                    <form method="post" action="{{ route('academic.homework.destroy', $a->id) }}">
                                        @csrf @method('delete')
                                        <button type="submit" class="btn btn-xs btn-link text-danger"><i class="icon-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                @if(Qs::userIsTeamSAT())
                <div class="tab-pane fade" id="add-assignment">
                    <form method="post" action="{{ route('academic.homework.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Title: </label>
                                    <input type="text" name="title" class="form-control" placeholder="Assignment / Homework Title" required>
                                </div>
                            </div>
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
                                    <select name="my_class_id" class="select-search form-control" data-placeholder="Select Class" required>
                                        <option value=""></option>
                                        @foreach($classes as $mc)
                                            <option value="{{ $mc->id }}">{{ $mc->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Due Date: </label>
                                    <input type="date" name="due_date" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Max Marks: </label>
                                    <input type="number" name="max_marks" class="form-control" value="100" min="1">
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
                                            <option {{ $t->id == $current_term->id ? 'selected' : '' }} value="{{ $t->id }}">{{ $t->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Instructions: </label>
                                    <textarea name="instructions" class="form-control" rows="3" placeholder="Instructions for students..."></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Attachment (optional): </label>
                                    <input type="file" name="attachment" class="form-input-styled" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png" data-fouc>
                                </div>
                            </div>
                        </div>
                        <button class="btn btn-primary">Create Assignment</button>
                    </form>
                </div>
                @endif
            </div>
        </div>
    </div>

@endsection