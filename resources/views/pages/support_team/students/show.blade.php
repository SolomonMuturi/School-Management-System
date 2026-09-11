@extends('layouts.master')
@section('page_title', 'Student Profile - '.$sr->user->name)
@section('content')

    <div class="mb-2">
        <a href="{{ route('students.index') }}" class="btn btn-link btn-sm pl-0"><i class="icon-arrow-left7 mr-1"></i> Back to Students</a>
    </div>

    {{-- Student Identity Card --}}
    <div class="card profile-head-card">
        <div class="card-body">
            <div class="media flex-column flex-md-row">
                <div class="mr-md-4 mb-2 mb-md-0 text-center">
                    <img src="{{ $sr->user->photo }}" alt="photo" class="rounded-circle" style="width: 110px; height: 110px; object-fit: cover;">
                </div>
                <div class="media-body">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                        <div>
                            <h4 class="mb-1 font-weight-semibold">{{ $sr->user->name }}</h4>
                            <div class="text-muted">
                                <span>Admission No: <strong>{{ $sr->adm_no }}</strong></span>
                                <span class="mx-2">•</span>
                                <span>Student ID: <strong>{{ $sr->user->code }}</strong></span>
                            </div>
                            <div class="mt-1">
                                <span class="badge badge-secondary">{{ $sr->my_class->name }}</span>
                                @if($sr->my_class->code)
                                    <span class="badge badge-light">{{ $sr->my_class->code }}</span>
                                @endif
                                <span class="badge badge-dark">Term {{ $current_term }}</span>
                                <span class="badge badge-info">{{ $current_session }}</span>
                            </div>
                        </div>
                        <div class="mt-2 mt-md-0">
                            @if($sr->status == 'active')
                                <span class="badge badge-success badge-pill font-size-base"><i class="icon-checkmark3 mr-1"></i>Active</span>
                            @elseif(in_array($sr->status, ['suspended', 'withdrawn']))
                                <span class="badge badge-danger badge-pill font-size-base">{{ ucfirst($sr->status) }}</span>
                            @elseif($sr->status == 'graduated')
                                <span class="badge badge-primary badge-pill font-size-base">{{ ucfirst($sr->status) }}</span>
                            @else
                                <span class="badge badge-secondary badge-pill font-size-base">{{ $sr->status ? ucfirst($sr->status) : 'Active' }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer bg-white">
            @if(Qs::userIsTeamSA())
                <a href="{{ route('students.edit', Qs::hash($sr->id)) }}" class="btn btn-primary btn-sm"><i class="icon-pencil mr-1"></i> Edit Student</a>
            @endif
            <a href="{{ route('students.reports.profile', Qs::hash($sr->id)) }}" class="btn btn-dark btn-sm"><i class="icon-file-download mr-1"></i> Download Profile</a>
            @if(Qs::userIsTeamAccount())
                <a href="{{ route('finance.student_bill', $sr->user_id) }}" class="btn btn-success btn-sm">Fees &amp; Payments</a>
            @endif
            @if(Qs::userIsTeamSAT())
                <a href="{{ route('marks.year_selector', Qs::hash($sr->user_id)) }}" class="btn btn-info btn-sm">Marksheet</a>
                <a href="{{ route('users.show', Qs::hash($sr->user_id)) }}" class="btn btn-secondary btn-sm">User Account</a>
            @endif
            <div class="btn-group ml-1 profile-more" id="profileMoreWrap">
                <button type="button" class="btn btn-light btn-sm" id="profileMoreBtn"><i class="icon-menu7 mr-1"></i> More <i class="icon-arrow-down12"></i></button>
                <div class="dropdown-menu dropdown-menu-right">
                    <a href="{{ route('students.guardians') }}" class="dropdown-item"><i class="icon-users2 mr-2"></i> Guardians</a>
                    <a href="{{ route('students.documents', Qs::hash($sr->id)) }}" class="dropdown-item"><i class="icon-file-download mr-2"></i> Manage Documents</a>
                    <a href="{{ route('students.discipline', Qs::hash($sr->id)) }}" class="dropdown-item"><i class="icon-warning2 mr-2"></i> Discipline</a>
                    <a href="{{ route('students.health', Qs::hash($sr->id)) }}" class="dropdown-item"><i class="icon-heart5 mr-2"></i> Health</a>
                    <a href="{{ route('students.transport', Qs::hash($sr->id)) }}" class="dropdown-item"><i class="icon-bus mr-2"></i> Transport</a>
                    <a href="{{ route('students.activities', Qs::hash($sr->id)) }}" class="dropdown-item"><i class="icon-dribbble mr-2"></i> Activities</a>
                    <a href="{{ route('students.history', Qs::hash($sr->id)) }}" class="dropdown-item"><i class="icon-history mr-2"></i> Full History</a>
                </div>
            </div>
        </div>
    </div>

    {{-- Profile Tabs --}}
    <div class="card">
        <div class="card-body">
            <ul class="nav nav-tabs nav-tabs-highlight nav-justified">
                <li class="nav-item"><a href="#tab-overview" class="nav-link active" data-toggle="tab">Overview</a></li>
                <li class="nav-item"><a href="#tab-academic" class="nav-link" data-toggle="tab">Academic</a></li>
                <li class="nav-item"><a href="#tab-attendance" class="nav-link" data-toggle="tab">Attendance</a></li>
                <li class="nav-item"><a href="#tab-finance" class="nav-link" data-toggle="tab">Finance</a></li>
                <li class="nav-item"><a href="#tab-timetable" class="nav-link" data-toggle="tab">Timetable</a></li>
                <li class="nav-item"><a href="#tab-documents" class="nav-link" data-toggle="tab">Documents</a></li>
                <li class="nav-item"><a href="#tab-history" class="nav-link" data-toggle="tab">History</a></li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane fade show active" id="tab-overview">
                    @include('pages.support_team.students.partials._overview_tab')
                </div>
                <div class="tab-pane fade" id="tab-academic">
                    @include('pages.support_team.students.partials._academic_tab')
                </div>
                <div class="tab-pane fade" id="tab-attendance">
                    @include('pages.support_team.students.partials._attendance_tab')
                </div>
                <div class="tab-pane fade" id="tab-finance">
                    @include('pages.support_team.students.partials._finance_tab')
                </div>
                <div class="tab-pane fade" id="tab-timetable">
                    @include('pages.support_team.students.partials._timetable_tab')
                </div>
                <div class="tab-pane fade" id="tab-documents">
                    @include('pages.support_team.students.partials._documents_tab')
                </div>
                <div class="tab-pane fade" id="tab-history">
                    @include('pages.support_team.students.partials._history_tab')
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        $(function () {
            var $wrap = $('#profileMoreWrap');
            var $btn  = $wrap.find('#profileMoreBtn');
            var $menu = $wrap.find('.dropdown-menu').attr('id', 'profileMoreMenu').appendTo('body');

            function position() {
                var r = $btn[0].getBoundingClientRect();
                $menu.css({
                    position: 'fixed',
                    top: (r.bottom + 6) + 'px',
                    right: (window.innerWidth - r.right) + 'px',
                    left: 'auto',
                    bottom: 'auto',
                    zIndex: 3000
                });
            }

            function open() {
                position();
                $menu.show();
                $wrap.addClass('open');
            }

            function close() {
                $menu.hide();
                $wrap.removeClass('open');
            }

            $btn.on('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                $wrap.hasClass('open') ? close() : open();
            });

            $(window).on('scroll resize', function () {
                if ($wrap.hasClass('open')) { position(); }
            });

            $(document).on('click', function (e) {
                if (!$(e.target).closest('#profileMoreMenu').length && !$(e.target).closest('#profileMoreBtn').length) {
                    close();
                }
            });
        });
    </script>
@endsection