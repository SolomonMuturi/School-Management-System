@extends('layouts.master')
@section('page_title', 'Student History - '.$sr->user->name)
@section('content')

<div class="card">
    <div class="card-header bg-white header-elements-inline">
        <h6 class="card-title">{{ $sr->user->name }} - Complete Student History</h6>
        <div class="header-elements">
            <a href="{{ route('students.show', Qs::hash($sr->id)) }}" class="btn btn-secondary btn-sm">Back to Profile</a>
        </div>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-3"><strong>Admission:</strong> {{ $sr->year_admitted ?: 'N/A' }}</div>
            <div class="col-md-3"><strong>Current Class:</strong> {{ $sr->my_class->name }}</div>
            <div class="col-md-3"><strong>Session:</strong> {{ $sr->session }}</div>
            <div class="col-md-3"><strong>Status:</strong>
                <span class="badge badge-{{ $sr->status == 'active' ? 'success' : 'secondary' }}">{{ ucfirst($sr->status) }}</span>
            </div>
        </div>

        @if($promotions->count())
            <h6 class="card-title">Class History (Promotions)</h6>
            <table class="table table-bordered mb-4">
                <thead class="thead-light">
                    <tr>
                        <th>From</th>
                        <th>To</th>
                        <th>Session</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($promotions as $p)
                        <tr>
                            <td>{{ $p->fc->name }}</td>
                            <td>{{ $p->tc->name }}</td>
                            <td>{{ $p->from_session.' - '.$p->to_session }}</td>
                            <td>
                                @if($p->grad == 1)
                                    <span class="badge badge-success">Graduated</span>
                                @elseif($p->status == 'done')
                                    <span class="badge badge-primary">Promoted</span>
                                @else
                                    <span class="badge badge-secondary">Pending</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <h6 class="card-title">Timeline of Changes & Events</h6>
        @if($events->count())
            <table class="table table-bordered table-striped">
                <thead class="thead-light">
                    <tr>
                        <th>Date / Time</th>
                        <th>Event</th>
                        <th>Description</th>
                        <th>Recorded By</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($events as $evt)
                        <tr>
                            <td>{{ $evt->created_at ? $evt->created_at->format('d M Y H:i') : '-' }}</td>
                            <td><span class="badge badge-secondary">{{ str_replace('_', ' ', ucfirst($evt->event_type)) }}</span></td>
                            <td>{{ $evt->description }}</td>
                            <td>{{ $evt->recorder ? $evt->recorder->name : '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p class="text-muted mb-0">No recorded events yet.</p>
        @endif
    </div>
</div>
@endsection