@extends('layouts.master')
@section('page_title', 'Academic Dashboard')
@section('content')

    <div class="card">
        <div class="card-header bg-white header-elements-inline">
            <h6 class="card-title">Academic Dashboard</h6>
            {!! Qs::getPanelOptions() !!}
        </div>

        <div class="card-body">
            @include('pages.support_team.academics.partials._quick_stats')
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header header-elements-inline">
                    <h6 class="card-title">Upcoming Exams & Assessments</h6>
                </div>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                        <tr><th>Exam</th><th>Term</th><th>Session</th></tr>
                        </thead>
                        <tbody>
                        @forelse($upcoming_exams as $ex)
                            <tr>
                                <td>{{ $ex->name }}</td>
                                <td>{{ 'Term '.$ex->term }}</td>
                                <td>{{ $ex->year }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center text-muted">No exams yet.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="card-header header-elements-inline">
                    <h6 class="card-title">Recent Results ({{ $year }})</h6>
                </div>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                        <tr><th>Student</th><th>Class</th><th>Subject</th><th>Exam</th><th>Score</th><th>Grade</th></tr>
                        </thead>
                        <tbody>
                        @forelse($recent_marks as $m)
                            <tr>
                                <td>{{ $m->user->name }}</td>
                                <td>{{ $m->my_class->name }}</td>
                                <td>{{ $m->subject->name }}</td>
                                <td>{{ $m->exam->name }} (T{{ $m->exam->term }})</td>
                                <td>{{ $m->total }}</td>
                                <td>{{ $m->grade->name }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted">No results recorded yet.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header header-elements-inline">
                    <h6 class="card-title">Recent Lessons</h6>
                </div>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                        <tr><th>Lesson</th><th>Subject</th><th>Class</th><th>Date</th></tr>
                        </thead>
                        <tbody>
                        @forelse($recent_lessons as $l)
                            <tr>
                                <td>{{ $l->topic }}</td>
                                <td>{{ $l->subject->name }}</td>
                                <td>{{ $l->myClass->name }}</td>
                                <td>{{ $l->lesson_date }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted">No lessons yet.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header header-elements-inline">
                    <h6 class="card-title">Recent Assignments & Homework</h6>
                </div>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                        <tr><th>Assignment</th><th>Subject</th><th>Class</th><th>Due</th></tr>
                        </thead>
                        <tbody>
                        @forelse($recent_assignments as $a)
                            <tr>
                                <td>{{ $a->title }}</td>
                                <td>{{ $a->subject->name }}</td>
                                <td>{{ $a->myClass->name }}</td>
                                <td>{{ $a->due_date }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted">No assignments yet.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection