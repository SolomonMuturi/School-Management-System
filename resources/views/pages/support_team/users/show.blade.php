@extends('layouts.master')
@section('page_title', 'User Profile - '.$user->name)
@section('content')

    <div class="row justify-content-center">
        <div class="col-lg-10">

            {{--Profile Header--}}
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-3 text-center">
                            <img src="{{ $user->photo ?: Qs::getDefaultUserImage() }}" alt="photo"
                                 class="rounded-circle" width="120" height="120"
                                 style="object-fit: cover; border: 3px solid #fff; box-shadow: 0 2px 12px rgba(0,0,0,.15);">
                        </div>

                        <div class="col-md-6 text-center text-md-left mt-3 mt-md-0">
                            <h4 class="font-weight-bold mb-1">{{ $user->name }}</h4>
                            <p class="text-muted mb-2">
                                <i class="icon-user-tie mr-1"></i>{{ $role ? $role->name : ucwords(str_replace('_', ' ', $user->user_type)) }}
                            </p>
                            <div>
                                <span class="badge {{ $user->status == 'active' || !$user->status ? 'badge-success' : 'badge-secondary' }} text-uppercase">{{ $user->status ?: 'active' }}</span>
                                @if($user->user_type == 'super_admin')
                                    <span class="badge badge-primary text-uppercase">Administrator</span>
                                @endif
                                @if($user->email)
                                    <span class="badge badge-flat border text-muted"><i class="icon-envelop mr-1"></i>{{ $user->email }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-3 text-md-right mt-3 mt-md-0">
                            @if(Qs::userIsTeamSA())
                                <form method="post" action="{{ route('users.status', $user->id) }}" class="d-block">@csrf @method('put')
                                    <button type="submit" class="btn btn-block btn-sm {{ $user->status == 'active' ? 'btn-secondary' : 'btn-success' }} mb-2">
                                        <i class="icon-switch2"></i> {{ $user->status == 'active' ? 'Deactivate' : 'Activate' }}
                                    </button>
                                </form>
                            @endif
                            <a href="{{ route('users.edit', Qs::hash($user->id)) }}" class="btn btn-block btn-sm btn-primary mb-2"><i class="icon-pencil"></i> Edit Details</a>
                            @if(Qs::userIsSuperAdmin())
                                <a href="{{ route('users.reset_pass', Qs::hash($user->id)) }}" class="btn btn-block btn-sm btn-warning"><i class="icon-lock"></i> Reset Password</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            {{--/Profile Header--}}

            {{--Account Summary--}}
            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="card mb-0">
                        <div class="card-body text-center">
                            <i class="icon-calendar3 text-primary icon-2x mb-2"></i>
                            <h6 class="font-weight-semibold mb-1">Member Since</h6>
                            <p class="text-muted mb-0"><strong>{{ $user->created_at ? $user->created_at->format('d M, Y') : '-' }}</strong></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="card mb-0">
                        <div class="card-body text-center">
                            <i class="icon-clock3 text-success icon-2x mb-2"></i>
                            <h6 class="font-weight-semibold mb-1">Last Login</h6>
                            <p class="text-muted mb-0"><strong>{{ $user->last_login ? $user->last_login->format('d M, Y H:i') : '-' }}</strong></p>
                        </div>
                    </div>
                </div>
            </div>
            {{--/Account Summary--}}

            {{--Basic Information--}}
            <div class="card">
                <div class="card-header header-elements-inline">
                    <h6 class="card-title font-weight-semibold"><i class="icon-user mr-2 text-primary"></i>Basic Information</h6>
                    {!! Qs::getPanelOptions() !!}
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <dl class="row mb-0">
                                <dt class="col-sm-4 font-weight-semibold text-muted">Name</dt>
                                <dd class="col-sm-8 mb-3">{{ $user->name }}</dd>

                                <dt class="col-sm-4 font-weight-semibold text-muted">Gender</dt>
                                <dd class="col-sm-8 mb-3">{{ $user->gender ?: '-' }}</dd>

                                @if($user->email)
                                    <dt class="col-sm-4 font-weight-semibold text-muted">Email</dt>
                                    <dd class="col-sm-8 mb-3">{{ $user->email }}</dd>
                                @endif

                                @if($user->username)
                                    <dt class="col-sm-4 font-weight-semibold text-muted">Username</dt>
                                    <dd class="col-sm-8 mb-3">{{ $user->username }}</dd>
                                @endif

                                @if($user->phone)
                                    <dt class="col-sm-4 font-weight-semibold text-muted">Phone</dt>
                                    <dd class="col-sm-8 mb-3">{{ trim($user->phone.' '.$user->phone2) }}</dd>
                                @endif
                            </dl>
                        </div>

                        <div class="col-md-6">
                            <dl class="row mb-0">
                                <dt class="col-sm-4 font-weight-semibold text-muted">Birthday</dt>
                                <dd class="col-sm-8 mb-3">{{ $user->dob ?: '-' }}</dd>

                                @if($user->bg_id)
                                    <dt class="col-sm-4 font-weight-semibold text-muted">Blood Group</dt>
                                    <dd class="col-sm-8 mb-3">{{ $user->blood_group->name }}</dd>
                                @endif

                                @if($user->nal_id)
                                    <dt class="col-sm-4 font-weight-semibold text-muted">Nationality</dt>
                                    <dd class="col-sm-8 mb-3">{{ $user->nationality->name }}</dd>
                                @endif

                                @if($user->state_id)
                                    <dt class="col-sm-4 font-weight-semibold text-muted">State</dt>
                                    <dd class="col-sm-8 mb-3">{{ $user->state->name }}</dd>
                                @endif

                                @if($user->lga_id)
                                    <dt class="col-sm-4 font-weight-semibold text-muted">LGA</dt>
                                    <dd class="col-sm-8 mb-3">{{ $user->lga->name }}</dd>
                                @endif

                                <dt class="col-sm-4 font-weight-semibold text-muted">Address</dt>
                                <dd class="col-sm-8 mb-3">{{ $user->address ?: '-' }}</dd>
                            </dl>
                        </div>
                    </div>

                    @if($user->user_type == 'parent')
                        <hr>
                        <h6 class="font-weight-semibold"><i class="icon-users4 mr-2 text-primary"></i>Children / Ward</h6>
                        <div class="row">
                            @forelse(Qs::findMyChildren($user->id) as $sr)
                                <div class="col-md-4">
                                    <div class="media mt-2">
                                        <div class="mr-3">
                                            <img src="{{ $sr->user->photo ?: Qs::getDefaultUserImage() }}" class="rounded-circle" width="44" height="44" style="object-fit: cover;">
                                        </div>
                                        <div class="media-body">
                                            <h6 class="mt-0 mb-0">
                                                <a href="{{ route('students.show', Qs::hash($sr->id)) }}" class="font-weight-semibold">{{ $sr->user->name }}</a>
                                            </h6>
                                            <span class="text-muted"><i class="icon-home2 mr-1"></i>{{ $sr->my_class->name }}</span>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12 text-muted">No linked children.</div>
                            @endforelse
                        </div>
                    @endif

                    @if($user->user_type == 'teacher')
                        <hr>
                        <h6 class="font-weight-semibold"><i class="icon-books mr-2 text-primary"></i>Subjects</h6>
                        <div class="row">
                            @forelse(Qs::findTeacherSubjects($user->id) as $sub)
                                <div class="col-md-4">
                                    <span class="badge badge-light border mt-1 py-2 px-3">
                                        <i class="icon-book mr-1 text-primary"></i>{{ $sub->name }} <small class="text-muted">- {{ $sub->my_class->name }}</small>
                                    </span>
                                </div>
                            @empty
                                <div class="col-12 text-muted">No assigned subjects.</div>
                            @endforelse
                        </div>
                    @endif
                </div>
            </div>
            {{--/Basic Information--}}

        </div>
    </div>

    {{--User Profile Ends--}}

@endsection