<?php

namespace App\Http\Controllers;

use App\Models\Membershipplan;
use Illuminate\Http\Request;

class SubscriptionPlanController extends Controller
{
    public function index()
    {
        $data['title'] = 'Subscription Plans';
        $data['template'] = 'admin.plan.list';
        $plans = Membershipplan::all();

        return view('with_login_common', compact('data', 'plans'));
    }

    public function edit($id)
    {
        $plan = Membershipplan::findOrFail($id);
        $data['title'] = 'Edit Subscription Plan';
        $data['template'] = 'admin.plan.edit';
        return view('with_login_common', compact('data', 'plan'));
    }

    public function update(Request $request, $id)
    {
        $plan = Membershipplan::findOrFail($id);

        $formData = $request->validate([
            'name' => 'required|string|max:50',
            'allowed_groups' => 'required|numeric|min:0',
            'allowed_items' => 'required|numeric|min:0',
            'price' => 'nullable|numeric',
            'stripe_price_id' => 'nullable|string',
        ]);

        $plan->update($formData);

        return redirect()->back()->with('success', 'User updated successfully!');
    }
}
