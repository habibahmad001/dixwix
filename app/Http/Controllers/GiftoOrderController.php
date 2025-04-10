<?php

namespace App\Http\Controllers;

use App\Models\GiftoOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class GiftoOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {

            $data['title'] = 'Gifto Orders';
            $data['template'] = 'admin.gifto.orders.list';

            $orders = GiftoOrder::latest()->get();

            return view('with_login_common', compact('data', 'orders'));
        } catch (\Exception $e) {
            Log::error('Error fetching campaign: ' . $e->getMessage());
            return response()->json(['error' => 'Campaign not found.'], 404);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'userEmail' => 'nullable|string|max:256',
            'userName' => 'nullable|string|max:256',
            'points' => 'nullable|string|max:256',
            'giftoAmount' => 'nullable|string|max:256',
            'giftoMsg' => 'nullable|string|max:256',
            'campaignUuid' => 'nullable|string|max:256|exists:gifto_campaigns,uuid',
            'selectedCard' => 'nullable|string',
            'cardPath' => 'nullable|string',
            'orderStatus' => 'in:active,inactive,processed,completed',
            'status' => 'in:yes,no',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $order = GiftoOrder::create($request->all());

        return response()->json(['message' => 'Gifto Order created successfully.', 'data' => $order], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $order = GiftoOrder::find($id);

        if (!$order) {
            return response()->json(['message' => 'Order not found.'], 404);
        }

        return response()->json($order);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $order = GiftoOrder::find($id);

        if (!$order) {
            return response()->json(['message' => 'Order not found.'], 404);
        }

        $validator = Validator::make($request->all(), [
            'userEmail' => 'nullable|string|max:256',
            'userName' => 'nullable|string|max:256',
            'points' => 'nullable|string|max:256',
            'giftoAmount' => 'nullable|string|max:256',
            'giftoMsg' => 'nullable|string|max:256',
            'campaignUuid' => 'nullable|string|max:256|exists:gifto_campaigns,uuid',
            'selectedCard' => 'nullable|string',
            'cardPath' => 'nullable|string',
            'orderStatus' => 'in:active,inactive,processed,completed',
            'status' => 'in:yes,no',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $order->update($request->all());

        return response()->json(['message' => 'Gifto Order updated successfully.', 'data' => $order]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $order = GiftoOrder::find($id);

        if (!$order) {
            return response()->json(['message' => 'Order not found.'], 404);
        }

        $order->delete();

        return response()->json(['message' => 'Gifto Order deleted successfully.']);
    }
}
