@extends('layouts.master')
@section('page_title', 'Suppliers')
@section('content')
    @include('partials.finance.finance_tabs')

    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header header-elements-inline">
                    <h5 class="card-title"><i class="icon-plus3 mr-2 text-primary"></i>Add Supplier</h5>
                    {!! Qs::getPanelOptions() !!}
                </div>
                <div class="card-body">
                    <form method="post" action="{{ route('finance.suppliers.store') }}" class="ajax-store">
                        @csrf
                        <div class="form-group">
                            <label>Company / Name</label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. Gaskiya Stationery Ltd" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Phone</label>
                                    <input type="text" name="phone" class="form-control" placeholder="+234...">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" name="email" class="form-control" placeholder="name@example.com">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Address</label>
                            <input type="text" name="address" class="form-control" placeholder="Street, City">
                        </div>
                        <div class="form-group">
                            <label>Contact Person</label>
                            <input type="text" name="contact_person" class="form-control" placeholder="Optional">
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Add Supplier <i class="icon-checkmark3 ml-2"></i></button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="card-header header-elements-inline">
                    <h5 class="card-title"><i class="icon-truck mr-2 text-primary"></i>Suppliers</h5>
                    {!! Qs::getPanelOptions() !!}
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table datatable-button-html5-columns">
                            <thead>
                            <tr>
                                <th>S/N</th>
                                <th>Name</th>
                                <th>Contact Person</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th>Address</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($suppliers as $s)
                                <tr class="{{ $s->is_active ? '' : 'opacity-50' }}">
                                    <td>{{ $loop->iteration }}</td>
                                    <td class="font-weight-semibold">{{ $s->name }}</td>
                                    <td>{{ $s->contact_person }}</td>
                                    <td>{{ $s->phone }}</td>
                                    <td>{{ $s->email }}</td>
                                    <td>{{ $s->address }}</td>
                                    <td>
                                        @if($s->is_active)
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex">
                                            <button data-toggle="modal" data-target="#editSupplier{{ $s->id }}" class="btn btn-sm btn-primary mr-1"><i class="icon-pencil"></i></button>
                                            <form method="post" action="{{ route('finance.suppliers.toggle', $s->id) }}">
                                                @csrf
                                                <button type="submit" class="btn btn-sm {{ $s->is_active ? 'btn-warning' : 'btn-success' }}"><i class="icon-switch2"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>

                                <div class="modal fade" id="editSupplier{{ $s->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit Supplier: {{ $s->name }}</h5>
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            </div>
                                            <form method="post" action="{{ route('finance.suppliers.update', $s->id) }}" class="ajax-update">
                                                @csrf @method('PUT')
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label>Name</label>
                                                        <input type="text" name="name" class="form-control" value="{{ $s->name }}" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Contact Person</label>
                                                        <input type="text" name="contact_person" class="form-control" value="{{ $s->contact_person }}">
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Phone</label>
                                                                <input type="text" name="phone" class="form-control" value="{{ $s->phone }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Email</label>
                                                                <input type="email" name="email" class="form-control" value="{{ $s->email }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Address</label>
                                                        <input type="text" name="address" class="form-control" value="{{ $s->address }}">
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-primary">Save</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            @if($suppliers->count() < 1)
                                <tr><td colspan="8" class="text-center text-muted">No suppliers yet.</td></tr>
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection