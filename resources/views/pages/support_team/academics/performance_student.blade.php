@extends('layouts.master')
@section('page_title', 'Student Performance')
@section('content')

    <div class="card">
        <div class="card-header bg-white header-elements-inline">
            <h6 class="card-title">Academic Performance - {{ $sr->user->name }}</h6>
            {!! Qs::getPanelOptions() !!}
        </div>

        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-12">
                    <a href="{{ route('academic.performance') }}" class="btn btn-light">Back</a>
                </div>
            </div>

            @foreach($terms as $t)
                <h6 class="font-weight-semibold text-primary">Session: {{ $t->year }}</h6>
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>Subject</th>
                        <th>Exam</th>
                        @foreach(["T1","T2","T3","T4","TCA","EXM","TOTAL"] as $col)
                            <th>{{ $col }}</th>
                        @endforeach
                        <th>Grade</th>
                        <th>Position</th>
                    </tr>
                    </thead>
                    <tbody>
                    @php $year_marks = $marks->where('year', $t->year); @endphp
                    @forelse($year_marks as $m)
                        <tr>
                            <td>{{ $m->subject->name }}</td>
                            <td>{{ $m->exam->name }} (T{{ $m->exam->term }})</td>
                            <td>{{ $m->t1 }}</td>
                            <td>{{ $m->t2 }}</td>
                            <td>{{ $m->t3 }}</td>
                            <td>{{ $m->t4 }}</td>
                            <td>{{ $m->tca }}</td>
                            <td>{{ $m->exm }}</td>
                            <td><strong>{{ $m->total }}</strong></td>
                            <td>{{ $m->grade->name }}</td>
                            <td>{{ $m->sub_pos }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="11" class="text-center text-muted">No results for this session.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            @endforeach
        </div>
    </div>

@endsection