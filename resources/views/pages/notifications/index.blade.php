@extends('layouts.master')
@section('page_title', 'Notifications')
@section('content')

    <div class="row">
        <div class="col-md-3 mb-3">
            <div class="card">
                <div class="card-body text-center">
                    <i class="icon-bell2 icon-2x text-primary d-block mb-2"></i>
                    <h3 class="font-weight-semibold mb-0">{{ $unread_count }}</h3>
                    <span class="text-muted">Unread</span>
                </div>
                <div class="card-footer d-flex justify-content-between">
                    <form method="post" action="{{ route('notifications.read_all') }}">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-primary"><i class="icon-checkmark2 mr-1"></i>Mark all read</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-9">
            <div class="card">
                <div class="card-header header-elements-inline">
                    <h5 class="card-title"><i class="icon-bell2 mr-2 text-primary"></i>All Notifications</h5>
                    {!! Qs::getPanelOptions() !!}
                </div>
                <div class="card-body p-0">
                    @foreach($notifications as $n)
                        <div class="d-flex justify-content-between align-items-center border-bottom p-3 {{ $n->is_read ? 'bg-light' : '' }}">
                            <div class="media-body pr-3">
                                <a href="{{ route('notifications.read', $n->id) }}" class="d-flex justify-content-between text-default">
                                    <span class="font-weight-semibold {{ $n->is_read ? 'text-muted' : '' }}">{{ $n->title }}</span>
                                    <span class="text-muted font-size-xs">{{ $n->created_at ? $n->created_at->diffForHumans() : '' }}</span>
                                </a>
                                <p class="mb-0 text-muted">{{ $n->message }}</p>
                            </div>
                            <div class="d-flex align-items-center">
                                @if(!$n->is_read)
                                    <span class="badge badge-info mr-2">new</span>
                                @endif
                                <form method="post" action="{{ route('notifications.destroy', $n->id) }}" onsubmit="return confirm('Delete this notification?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-light"><i class="icon-trash text-danger"></i></button>
                                </form>
                            </div>
                        </div>
                    @endforeach

                    @if($notifications->isEmpty())
                        <p class="text-muted text-center py-4 mb-0">You have no notifications.</p>
                    @endif
                </div>
            </div>

            <div class="d-flex justify-content-center">
                {{ $notifications->links() }}
            </div>
        </div>
    </div>
@endsection