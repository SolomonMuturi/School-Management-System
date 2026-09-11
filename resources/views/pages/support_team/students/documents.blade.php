@extends('layouts.master')
@section('page_title', 'Student Documents - '.$sr->user->name)
@section('content')

<div class="card">
    <div class="card-header bg-white header-elements-inline">
        <h6 class="card-title">{{ $sr->user->name }} - Documents</h6>
        <div class="header-elements">
            <a href="{{ route('students.show', Qs::hash($sr->id)) }}" class="btn btn-secondary btn-sm">Back to Profile</a>
        </div>
    </div>
    <div class="card-body">
        <form method="post" action="{{ route('students.documents.store', Qs::hash($sr->id)) }}" enctype="multipart/form-data" autocomplete="off">
            @csrf
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Title <span class="text-danger">*</span></label>
                        <input required type="text" name="title" class="form-control" placeholder="e.g. Birth Certificate">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Document Type</label>
                        <select name="doc_type" class="select-search form-control">
                            <option value="">Choose..</option>
                            @foreach($doc_types as $dt)
                                <option value="{{ $dt }}">{{ $dt }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>File <span class="text-danger">*</span></label>
                        <input required type="file" name="doc" class="form-input-styled" data-fouc>
                        <span class="form-text text-muted">PDF, Images, Word/Excel. Max 5Mb</span>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group mt-4">
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label>Notes</label>
                <input type="text" name="notes" class="form-control" maxlength="200">
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        @if($documents->count())
            <table class="table table-bordered table-striped">
                <thead class="thead-light">
                    <tr>
                        <th>Title</th>
                        <th>Type</th>
                        <th>Notes</th>
                        <th>Uploaded</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($documents as $doc)
                        <tr>
                            <td>{{ $doc->title }}</td>
                            <td>{{ $doc->doc_type ?: '-' }}</td>
                            <td>{{ $doc->notes ?: '-' }}</td>
                            <td>{{ $doc->created_at ? $doc->created_at->format('d M Y H:i') : '-' }}</td>
                            <td>
                                <a href="{{ route('students.documents.download', [Qs::hash($sr->id), $doc->id]) }}" class="btn btn-secondary btn-sm">Download</a>
                                <form class="d-inline" method="post" action="{{ route('students.documents.destroy', [Qs::hash($sr->id), $doc->id]) }}" onsubmit="return confirm('Delete this document?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p class="text-muted mb-0">No documents uploaded for this student yet.</p>
        @endif
    </div>
</div>
@endsection