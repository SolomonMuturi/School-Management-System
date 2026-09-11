<div class="card">
    <div class="card-header bg-white header-elements-inline">
        <h6 class="card-title">Documents</h6>
        @if(Qs::userIsTeamSAT())
            <div class="header-elements">
                <a href="{{ route('students.documents', Qs::hash($sr->id)) }}" class="btn btn-primary btn-sm">Manage Documents</a>
            </div>
        @endif
    </div>
    <div class="card-body">
        @if($documents->count())
            <table class="table table-bordered">
                <thead class="thead-light">
                    <tr>
                        <th>Title</th>
                        <th>Type</th>
                        <th>Uploaded</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($documents as $doc)
                        <tr>
                            <td>{{ $doc->title }}</td>
                            <td>{{ $doc->doc_type ?: '-' }}</td>
                            <td>{{ $doc->created_at ? $doc->created_at->format('d M Y') : '-' }}</td>
                            <td>
                                @if(Qs::userIsTeamSAT())
                                    <a href="{{ route('students.documents.download', [Qs::hash($sr->id), $doc->id]) }}" class="btn btn-secondary btn-sm">Download</a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p class="text-muted mb-0">No documents uploaded yet.</p>
        @endif
    </div>
</div>