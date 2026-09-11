@extends('layouts.master')
@section('page_title', 'Permissions & Access')
@section('content')

    <div class="card">
        <div class="card-header header-elements-inline">
            <h6 class="card-title">Permissions &amp; Access Matrix</h6>
            {!! Qs::getPanelOptions() !!}
        </div>

        <div class="card-body">
            <div class="alert alert-info border-0">
                <i class="icon-shield2 mr-2"></i>
                Tick a cell to grant that role access to the corresponding module. Changes apply immediately
                (menu items and backend access are controlled by these permissions).
                The <strong>Super Admin</strong> role always has access to every module and cannot be removed.
            </div>

            <form method="post" action="{{ route('users.permissions.update') }}">
                @csrf

                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                        <tr>
                            <th class="align-middle">Module</th>
                            @foreach($roles as $role)
                                <th class="text-center align-middle">
                                    {{ $role->name }}
                                    <br>
                                    <small class="text-muted text-uppercase">{{ $role->title }}</small>
                                </th>
                            @endforeach
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($modules as $module)
                            <tr>
                                <td class="font-weight-semibold align-middle">{{ $module }}</td>
                                @foreach($roles as $role)
                                    <td class="text-center align-middle">
                                        @if($role->title === 'super_admin')
                                            <span class="badge badge-success"><i class="icon-checkmark"></i></span>
                                        @else
                                            @php $checked = !empty($permissions[$role->title][$module]); @endphp
                                            <label class="mb-0" title="{{ $checked ? 'Revoke access' : 'Grant access' }}">
                                                <input type="checkbox"
                                                       name="permissions[{{ $role->title }}][]"
                                                       value="{{ $module }}"
                                                       {{ $checked ? 'checked' : '' }}>
                                            </label>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="text-right mt-3">
                    <button type="submit" class="btn btn-primary">Save Permissions</button>
                </div>
            </form>
        </div>
    </div>

@endsection