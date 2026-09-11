@extends('layouts.master')
@section('page_title', 'Edit Exam - '.$ex->name. ' ('.$ex->year.')')
@section('content')

    <div class="card">
        <div class="card-header header-elements-inline">
            <h6 class="card-title">Edit Exam</h6>
            {!! Qs::getPanelOptions() !!}
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <form method="post" action="{{ route('exams.update', $ex->id) }}">
                        @csrf @method('PUT')
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label font-weight-semibold">Name <span class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                <input name="name" value="{{ $ex->name }}" required type="text" class="form-control" placeholder="Name of Exam">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="term" class="col-lg-3 col-form-label font-weight-semibold">Term</label>
                            <div class="col-lg-9">
                                <select data-placeholder="Select Teacher" class="form-control select-search" name="term" id="term">
                                    <option {{ $ex->term == 1 ? 'selected' : '' }} value="1">First Term</option>
                                    <option {{ $ex->term == 2 ? 'selected' : '' }} value="2">Second Term</option>
                                    <option {{ $ex->term == 3 ? 'selected' : '' }} value="3">Third Term</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="type" class="col-lg-3 col-form-label font-weight-semibold">Exam Type</label>
                            <div class="col-lg-9">
                                <select class="form-control select-search" name="type" id="type">
                                    <option {{ $ex->type == 'term' || !$ex->type ? 'selected' : '' }} value="term">Terminal Exam</option>
                                    <option {{ $ex->type == 'midterm' ? 'selected' : '' }} value="midterm">Mid-Term Test</option>
                                    <option {{ $ex->type == 'mock' ? 'selected' : '' }} value="mock">Mock Exam</option>
                                    <option {{ $ex->type == 'final' ? 'selected' : '' }} value="final">Final Exam</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="start_date" class="col-lg-3 col-form-label font-weight-semibold">Start Date</label>
                            <div class="col-lg-9">
                                <input name="start_date" id="start_date" value="{{ $ex->start_date }}" type="date" class="form-control">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="end_date" class="col-lg-3 col-form-label font-weight-semibold">End Date</label>
                            <div class="col-lg-9">
                                <input name="end_date" id="end_date" value="{{ $ex->end_date }}" type="date" class="form-control">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="status" class="col-lg-3 col-form-label font-weight-semibold">Status</label>
                            <div class="col-lg-9">
                                <select class="form-control select" name="status" id="status">
                                    <option {{ $ex->status == 'pending' || !$ex->status ? 'selected' : '' }} value="pending">Pending</option>
                                    <option {{ $ex->status == 'published' ? 'selected' : '' }} value="published">Published</option>
                                    <option {{ $ex->status == 'closed' ? 'selected' : '' }} value="closed">Closed</option>
                                </select>
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

    {{--Class Edit Ends--}}

@endsection
