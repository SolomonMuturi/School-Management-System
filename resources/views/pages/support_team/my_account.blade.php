@extends('layouts.master')
@section('page_title', 'My Account')
@section('content')

    <div class="row">
        <div class="col-md-9">
            <div class="card">
                <div class="card-header header-elements-inline">
                    <h6 class="card-title">My Account</h6>
                    {!! Qs::getPanelOptions() !!}
                </div>

                <div class="card-body">
                    <ul class="nav nav-tabs nav-tabs-highlight">
                        <li class="nav-item"><a href="#acct-info" class="nav-link active" data-toggle="tab">Account Info</a></li>
                        <li class="nav-item"><a href="#edit-profile" class="nav-link" data-toggle="tab"><i class="icon-user mr-1"></i> Edit Profile</a></li>
                        <li class="nav-item"><a href="#change-pass" class="nav-link" data-toggle="tab"><i class="icon-lock mr-1"></i> Change Password</a></li>
                    </ul>

                    <div class="tab-content">

                        {{--ACCOUNT INFO--}}
                        <div class="tab-pane fade show active" id="acct-info">
                            <div class="row mt-3">
                                <div class="col-md-4 text-center">
                                    <img style="width: 120px; height: 120px;" src="{{ $my->photo }}" alt="photo" class="rounded-circle mb-2">
                                    <h5>{{ $my->name }}</h5>
                                    <span class="badge {{ $my->status == 'active' || !$my->status ? 'badge-success' : 'badge-secondary' }} text-uppercase">{{ $my->status ?: 'active' }}</span>
                                </div>
                                <div class="col-md-8">
                                    <table class="table table-bordered">
                                        <tbody>
                                        <tr><td class="font-weight-bold" width="35%">Role</td><td>{{ ucwords(str_replace('_', ' ', $my->user_type)) }}</td></tr>
                                        <tr><td class="font-weight-bold">Username</td><td>{{ $my->username ?: '-' }}</td></tr>
                                        <tr><td class="font-weight-bold">Email</td><td>{{ $my->email ?: '-' }}</td></tr>
                                        <tr><td class="font-weight-bold">Phone</td><td>{{ ($my->phone ?: '').' '.($my->phone2 ?: '') }}</td></tr>
                                        <tr><td class="font-weight-bold">Address</td><td>{{ $my->address ?: '-' }}</td></tr>
                                        <tr><td class="font-weight-bold">Last Login</td><td>{{ $my->last_login ? $my->last_login->format('d M, Y H:i') : 'Never' }}</td></tr>
                                        <tr><td class="font-weight-bold">Member Since</td><td>{{ $my->created_at ? $my->created_at->format('d M, Y') : '-' }}</td></tr>
                                        </tbody>
                                    </table>
                                    <form method="post" action="{{ route('logout') }}" class="text-right">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-danger btn-sm"><i class="icon-switch2 mr-1"></i> Logout</button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        {{--EDIT PROFILE--}}
                        <div class="tab-pane fade" id="edit-profile">
                            <div class="row mt-3">
                                <div class="col-md-8">
                                    <form enctype="multipart/form-data" method="post" action="{{ route('my_account.update') }}">
                                        @csrf @method('put')

                                        <div class="form-group row">
                                            <label for="name" class="col-lg-3 col-form-label font-weight-semibold">Name <span class="text-danger">*</span></label>
                                            <div class="col-lg-9">
                                                <input id="name" name="name" required class="form-control" type="text" value="{{ $my->name }}">
                                            </div>
                                        </div>

                                        @if($my->username)
                                            <div class="form-group row">
                                                <label for="username" class="col-lg-3 col-form-label font-weight-semibold">Username</label>
                                                <div class="col-lg-9">
                                                    <input disabled="disabled" id="username" class="form-control" type="text" value="{{ $my->username }}">
                                                </div>
                                            </div>
                                        @else
                                            <div class="form-group row">
                                                <label for="username" class="col-lg-3 col-form-label font-weight-semibold">Username</label>
                                                <div class="col-lg-9">
                                                    <input id="username" name="username" type="text" class="form-control">
                                                </div>
                                            </div>
                                        @endif

                                        <div class="form-group row">
                                            <label for="email" class="col-lg-3 col-form-label font-weight-semibold">Email</label>
                                            <div class="col-lg-9">
                                                <input id="email" value="{{ $my->email }}" name="email" type="email" class="form-control">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="phone" class="col-lg-3 col-form-label font-weight-semibold">Phone</label>
                                            <div class="col-lg-9">
                                                <input id="phone" value="{{ $my->phone }}" name="phone" type="text" class="form-control">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="phone2" class="col-lg-3 col-form-label font-weight-semibold">Telephone</label>
                                            <div class="col-lg-9">
                                                <input id="phone2" value="{{ $my->phone2 }}" name="phone2" type="text" class="form-control">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="address" class="col-lg-3 col-form-label font-weight-semibold">Address <span class="text-danger">*</span></label>
                                            <div class="col-lg-9">
                                                <input id="address" value="{{ $my->address }}" name="address" type="text" required class="form-control">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="photo" class="col-lg-3 col-form-label font-weight-semibold">Change Photo</label>
                                            <div class="col-lg-9">
                                                <input id="photo" accept="image/*" type="file" name="photo" class="form-input-styled" data-fouc>
                                            </div>
                                        </div>

                                        <div class="text-right">
                                            <button type="submit" class="btn btn-danger">Save</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        {{--CHANGE PASSWORD--}}
                        <div class="tab-pane fade" id="change-pass">
                            <div class="row mt-3">
                                <div class="col-md-8">
                                    <form method="post" action="{{ route('my_account.change_pass') }}">
                                        @csrf @method('put')

                                        <div class="form-group row">
                                            <label for="current_password" class="col-lg-3 col-form-label font-weight-semibold">Current Password <span class="text-danger">*</span></label>
                                            <div class="col-lg-9">
                                                <input id="current_password" name="current_password" required type="password" class="form-control">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="password" class="col-lg-3 col-form-label font-weight-semibold">New Password <span class="text-danger">*</span></label>
                                            <div class="col-lg-9">
                                                <input id="password" name="password" required type="password" class="form-control">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="password_confirmation" class="col-lg-3 col-form-label font-weight-semibold">Confirm Password <span class="text-danger">*</span></label>
                                            <div class="col-lg-9">
                                                <input id="password_confirmation" name="password_confirmation" required type="password" class="form-control">
                                            </div>
                                        </div>

                                        <div class="text-right">
                                            <button type="submit" class="btn btn-danger">Save</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    {{--My Profile Ends--}}

@endsection