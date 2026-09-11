@extends('layouts.master')
@section('page_title', 'Curriculum')
@section('content')

    <div class="card">
        <div class="card-header bg-white header-elements-inline">
            <h6 class="card-title">Curriculum &amp; Programs</h6>
            {!! Qs::getPanelOptions() !!}
        </div>

        <div class="card-body">
            <ul class="nav nav-tabs nav-tabs-highlight">
                <li class="nav-item"><a href="#curriculums" class="nav-link active" data-toggle="tab">Curriculums</a></li>
                @if(Qs::userIsTeamSA())
                    <li class="nav-item"><a href="#add-curriculum" class="nav-link" data-toggle="tab"><i class="icon-plus2"></i> Add Curriculum</a></li>
                    <li class="nav-item"><a href="#add-topic" class="nav-link" data-toggle="tab"><i class="icon-plus2"></i> Add Topic</a></li>
                @endif
            </ul>

            <div class="tab-content">
                <div class="tab-pane fade show active" id="curriculums">
                    @foreach($curriculums as $cur)
                        <div class="card card-body">
                            <div class="d-flex justify-content-between flex-wrap">
                                <h5 class="mb-0">
                                    {{ $cur->name }}
                                    @if($cur->program)<span class="badge badge-dark">{{ $cur->program }}</span>@endif
                                    @if($cur->academicYear)<span class="badge badge-info">{{ $cur->academicYear->name }}</span>@endif
                                    @if($cur->academicTerm)<span class="badge badge-success">{{ $cur->academicTerm->name }}</span>@endif
                                    <span class="badge {{ $cur->status == 'active' ? 'badge-primary' : 'badge-secondary' }}">{{ $cur->status }}</span>
                                </h5>
                                <div class="list-icons">
                                    <div class="dropdown">
                                        <a href="#" class="list-icons-item" data-toggle="dropdown"><i class="icon-menu9"></i></a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            @if(Qs::userIsTeamSA())
                                            <form method="post" action="{{ route('academic.curriculum.subject.store') }}" class="dropdown-item pa-1">
                                                @csrf
                                                <input type="hidden" name="curriculum_id" value="{{ $cur->id }}">
                                                <div class="input-group input-group-sm">
                                                    <select name="subject_id" class="select form-control" data-placeholder="Add Subject">
                                                        @foreach($subjects as $s)
                                                            <option value="{{ $s->id }}">{{ $s->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    <select name="my_class_id" class="select form-control" data-placeholder="Class (optional)">
                                                        <option value=""></option>
                                                        @foreach($classes as $mc)
                                                            <option value="{{ $mc->id }}">{{ $mc->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    <span class="input-group-append"><button class="btn btn-primary btn-sm"><i class="icon-plus2"></i></button></span>
                                                </div>
                                            </form>
                                            <form method="post" action="{{ route('academic.curriculum.destroy', $cur->id) }}">
                                                @csrf @method('delete')
                                                <button type="submit" class="dropdown-item text-danger"><i class="icon-trash"></i> Delete</button>
                                            </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if($cur->description)
                                <p class="text-muted mb-2">{{ $cur->description }}</p>
                            @endif

                            <div class="row mt-2">
                                <div class="col-md-6">
                                    <h6 class="font-weight-semibold">Subjects ({{ $cur->subjects->count() }})</h6>
                                    @if($cur->subjects->count())
                                        <table class="table table-sm">
                                            <thead>
                                            <tr><th>Subject</th><th>Class</th><th></th></tr>
                                            </thead>
                                            <tbody>
                                            @foreach($cur->subjects as $sub)
                                                <tr>
                                                    <td>{{ $sub->name }}</td>
                                                    <td>{{ $sub->pivot->my_class_id ? \App\Models\MyClass::find($sub->pivot->my_class_id)->name : 'All' }}</td>
                                                    <td class="text-right">
                                                        @if(Qs::userIsTeamSA())
                                                        <form method="post" action="{{ route('academic.curriculum.subject.destroy', [$cur->id, $sub->id]) }}">
                                                            @csrf @method('delete')
                                                            <button type="submit" class="btn btn-xs btn-link text-danger"><i class="icon-x"></i></button>
                                                        </form>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    @else
                                        <p class="text-muted">No subjects attached.</p>
                                    @endif
                                </div>

                                <div class="col-md-6">
                                    <h6 class="font-weight-semibold">Topics &amp; Learning Objectives ({{ $cur->topics->count() }})</h6>
                                    @if($cur->topics->count())
                                        <table class="table table-sm">
                                            <thead>
                                            <tr><th>Topic</th><th>Subject</th><th>Term</th><th></th></tr>
                                            </thead>
                                            <tbody>
                                            @foreach($cur->topics as $tp)
                                                <tr>
                                                    <td>{{ $tp->topic }}
                                                        @if($tp->learning_objectives)
                                                            <div class="text-muted"><small>{{ Str::limit($tp->learning_objectives, 80) }}</small></div>
                                                        @endif
                                                    </td>
                                                    <td>{{ $tp->subject->name }}</td>
                                                    <td>{{ $tp->term }}</td>
                                                    <td class="text-right">
                                                        @if(Qs::userIsTeamSA())
                                                        <form method="post" action="{{ route('academic.curriculum.topic.destroy', $tp->id) }}">
                                                            @csrf @method('delete')
                                                            <button type="submit" class="btn btn-xs btn-link text-danger"><i class="icon-x"></i></button>
                                                        </form>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    @else
                                        <p class="text-muted">No topics yet.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if(Qs::userIsTeamSA())
                <div class="tab-pane fade" id="add-curriculum">
                    <form method="post" action="{{ route('academic.curriculum.store') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Curriculum / Program Name: </label>
                                    <input type="text" name="name" class="form-control" placeholder="e.g. 8-4-4 Curriculum" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Program: </label>
                                    <input type="text" name="program" class="form-control" placeholder="e.g. Junior Secondary">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Academic Year: </label>
                                    <select name="academic_year_id" class="select form-control" data-placeholder="Select Year">
                                        <option value=""></option>
                                        @foreach($years as $y)
                                            <option {{ $y->id == $current_year->id ? 'selected' : '' }} value="{{ $y->id }}">{{ $y->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Term: </label>
                                    <select name="academic_term_id" class="select form-control" data-placeholder="Select Term">
                                        <option value=""></option>
                                        @foreach($terms as $t)
                                            <option {{ $t->is_current ? 'selected' : '' }} value="{{ $t->id }}">{{ $t->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Status: </label>
                                    <select name="status" class="select form-control">
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Description: </label>
                                    <textarea name="description" class="form-control" rows="2"></textarea>
                                </div>
                            </div>
                        </div>
                        <button class="btn btn-primary">Save Curriculum</button>
                    </form>
                </div>

                <div class="tab-pane fade" id="add-topic">
                    <form method="post" action="{{ route('academic.curriculum.topic.store') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Curriculum: </label>
                                    <select name="curriculum_id" class="select form-control" data-placeholder="Select Curriculum" required>
                                        @foreach($curriculums as $cur)
                                            <option value="{{ $cur->id }}">{{ $cur->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Subject: </label>
                                    <select name="subject_id" class="select form-control" data-placeholder="Select Subject" required>
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
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Topic: </label>
                                    <input type="text" name="topic" class="form-control" placeholder="e.g. Fractions" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Term: </label>
                                    <select name="term" class="select form-control">
                                        <option value=""></option>
                                        @foreach($terms as $t)
                                            <option value="{{ $t->name }}">{{ $t->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Learning Objectives: </label>
                                    <textarea name="learning_objectives" class="form-control" rows="3" placeholder="What students should be able to do..."></textarea>
                                </div>
                            </div>
                        </div>
                        <button class="btn btn-primary">Save Topic</button>
                    </form>
                </div>
                @endif

            </div>
        </div>
    </div>

@endsection