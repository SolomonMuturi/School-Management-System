@extends('layouts.master')
@section('page_title', 'Exam Report - '.$exam->name)
@section('content')

    <div class="card">
        <div class="card-header bg-white header-elements-inline">
            <h6 class="card-title">Exam Report: {{ $exam->name }} (Term {{ $exam->term }} - {{ $exam->year }})</h6>
            {!! Qs::getPanelOptions() !!}
        </div>

        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-12">
                    <button class="btn btn-primary" onclick="window.print()"><i class="icon-file-download"></i> Download</button>
                    <a href="{{ route('academic.reports') }}" class="btn btn-light">Back</a>
                </div>
            </div>

            <table class="table table-bordered datatable-responsive">
                <thead>
                <tr>
                    <th>S/N</th>
                    <th>Student</th>
                    <th>Class</th>
                    <th>Total</th>
                    <th>Average</th>
                    <th>Class Avg</th>
                    <th>Position</th>
                </tr>
                </thead>
                <tbody>
                @forelse($records as $r)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $users[$r->student_id] ?? 'Student #'.$r->student_id }}</td>
                        <td>{{ $classes[$r->my_class_id] ?? 'Class #'.$r->my_class_id }}</td>
                        <td>{{ $r->total }}</td>
                        <td>{{ $r->ave }}</td>
                        <td>{{ $r->class_ave }}</td>
                        <td>{{ $r->pos }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted">No exam records for {{ $exam->name }}.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection