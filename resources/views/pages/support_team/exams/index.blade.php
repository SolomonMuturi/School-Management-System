@extends('layouts.master')
@section('page_title', 'Manage Exams')
@section('content')

    <div class="card">
        <div class="card-header header-elements-inline">
            <h6 class="card-title">Manage Exams</h6>
            {!! Qs::getPanelOptions() !!}
        </div>

        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-12">
                    <a href="{{ route('exams.dashboard') }}" class="btn btn-outline-primary btn-sm">Open Dashboard</a>
                </div>
            </div>
            <ul class="nav nav-tabs nav-tabs-highlight">
                <li class="nav-item"><a href="#all-exams" class="nav-link active" data-toggle="tab">Manage Exam</a></li>
                <li class="nav-item"><a href="#new-exam" class="nav-link" data-toggle="tab"><i class="icon-plus2"></i> Add Exam</a></li>
            </ul>

            <div class="tab-content">
                    <div class="tab-pane fade show active" id="all-exams">
                        <table class="table datatable-button-html5-columns">
                            <thead>
                            <tr>
                                <th>S/N</th>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Term</th>
                                <th>Session</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($exams as $ex)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $ex->name }}</td>
                                    <td>{{ ucfirst($ex->type ?: 'term') }}</td>
                                    <td>{{ 'Term '.$ex->term }}</td>
                                    <td>{{ $ex->year }}</td>
                                    <td><span class="badge {{ $ex->status == 'published' ? 'badge-success' : ($ex->status == 'closed' ? 'badge-secondary' : 'badge-warning') }}">{{ $ex->status ?: 'pending' }}</span></td>
                                    <td class="text-center">
                                        <div class="list-icons">
                                            <div class="dropdown">
                                                <a href="#" class="list-icons-item" data-toggle="dropdown">
                                                <i class="icon-menu9"></i>
                                                </a>

                                                <div class="dropdown-menu dropdown-menu-left">
                                                    <a href="{{ route('exams.show', $ex->id) }}" class="dropdown-item"><i class="icon-eye"></i> View</a>
                                                    @if(Qs::userIsTeamSA())
                                                    {{--Edit--}}
                                                    <a href="{{ route('exams.edit', $ex->id) }}" class="dropdown-item"><i class="icon-pencil"></i> Edit</a>
                                                   @endif
                                                    @if(Qs::userIsSuperAdmin())
                                                    {{--Delete--}}
                                                    <a id="{{ $ex->id }}" onclick="confirmDelete(this.id)" href="#" class="dropdown-item"><i class="icon-trash"></i> Delete</a>
                                                    <form method="post" id="item-delete-{{ $ex->id }}" action="{{ route('exams.destroy', $ex->id) }}" class="hidden">@csrf @method('delete')</form>
                                                        @endif

                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                <div class="tab-pane fade" id="new-exam">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="alert alert-info border-0 alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>

                                <span>You are creating an Exam for the Current Session <strong>{{ Qs::getSetting('current_session') }}</strong></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <form method="post" action="{{ route('exams.store') }}">
                                @csrf
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label font-weight-semibold">Name <span class="text-danger">*</span></label>
                                    <div class="col-lg-9">
                                        <input name="name" value="{{ old('name') }}" required type="text" class="form-control" placeholder="Name of Exam">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="term" class="col-lg-3 col-form-label font-weight-semibold">Term</label>
                                    <div class="col-lg-9">
                                        <select data-placeholder="Select Teacher" class="form-control select-search" name="term" id="term">
                                            <option {{ old('term') == 1 ? 'selected' : '' }} value="1">First Term</option>
                                            <option {{ old('term') == 2 ? 'selected' : '' }} value="2">Second Term</option>
                                            <option {{ old('term') == 3 ? 'selected' : '' }} value="3">Third Term</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="type" class="col-lg-3 col-form-label font-weight-semibold">Exam Type</label>
                                    <div class="col-lg-9">
                                        <select data-placeholder="Select Exam Type" class="form-control select-search" name="type" id="type">
                                            <option {{ old('type') == 'term' || !old('type') ? 'selected' : '' }} value="term">Terminal Exam</option>
                                            <option {{ old('type') == 'midterm' ? 'selected' : '' }} value="midterm">Mid-Term Test</option>
                                            <option {{ old('type') == 'mock' ? 'selected' : '' }} value="mock">Mock Exam</option>
                                            <option {{ old('type') == 'final' ? 'selected' : '' }} value="final">Final Exam</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="start_date" class="col-lg-3 col-form-label font-weight-semibold">Start Date</label>
                                    <div class="col-lg-9">
                                        <input name="start_date" id="start_date" value="{{ old('start_date') }}" type="date" class="form-control">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="end_date" class="col-lg-3 col-form-label font-weight-semibold">End Date</label>
                                    <div class="col-lg-9">
                                        <input name="end_date" id="end_date" value="{{ old('end_date') }}" type="date" class="form-control">
                                    </div>
                                </div>

                                <div class="text-right">
                                    <button type="submit" class="btn btn-primary">Save</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{--Class List Ends--}}

@endsection
