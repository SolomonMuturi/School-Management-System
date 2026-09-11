@extends('layouts.master')
@section('page_title', 'Class List - '.$my_class->name)
@section('content')

    <div class="card">
        <div class="card-header bg-white header-elements-inline">
            <h6 class="card-title">Class List: {{ $my_class->name }} ({{ \Qs::getCurrentSession() }})</h6>
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
                    <th>Student ID</th>
                    <th>Name</th>
                    <th>Gender</th>
                    <th>Status</th>
                </tr>
                </thead>
                <tbody>
                @foreach($students as $st)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $st->adm_no }}</td>
                        <td>{{ $st->user->name }}</td>
                        <td>{{ $st->user->gender }}</td>
                        <td>
                            <span class="badge {{ $st->status == 'active' ? 'badge-success' : 'badge-secondary' }}">{{ $st->status }}</span>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

@endsection