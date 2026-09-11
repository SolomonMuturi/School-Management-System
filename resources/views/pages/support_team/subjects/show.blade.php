@extends('layouts.master')
@section('page_title', 'Subject Details - '.$s->name)
@section('content')

    <div class="card">
        <div class="card-header header-elements-inline">
            <h6 class="card-title">{{ $s->name }} <span class="text-muted">({{ $s->code ?: $s->slug }})</span></h6>
            {!! Qs::getPanelOptions() !!}
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="card bg-primary-400">
                        <div class="card-body text-center">
                            <h3 class="font-weight-bold mb-0">{{ $s->my_class ? $s->my_class->name : '-' }}</h3>
                            <span>Class</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success-400">
                        <div class="card-body text-center">
                            <h3 class="font-weight-bold mb-0">{{ $s->teacher ? $s->teacher->name : 'None' }}</h3>
                            <span>Subject Teacher</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-teal-400">
                        <div class="card-body text-center">
                            <h3 class="font-weight-bold mb-0">{{ $s->slug }}</h3>
                            <span>Short Name</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-{{ $s->status == 'active' ? 'success' : 'secondary' }}-400">
                        <div class="card-body text-center">
                            <h3 class="font-weight-bold mb-0">{{ $s->status ?: 'active' }}</h3>
                            <span>Status</span>
                        </div>
                    </div>
                </div>
            </div>

            @if($s->description)
                <div class="alert alert-info border-0">
                    <h6 class="font-weight-semibold">Description</h6>
                    <p class="mb-0">{{ $s->description }}</p>
                </div>
            @endif

            <div class="row mb-3">
                <div class="col-md-12">
                    <a href="{{ route('subjects.edit', $s->id) }}" class="btn btn-primary btn-sm"><i class="icon-pencil"></i> Edit Subject</a>
                    @if($s->my_class)
                        <a href="{{ route('classes.show', $s->my_class->id) }}" class="btn btn-secondary btn-sm"><i class="icon-windows2"></i> View Class</a>
                    @endif
                </div>
            </div>

            <table class="table table-bordered">
                <tbody>
                <tr>
                    <td class="font-weight-bold" style="width: 25%;">Subject Name</td>
                    <td>{{ $s->name }}</td>
                </tr>
                <tr>
                    <td class="font-weight-bold">Subject Code</td>
                    <td>{{ $s->code ?: '-' }}</td>
                </tr>
                <tr>
                    <td class="font-weight-bold">Short Name</td>
                    <td>{{ $s->slug }}</td>
                </tr>
                <tr>
                    <td class="font-weight-bold">Class</td>
                    <td>{{ $s->my_class ? $s->my_class->name : '-' }}</td>
                </tr>
                <tr>
                    <td class="font-weight-bold">Teacher</td>
                    <td>{{ $s->teacher ? $s->teacher->name : '-' }}</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

@endsection