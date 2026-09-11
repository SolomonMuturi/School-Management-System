@extends('layouts.master')
@section('page_title', 'Fee Types')
@section('content')
    @include('partials.finance.finance_tabs')

    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header header-elements-inline">
                    <h5 class="card-title"><i class="icon-plus3 mr-2 text-primary"></i>Add Fee Type</h5>
                    {!! Qs::getPanelOptions() !!}
                </div>
                <div class="card-body">
                    <form method="post" action="{{ route('finance.fee_types.store') }}" class="ajax-store">
                        @csrf
                        <div class="form-group">
                            <label>Fee Type Name</label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. Tuition" required>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" class="form-control" rows="2" placeholder="Optional"></textarea>
                        </div>
                        <div class="form-group">
                            <label class="form-check-label">
                                <input type="checkbox" name="is_active" class="form-check-input-styled" checked data-fouc>
                                Active
                            </label>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Add Fee Type <i class="icon-checkmark3 ml-2"></i></button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="card-header header-elements-inline">
                    <h5 class="card-title"><i class="icon-price-tag2 mr-2 text-primary"></i>Fee Types</h5>
                    {!! Qs::getPanelOptions() !!}
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table datatable-button-html5-columns">
                            <thead>
                            <tr>
                                <th>S/N</th>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($fee_types as $ft)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td class="font-weight-semibold">{{ $ft->name }}</td>
                                    <td>{{ $ft->description }}</td>
                                    <td>
                                        @if($ft->is_active)
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-secondary">Disabled</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex">
                                            <button data-toggle="modal" data-target="#editType{{ $ft->id }}" class="btn btn-sm btn-primary mr-1"><i class="icon-pencil"></i></button>
                                            <form method="post" action="{{ route('finance.fee_types.toggle', $ft->id) }}">
                                                @csrf
                                                <button type="submit" class="btn btn-sm {{ $ft->is_active ? 'btn-warning' : 'btn-success' }}"><i class="icon-switch2"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>

                                <div class="modal fade" id="editType{{ $ft->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit Fee Type: {{ $ft->name }}</h5>
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            </div>
                                            <form method="post" action="{{ route('finance.fee_types.update', $ft->id) }}" class="ajax-update">
                                                @csrf @method('PUT')
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label>Name</label>
                                                        <input type="text" name="name" class="form-control" value="{{ $ft->name }}" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Description</label>
                                                        <textarea name="description" class="form-control" rows="2">{{ $ft->description }}</textarea>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="form-check-label">
                                                            <input type="checkbox" name="is_active" class="form-check-input-styled" {{ $ft->is_active ? 'checked' : '' }} data-fouc>
                                                            Active
                                                        </label>
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
                            @if($fee_types->count() < 1)
                                <tr><td colspan="5" class="text-center text-muted">No fee types yet.</td></tr>
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection