@extends('layouts.master')
@section('page_title', 'Class Performance')
@section('content')

    <div class="card">
        <div class="card-header bg-white header-elements-inline">
            <h6 class="card-title">Class Performance - {{ $class->name }} ({{ $year }})</h6>
            {!! Qs::getPanelOptions() !!}
        </div>

        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-12">
                    <button class="btn btn-primary" onclick="window.print()"><i class="icon-file-download"></i> Download</button>
                    <a href="{{ route('academic.performance') }}" class="btn btn-light">Back</a>
                </div>
            </div>

            @if($rows->count())
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>S/N</th>
                        <th>Subject</th>
                        <th>Students</th>
                        <th>Avg Test (TCA)</th>
                        <th>Avg Exam</th>
                        <th>Avg Total</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($rows as $r)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $subjects[$r->subject_id] ?? 'Subject #'.$r->subject_id }}</td>
                            <td>{{ $r->students }}</td>
                            <td>{{ round($r->avg_tca, 1) }}</td>
                            <td>{{ round($r->avg_exm, 1) }}</td>
                            <td><strong>{{ round($r->avg_total, 1) }}</strong></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @else
                <div class="alert alert-warning">No marks recorded for this class in {{ $year }}.</div>
            @endif
        </div>
    </div>

@endsection