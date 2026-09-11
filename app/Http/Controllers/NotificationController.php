<?php

namespace App\Http\Controllers;

use App\Helpers\Qs;
use App\Models\AppNotification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $req)
    {
        $d['notifications'] = AppNotification::where('user_id', auth()->id())
            ->orderByDesc('id')
            ->paginate(30);

        $d['unread_count'] = AppNotification::unreadCount(auth()->id());
        $d['recent'] = AppNotification::where('user_id', auth()->id())->orderByDesc('id')->limit(5)->get();

        return view('pages.notifications.index', $d);
    }

    public function markRead($id)
    {
        $notification = AppNotification::where('id', $id)->where('user_id', auth()->id())->first();
        if (!$notification) {
            return Qs::goWithDanger();
        }

        $notification->update(['is_read' => true]);

        if ($notification->link) {
            return redirect($notification->link);
        }

        return redirect()->route('notifications.index');
    }

    public function markAll()
    {
        AppNotification::where('user_id', auth()->id())->where('is_read', false)
            ->update(['is_read' => true]);

        return back()->with('flash_success', 'All notifications marked as read.');
    }

    public function destroy($id)
    {
        AppNotification::where('id', $id)->where('user_id', auth()->id())->delete();
        return back()->with('flash_success', __('msg.del_ok'));
    }
}