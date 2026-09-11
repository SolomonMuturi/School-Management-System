@extends('layouts.master')
@section('page_title', 'Subject Report - '.$subject->name)
@section('content')

    <div class="card">
        <div class="card-header bg-white header-elements-inline">
            <h6 class="card-title">Subject Report: {{ $subject->name }} ({{ $year }})</h6>
            {!! Qs::getPanelOptions() !!}
        </div>

        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-12">
                    <button class="btn btn-primary" onclick="window.print()"><i class="icon-file-download"></i> Download</button>
                    <a href="{{ route('academic.reports') }}" class="btn btn-light">Back</a>
                </div>
            </div>

            <table class="table table-bordered">
                <thead>
                <tr>
                    <th>S/N</th>
                    <th>Class</th>
                    <th>Students</th>
                    <th>Avg Test (TCA)</th>
                    <th>Avg Exam</th>
                    <th>Avg Total</th>
                </tr>
                </thead>
                <tbody>
                @forelse($rows as $r)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $classes[$r->my_class_id] ?? 'Class #'.$r->my_class_id }}</td>
                        <td>{{ $r->students }}</td>
                        <td>{{ round($r->avg_tca, 1) }}</td>
                        <td>{{ round($r->avg_exm, 1) }}</td>
                        <td><strong>{{ round($r->avg_total, 1) }}</strong></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted">No results recorded for {{ $subject->name }} in {{ $year }}.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection