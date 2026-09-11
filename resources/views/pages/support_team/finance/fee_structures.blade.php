@extends('layouts.master')
@section('page_title', 'Fee Structures')
@section('content')
    @include('partials.finance.finance_tabs')

    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header header-elements-inline">
                    <h5 class="card-title"><i class="icon-plus3 mr-2 text-primary"></i>Add Fee Structure</h5>
                    {!! Qs::getPanelOptions() !!}
                </div>
                <div class="card-body">
                    <div class="alert alert-light border">
                        <strong>Session:</strong> <input type="text" class="form-control" value="{{ $selected }}" disabled>
                    </div>
                    <form method="post" action="{{ route('finance.fee_structures.store') }}" class="ajax-store">
                        @csrf
                        <div class="form-group">
                            <label>Fee Type</label>
                            <select name="fee_type_id" class="form-control select" required>
                                <option value="">Select Fee Type</option>
                                @foreach($fee_types as $ft)
                                    <option value="{{ $ft->id }}">{{ $ft->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Class</label>
                            <select name="my_class_id" class="form-control select" required>
                                <option value="">Select Class</option>
                                @foreach($my_classes as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Term</label>
                            <select name="term" class="form-control select" required>
                                @foreach($terms as $term)
                                    <option value="{{ $term }}">{{ $term }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Amount</label>
                            <input type="number" step="0.01" min="0" name="amount" class="form-control" placeholder="0.00" required>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <input type="text" name="description" class="form-control" placeholder="Optional">
                        </div>
                        <div class="form-group">
                            <label class="form-check-label">
                                <input type="checkbox" name="is_active" class="form-check-input-styled" checked data-fouc>
                                Active
                            </label>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Add Fee Structure <i class="icon-checkmark3 ml-2"></i></button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="card-header header-elements-inline">
                    <h5 class="card-title"><i class="icon-stack3 mr-2 text-primary"></i>Fee Structures - {{ $selected }}</h5>
                    {!! Qs::getPanelOptions() !!}
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table datatable-button-html5-columns">
                            <thead>
                            <tr>
                                <th>S/N</th>
                                <th>Fee Type</th>
                                <th>Class</th>
                                <th>Term</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($fee_structures as $fs)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td class="font-weight-semibold">{{ $fs->feeType->name ?? 'N/A' }}</td>
                                    <td>{{ $fs->myClass->name ?? 'N/A' }}</td>
                                    <td>{{ $fs->term }}</td>
                                    <td class="font-weight-bold text-primary">{{ number_format($fs->amount, 2) }}</td>
                                    <td>
                                        @if($fs->is_active)
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex">
                                            <button data-toggle="modal" data-target="#editFs{{ $fs->id }}" class="btn btn-sm btn-primary mr-1"><i class="icon-pencil"></i></button>
                                            <form method="post" action="{{ route('finance.fee_structures.destroy', $fs->id) }}" onsubmit="return confirm('Delete this fee structure?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger"><i class="icon-trash"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>

                                <div class="modal fade" id="editFs{{ $fs->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit Fee Structure</h5>
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            </div>
                                            <form method="post" action="{{ route('finance.fee_structures.update', $fs->id) }}" class="ajax-update">
                                                @csrf @method('PUT')
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label>Amount</label>
                                                        <input type="number" step="0.01" min="0" name="amount" class="form-control" value="{{ $fs->amount }}" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Term</label>
                                                        <select name="term" class="form-control select">
                                                            @foreach($terms as $term)
                                                                <option value="{{ $term }}" {{ $fs->term == $term ? 'selected' : '' }}>{{ $term }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="form-check-label">
                                                            <input type="checkbox" name="is_active" class="form-check-input-styled" {{ $fs->is_active ? 'checked' : '' }} data-fouc>
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
                            @if($fee_structures->count() < 1)
                                <tr><td colspan="7" class="text-center text-muted">No fee structures yet.</td></tr>
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection