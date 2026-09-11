<div class="card">
    <div class="card-header bg-white header-elements-inline">
        <h6 class="card-title">Student History</h6>
        @if(Qs::userIsTeamSAT())
            <div class="header-elements">
                <a href="{{ route('students.history', Qs::hash($sr->id)) }}" class="btn btn-primary btn-sm">Full History</a>
            </div>
        @endif
    </div>
    <div class="card-body">
        @if($events->count())
            <div class="timeline timeline-left mt-2">
                @foreach($events->take(15) as $evt)
                    <div class="timeline-container">
                        <div class="timeline-row">
                            <div class="timeline-date text-muted">{{ $evt->created_at ? $evt->created_at->format('d M Y H:i') : '' }}</div>
                            <h6 class="timeline-title font-weight-semibold">{{ str_replace('_', ' ', ucfirst($evt->event_type)) }}</h6>
                            <div class="timeline-content">{{ $evt->description }}</div>
                            @if($evt->recorder && Qs::userIsTeamSAT())
                                <div class="text-muted small">By: {{ $evt->recorder->name }}</div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-muted mb-0">No recorded history events yet.</p>
        @endif
    </div>
</div>