<?php

namespace App\Http\Controllers;

use App\Models\Point;
use App\Models\TransferRequest;
use App\Models\User;
use Illuminate\Http\Request;

class TransferRequestController extends Controller
{
    public function index()
    {
        $data['title'] = 'Transfer Point Requests';
        $data['template'] = 'admin.reward.transfer-req-list';
        $transferRequests = TransferRequest::orderBy('created_at', 'desc')->get();

        return view('with_login_common', compact('data', 'transferRequests'));
    }

    public function edit($id)
    {
        $transferRequest = TransferRequest::findOrFail($id);
        $data['title'] = 'Edit Transfer Point Request';
        $data['template'] = 'admin.reward.edit-transfer-request';

        return view('with_login_common', compact('data', 'transferRequest'));
    }

    public function update(Request $request, $id){

        $request->validate([
            'status' => 'required|in:1,2',
        ]);

        $transferRequest = TransferRequest::find($id);
        $fromUser = User::findOrFail($transferRequest->from_user_id);
        $toUser = User::findOrFail($transferRequest->to_user_id);

        if ($request->status == TransferRequest::APPROVED) {

            if ($fromUser->reward_balance < $transferRequest->points) {
                return redirect()->back()->with('error', "User does not have enough points to approve this request.");
            }

            // Process the point transfer
            $this->processPointTransfer($fromUser, $toUser, $transferRequest->points);
        }

        $transferRequest->status = $request->status;
        $transferRequest->approved_by = auth()->user()->id;
        $transferRequest->approved_at = now();
        $transferRequest->save();

        return redirect()->route('transfer-requests')->with('success', 'Point Transfer request updated successfully.');
    }

    private function processPointTransfer($fromUser, $toUser, $points)
    {
        Point::create([
            'user_id' => $fromUser->id,
            'type' => 'debit',
            'points' => $points,
            'description' => "Points gifted to {$toUser->name}",
        ]);

        $fromUser->reward_balance -= $points;
        $fromUser->save();

        Point::create([
            'user_id' => $toUser->id,
            'type' => 'credit',
            'points' => $points,
            'trans_type' => Point::TRANS_TYPE_GIFT,
            'description' => "Gift received from {$fromUser->name}",
        ]);

        $toUser->reward_balance += $points;
        $toUser->save();
    }
}
