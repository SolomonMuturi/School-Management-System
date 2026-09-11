@extends('layouts.master')
@section('page_title', 'Lessons')
@section('content')

    <div class="card">
        <div class="card-header bg-white header-elements-inline">
            <h6 class="card-title">Lessons &amp; Lesson Plans</h6>
            {!! Qs::getPanelOptions() !!}
        </div>

        <div class="card-body">
            <ul class="nav nav-tabs nav-tabs-highlight">
                <li class="nav-item"><a href="#lessons" class="nav-link active" data-toggle="tab">Lesson Log</a></li>
                @if(Qs::userIsTeamSAT())
                    <li class="nav-item"><a href="#add-lesson" class="nav-link" data-toggle="tab"><i class="icon-plus2"></i> Add Lesson</a></li>
                @endif
            </ul>

            <div class="tab-content">
                <div class="tab-pane fade show active" id="lessons">
                    <table class="table datatable-responsive">
                        <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Lesson / Topic</th>
                            <th>Subject</th>
                            <th>Class</th>
                            <th>Teacher</th>
                            <th>Date</th>
                            <th>Year</th>
                            <th>Term</th>
                            @if(Qs::userIsTeamSAT())<th>Action</th>@endif
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($lessons as $l)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <strong>{{ $l->topic }}</strong>
                                    @if($l->learning_objectives)
                                        <div class="text-muted"><small><i class="icon-target"></i> {{ $l->learning_objectives }}</small></div>
                                    @endif
                                </td>
                                <td>{{ $l->subject->name }}</td>
                                <td>{{ $l->myClass->name }}</td>
                                <td>{{ $l->teacher->name }}</td>
                                <td>{{ $l->lesson_date }}</td>
                                <td>{{ $l->academicYear ? $l->academicYear->name : '-' }}</td>
                                <td>{{ $l->academicTerm ? $l->academicTerm->name : '-' }}</td>
                                @if(Qs::userIsTeamSAT())
                                <td class="text-center">
                                    <form method="post" action="{{ route('academic.lessons.destroy', $l->id) }}">
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

                @if(Qs::userIsTeamSAT())
                <div class="tab-pane fade" id="add-lesson">
                    <form method="post" action="{{ route('academic.lessons.store') }}">
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
                                    <label>Lesson Date: </label>
                                    <input type="date" name="lesson_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Topic: </label>
                                    <input type="text" name="topic" class="form-control" placeholder="Lesson Topic" required>
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
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Lesson Plan / Teaching Notes: </label>
                                    <textarea name="plan" class="form-control" rows="4" placeholder="Lesson plan steps..."></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Learning Objectives: </label>
                                    <textarea name="learning_objectives" class="form-control" rows="4" placeholder="By the end of the lesson students should be able to..."></textarea>
                                </div>
                            </div>
                        </div>
                        <button class="btn btn-primary">Save Lesson</button>
                    </form>
                </div>
                @endif
            </div>
        </div>
    </div>

@endsection