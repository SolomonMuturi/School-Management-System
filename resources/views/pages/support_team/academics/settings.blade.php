@extends('layouts.master')
@section('page_title', 'Academic Settings')
@section('content')

    <div class="card">
        <div class="card-header bg-white header-elements-inline">
            <h6 class="card-title">Academic Settings</h6>
            {!! Qs::getPanelOptions() !!}
        </div>

        <div class="card-body">
            <ul class="nav nav-tabs nav-tabs-highlight">
                <li class="nav-item"><a href="#general" class="nav-link active" data-toggle="tab">General</a></li>
                <li class="nav-item"><a href="#grading" class="nav-link" data-toggle="tab">Grading System</a></li>
                <li class="nav-item"><a href="#refs" class="nav-link" data-toggle="tab">Subjects / Years / Terms</a></li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane fade show active" id="general">
                    @if(Qs::userIsTeamSA())
                    <form method="post" action="{{ route('academic.settings.store') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <label class="font-weight-semibold">
                                    <input type="checkbox" name="enable_ranking" class="form-check-input-styled" data-fouc {{ !empty($settings['enable_ranking']) ? 'checked' : '' }}>
                                    Enable Student Ranking
                                </label>
                            </div>
                            <div class="col-md-4">
                                <label class="font-weight-semibold">
                                    <input type="checkbox" name="enable_teacher_comments" class="form-check-input-styled" data-fouc {{ !empty($settings['enable_teacher_comments']) ? 'checked' : '' }}>
                                    Enable Teacher Comments
                                </label>
                            </div>
                            <div class="col-md-4">
                                <label class="font-weight-semibold">
                                    <input type="checkbox" name="report_card_position" class="form-check-input-styled" data-fouc {{ !empty($settings['report_card_position']) ? 'checked' : '' }}>
                                    Show Position on Report Card
                                </label>
                            </div>
                        </div>
                        <button class="btn btn-primary">Save Settings</button>
                    </form>
                    @else
                        <div class="alert alert-info">Grading / ranking settings are managed by the Admin.</div>
                    @endif
                </div>

                <div class="tab-pane fade" id="grading">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-2">
                                <a href="{{ route('grades.index') }}" class="btn btn-primary btn-sm">Manage Grades</a>
                            </div>
                            <table class="table table-bordered">
                                <thead>
                                <tr><th>Grade</th><th>From</th><th>To</th><th>Remark</th></tr>
                                </thead>
                                <tbody>
                                @foreach($grades as $g)
                                    <tr>
                                        <td>{{ $g->name }}</td>
                                        <td>{{ $g->mark_from }}</td>
                                        <td>{{ $g->mark_to }}</td>
                                        <td>{{ $g->remark }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="refs">
                    <div class="row">
                        <div class="col-md-4">
                            <h6 class="font-weight-semibold">Subjects</h6>
                            <a href="{{ route('subjects.index') }}" class="btn btn-sm btn-outline-primary mb-2">Manage Subjects</a>
                            <ul class="list-group">
                                @foreach($subjects as $s)
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span>{{ $s->name }}</span>
                                        <span class="badge badge-light">{{ $s->my_class ? $s->my_class->name : 'All' }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="col-md-4">
                            <h6 class="font-weight-semibold">Academic Years</h6>
                            <a href="{{ route('academic.years') }}" class="btn btn-sm btn-outline-primary mb-2">Manage Years &amp; Terms</a>
                            <ul class="list-group">
                                @foreach($years as $y)
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span>{{ $y->name }}</span>
                                        @if($y->is_current)<span class="badge badge-success">Current</span>@endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="col-md-4">
                            <h6 class="font-weight-semibold">Terms</h6>
                            <ul class="list-group">
                                @foreach($terms as $t)
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span>{{ $t->name }}</span>
                                        @if($t->is_current)<span class="badge badge-info">Current</span>@endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection