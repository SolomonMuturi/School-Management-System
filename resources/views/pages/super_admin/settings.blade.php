@extends('layouts.master')
@section('page_title', 'System Settings')
@section('content')

    <div class="card">
        <div class="card-header header-elements-inline">
            <h6 class="card-title font-weight-semibold">System Settings</h6>
            {!! Qs::getPanelOptions() !!}
        </div>

        <div class="card-body">
            <form enctype="multipart/form-data" method="post" action="{{ route('settings.update') }}">
                @csrf @method('PUT')

                <ul class="nav nav-tabs nav-tabs-highlight">
                    <li class="nav-item"><a href="#school-info" class="nav-link active" data-toggle="tab">School Information</a></li>
                    <li class="nav-item"><a href="#session-term" class="nav-link" data-toggle="tab">Academic Session &amp; Term</a></li>
                    <li class="nav-item"><a href="#exam-lock" class="nav-link" data-toggle="tab">Exam Lock</a></li>
                    <li class="nav-item"><a href="#school-levels" class="nav-link" data-toggle="tab">School Levels</a></li>
                </ul>

                <div class="tab-content">
                    {{--SCHOOL INFORMATION--}}
                    <div class="tab-pane fade show active" id="school-info">
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label font-weight-semibold">Name of School <span class="text-danger">*</span></label>
                                    <div class="col-lg-9">
                                        <input name="system_name" value="{{ $s['system_name'] }}" required type="text" class="form-control" placeholder="Name of School">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label font-weight-semibold">School Acronym</label>
                                    <div class="col-lg-9">
                                        <input name="system_title" value="{{ $s['system_title'] }}" type="text" class="form-control" placeholder="School Acronym">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label font-weight-semibold">Phone</label>
                                    <div class="col-lg-9">
                                        <input name="phone" value="{{ $s['phone'] }}" type="text" class="form-control" placeholder="Phone">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label font-weight-semibold">School Email</label>
                                    <div class="col-lg-9">
                                        <input name="system_email" value="{{ $s['system_email'] }}" type="email" class="form-control" placeholder="School Email">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label font-weight-semibold">Alternate Email</label>
                                    <div class="col-lg-9">
                                        <input name="alt_email" value="{{ $s['alt_email'] }}" type="email" class="form-control" placeholder="Alternate Email">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label font-weight-semibold">School Address <span class="text-danger">*</span></label>
                                    <div class="col-lg-9">
                                        <input required name="address" value="{{ $s['address'] }}" type="text" class="form-control" placeholder="School Address">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label font-weight-semibold">School Logo</label>
                                    <div class="col-lg-9">
                                        <div class="mb-3">
                                            @if(!empty($s['logo']))
                                                <img style="width: 100px; height: 100px;" src="{{ $s['logo'] }}" alt="School Logo" class="rounded border">
                                            @else
                                                <span class="text-muted">No logo uploaded yet.</span>
                                            @endif
                                        </div>
                                        <input name="logo" accept="image/*" type="file" class="file-input" data-show-caption="false" data-show-upload="false" data-fouc>
                                        <span class="form-text text-muted">Accepted Images: jpeg, png, jpg. Max file size 2Mb. Used on reports and receipts where supported.</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{--ACADEMIC SESSION & TERM--}}
                    <div class="tab-pane fade" id="session-term">
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label for="current_session" class="col-lg-3 col-form-label font-weight-semibold">Current Session <span class="text-danger">*</span></label>
                                    <div class="col-lg-9">
                                        <input list="sessions-list" required name="current_session" id="current_session" class="form-control" value="{{ $s['current_session'] }}" placeholder="Eg. 2026-2027">
                                        <datalist id="sessions-list">
                                            @for($y = date('Y') - 4; $y <= date('Y') + 10; $y++)
                                                <option value="{{ ($y - 1).'-'.$y }}"></option>
                                            @endfor
                                        </datalist>
                                        <span class="form-text text-muted">This is the single source of truth used by Students, Classes, Subjects, Academics, Exams and Finance. Custom future sessions (format YYYY-YYYY) are allowed.</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label font-weight-semibold">This Term Ends</label>
                                    <div class="col-lg-9">
                                        <input name="term_ends" value="{{ $s['term_ends'] }}" type="text" class="form-control date-pick" placeholder="Date Term Ends">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label font-weight-semibold">Next Term Begins</label>
                                    <div class="col-lg-9">
                                        <input name="term_begins" value="{{ $s['term_begins'] }}" type="text" class="form-control date-pick" placeholder="Next Term Begins">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="divider">

                        {{--Fees--}}
                        <fieldset>
                            <legend><strong>Next Term Fees</strong></legend>
                            <div class="row">
                            @foreach($class_types as $ct)
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-lg-3 col-form-label font-weight-semibold">{{ $ct->name }}</label>
                                        <div class="col-lg-9">
                                            <input class="form-control" value="{{ $s['next_term_fees_'.strtolower($ct->code)] }}" name="next_term_fees_{{ strtolower($ct->code) }}" placeholder="{{ $ct->name }}" type="text">
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            </div>
                        </fieldset>
                    </div>

                    {{--EXAM LOCK--}}
                    <div class="tab-pane fade" id="exam-lock">
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label for="lock_exam" class="col-lg-3 col-form-label font-weight-semibold">Lock Exam</label>
                                    <div class="col-lg-3">
                                        <select class="form-control select" name="lock_exam" id="lock_exam">
                                            <option {{ $s['lock_exam'] ? 'selected' : '' }} value="1">Yes</option>
                                            <option {{ $s['lock_exam'] ?: 'selected' }} value="0">No</option>
                                        </select>
                                    </div>
                                    <div class="col-lg-6">
                                        <span class="font-weight-bold font-italic text-info-800">{{ __('msg.lock_exam') }}</span>
                                    </div>
                                </div>
                                <p class="ml-3 text-muted">
                                    When Exam Lock is enabled, results are protected and a subject PIN is required for
                                    students to access their marksheets. PINs are managed in the
                                    <a href="{{ route('pins.index') }}" class="font-weight-semibold">PINs module</a>.
                                </p>
                            </div>
                        </div>
                    </div>

                    {{--SCHOOL LEVELS--}}
                    <div class="tab-pane fade" id="school-levels">
                        <div class="alert alert-info border-0 mt-3">
                            <i class="icon-windows2 mr-2 text-info"></i>
                            School levels are the class types used across the system (e.g. Creche, Pre-Nursery, Nursery, Primary, Junior Secondary, Senior Secondary).
                            Levels are managed as part of the Classes module to avoid duplicate configuration.
                        </div>
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th>S/N</th>
                                <th>Level (Class Type)</th>
                                <th>Code</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($class_types as $ct)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $ct->name }}</td>
                                    <td>{{ $ct->code }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <hr class="divider">

                <div class="text-right">
                    <button type="submit" class="btn btn-danger">Save</button>
                </div>
            </form>
        </div>
    </div>

    {{--Settings Edit Ends--}}

@endsection