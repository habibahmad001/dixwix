<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Group;
use App\Models\Ticket;
use App\Notifications\GeneralNotification;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'description' => 'required|string|max:255',
            'group_id' => 'required|exists:group,id',
        ]);

        $group = Group::with(['groupmembers' => function ($query) {
            $query->where('member_role', 'admin')->where('activated', true)->where("member_id", auth()->id());
        }])->find($request->group_id);

        $optionsForAssigning = $group->groupmembers->pluck('member_id')->toArray();
        if ($group->created_by && empty($optionsForAssigning)) {
            $optionsForAssigning[] = $group->created_by;
        }

        $adminId = $optionsForAssigning[array_rand($optionsForAssigning)];

        $ticket = Ticket::create([
            'description' => $request->description,
            'group_id' => $request->group_id,
            'user_id' => auth()->id(),
            'admin_id' => $adminId,
        ]);

        $ticketAssignNotification = [
            'title' => 'Ticket assigned to you',
            'type' => 'ticket_assigned',
            'subject' => 'Ticket assigned to you',
            'message' => "One of the group members, {$ticket->user->name}, has booked a ticket",
            'user_id' => auth()->id(),
            'url' => url("show-group/{$request->group_id}"),
            'action' => 'View Ticket',
        ];

        $ticket->admin->notify(new GeneralNotification($ticketAssignNotification));

        return response()->json([
            'success' => true,
            'message' => 'Ticket submitted successfully!',
            'data' => $ticket,
        ]);

    }

    public function ticketCommentStore(Request $request)
    {
        $request->validate([
            'ticket_id' => 'required|exists:tickets,id',
            'comment' => 'required|string|max:1000',
        ]);

        $ticket = Ticket::find($request->ticket_id);

        $comment = $ticket->comments()->create([
            'user_id' => auth()->id(),
            'comment' => $request->comment,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Comment added successfully!',
            'comment' => $comment,
            'user' => auth()->user(),
        ]);

    }

    public function destroy($id)
    {
        $ticket = Ticket::findOrFail($id);

        $ticket->delete();

        return response()->json(['success' => true, 'message' => 'Ticket deleted successfully.']);
    }

    public function ticketCommentDestroy($id)
    {
        $comment = Comment::findOrFail($id);
        $comment->delete();

        return response()->json(['success' => true, 'message' => 'Comment deleted successfully.']);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate(['status' => 'required|in:open,resolved,closed']);

        $ticket = Ticket::findOrFail($id);

        $ticket->update(['status' => $request->status]);

        return response()->json(['success' => true, 'message' => 'Ticket status updated successfully.']);
    }

}
