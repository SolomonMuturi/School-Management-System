@extends('layouts.master')
@section('page_title', 'Edit Role - '.$role->name)
@section('content')

    <div class="card">
        <div class="card-header header-elements-inline">
            <h6 class="card-title">Edit Role</h6>
            {!! Qs::getPanelOptions() !!}
        </div>

        <div class="card-body">
            <form method="post" action="{{ route('users.roles.update', $role->id) }}">
                @csrf @method('put')
                <div class="form-group row">
                    <label class="col-lg-3 col-form-label font-weight-semibold">Role Name <span class="text-danger">*</span></label>
                    <div class="col-lg-9">
                        <input required name="name" value="{{ $role->name }}" type="text" class="form-control" placeholder="Role Name">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-3 col-form-label font-weight-semibold">Role Title</label>
                    <div class="col-lg-9">
                        <input disabled class="form-control" type="text" value="{{ $role->title }}">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-3 col-form-label font-weight-semibold">Role Level <span class="text-danger">*</span></label>
                    <div class="col-lg-9">
                        <input required type="number" min="1" max="20" name="level" class="form-control" value="{{ $role->level }}">
                    </div>
                </div>
                <div class="text-right">
                    <button type="submit" class="btn btn-primary">Update Role</button>
                </div>
            </form>
        </div>
    </div>

@endsection