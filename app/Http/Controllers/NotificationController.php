<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller as Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $data['title'] = 'My Notifications';
        $data['template'] = 'notification.list';

        $query = auth()->user()->notifications();

        if ($request->filled('search')) {
            $searchTerm = $request->search;

            $query->where(function ($q) use ($searchTerm) {
                $q->where('data->title', 'like', '%' . $searchTerm . '%')
                    ->orWhere('data->message', 'like', '%' . $searchTerm . '%');
            });
        }

        $data['notifications'] = $query->get();

        $userIds = $data['notifications']->pluck('data.user_id')->filter()->unique()->values();

        $users = User::whereIn('id', $userIds)->get(['id', 'name', 'profile_pic'])->keyBy('id');

        foreach ($data['notifications'] as $notification) {
            $userId = $notification->data['user_id'] ?? null;
            $notification->user = $userId ? $users->get($userId) : null;
        }

        return view('with_login_common', compact('data'));
    }

    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();
        return redirect()->back()->with('success', 'All notifications marked as read.');
    }

    public function deleteAll()
    {
        $user = auth()->user();

        if ($user->notifications()->count() > 0) {
            $user->notifications()->delete();
            return redirect()->back()->with('success', 'All notifications deleted successfully.');
        }

        return redirect()->back()->with('error', 'No notifications to delete.');
    }

    public function delete($id)
    {
        $notification = auth()->user()->notifications()->find($id);

        if ($notification) {
            $notification->delete();
            return redirect()->back()->with('success', 'Notification deleted successfully.');
        }

        return redirect()->back()->with('error', 'Notification not found.');
    }

}
