@extends('layouts.master')
@section('page_title', 'Classes & Grades')
@section('content')

    <div class="card">
        <div class="card-header bg-white header-elements-inline">
            <h6 class="card-title">Classes &amp; Grades ({{ $current_year->name }})</h6>
            {!! Qs::getPanelOptions() !!}
        </div>

        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-12">
                    <a href="{{ route('classes.index') }}" class="btn btn-outline-primary btn-sm">Manage Classes</a>
                    <a href="{{ route('academic.reports.class') }}" class="btn btn-outline-success btn-sm">Class List Report</a>
                </div>
            </div>

            <table class="table table-bordered datatable-responsive">
                <thead>
                <tr>
                    <th>S/N</th>
                    <th>Class</th>
                    <th>Type</th>
                    <th>Class Teacher</th>
                    <th>Students</th>
                </tr>
                </thead>
                <tbody>
                @foreach($classes as $mc)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $mc->name }}</td>
                        <td>{{ $mc->class_type ? $mc->class_type->name : '-' }}</td>
                        <td>
                            @if($mc->teacher)
                                <span class="badge badge-light">{{ $mc->teacher->name }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge badge-success">{{ \App\Models\StudentRecord::where('my_class_id', $mc->id)->where('status','active')->count() }}</span> active
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

@endsection