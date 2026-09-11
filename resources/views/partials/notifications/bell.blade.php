@php
    $unreadCount = \App\Models\AppNotification::unreadCount(Auth::id());
    $recentNotifications = \App\Models\AppNotification::where('user_id', Auth::id())->orderByDesc('id')->limit(6)->get();
@endphp
<li class="nav-item dropdown">
    <a href="#" class="navbar-nav-link dropdown-toggle" data-toggle="dropdown">
        <i class="icon-bell2"></i>
        <span class="badge badge-danger badge-pill ml-1 {{ $unreadCount === 0 ? 'd-none' : '' }}" id="notif-badge">{{ $unreadCount }}</span>
    </a>

    <div class="dropdown-menu dropdown-menu-right dropdown-content wmin-md-350">
        <div class="dropdown-content-header">
            <span class="font-weight-semibold">Notifications</span>
            <form method="post" action="{{ route('notifications.read_all') }}" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-link btn-sm p-0 text-primary">Mark all read</button>
            </form>
        </div>

        <div class="dropdown-content-body dropdown-scrollable" style="max-height: 320px; overflow-y: auto;">
            @foreach($recentNotifications as $n)
                <div class="media">
                    <div class="media-body">
                        <a href="{{ route('notifications.read', $n->id) }}" class="d-block text-default">
                            <div class="d-flex justify-content-between">
                                <span class="font-weight-semibold {{ $n->is_read ? 'text-muted' : '' }}">{{ $n->title }}</span>
                                <span class="text-muted font-size-xs">{{ $n->created_at ? $n->created_at->diffForHumans() : '' }}</span>
                            </div>
                            <span class="d-block text-muted font-size-sm">{{ Str::limit($n->message, 70) }}</span>
                        </a>
                    </div>
                    @if(!$n->is_read)
                        <span class="ml-2 mt-1"><span class="badge badge-info">new</span></span>
                    @endif
                </div>
                @if(!$loop->last)
                    <div class="dropdown-divider"></div>
                @endif
            @endforeach

            @if($recentNotifications->isEmpty())
                <p class="text-muted text-center mb-0 py-3">No notifications yet.</p>
            @endif
        </div>

        <div class="dropdown-content-footer">
            <a href="{{ route('notifications.index') }}" class="btn btn-light btn-block">View all notifications</a>
        </div>
    </div>
</li>

<script>
    $(function () {
        $('.dropdown-content-body a').on('click', function () {
            var badge = $('#notif-badge');
            if (badge.length && !badge.hasClass('d-none')) {
                var count = parseInt(badge.text() || '0');
                count = Math.max(0, count - 1);
                badge.text(count);
                if (count === 0) badge.addClass('d-none');
            }
        });
    });
</script>