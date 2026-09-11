<?php

namespace App\Http\Controllers\SupportTeam;

use App\User;
use App\Models\ActivityLog;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $req)
    {
        $logs = ActivityLog::with('user')->orderByDesc('id');

        if ($req->filled('action')) {
            $logs->where('action', $req->action);
        }
        if ($req->filled('user_id')) {
            $logs->where('user_id', $req->user_id);
        }
        if ($req->filled('date_from')) {
            $logs->whereDate('created_at', '>=', $req->date_from);
        }
        if ($req->filled('date_to')) {
            $logs->whereDate('created_at', '<=', $req->date_to);
        }
        if ($req->filled('search')) {
            $search = $req->search;
            $logs->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('module', 'like', "%{$search}%")
                  ->orWhere('route', 'like', "%{$search}%")
                  ->orWhere('ip', 'like', "%{$search}%");
            });
        }

        $d['logs'] = $logs->paginate(100)->withQueryString();
        $d['actions'] = ActivityLog::distinct()->orderBy('action')->pluck('action');
        $d['users'] = User::whereIn('id', ActivityLog::distinct()->pluck('user_id'))
            ->orderBy('name')->get(['id', 'name', 'user_type']);
        $d['query'] = $req->only(['action', 'user_id', 'date_from', 'date_to', 'search']);

        return view('pages.support_team.users.activity', $d);
    }
}