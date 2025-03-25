<?php

namespace App\Http\Controllers;

use App\Models\CoinPackage;
use Illuminate\Http\Request;

class RewardPlanController extends Controller
{
    public function index()
    {
        $data['title'] = 'Reward Plans';
        $data['template'] = 'admin.reward.list';
        $packages = CoinPackage::orderBy('created_at', 'desc')
            ->get();

        return view('with_login_common', compact('data', 'packages'));
    }

    public function create(){

        $data['title'] = 'Add Reward Plan';
        $data['template'] = 'admin.reward.add';

        return view('with_login_common', compact('data'));
    }

    public function store(Request $request)
    {
        // Validate request
        $formData = $request->validate([
            'name'  => 'required|string|max:50',
            'coins' => 'required|numeric|max:9999',
            'price' => 'required|numeric|max:9999',
        ]);

        try {
            // Create a new CoinPackage record
            CoinPackage::create($formData);

            return redirect()->back()->with('success', 'Reward created successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong! ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $package = CoinPackage::findOrFail($id);
        $data['title'] = 'Edit Reward Plan';
        $data['template'] = 'admin.reward.edit';
        return view('with_login_common', compact('data', 'package'));
    }

    public function update(Request $request, $id)
    {
        $package = CoinPackage::findOrFail($id);

        $formData = $request->validate([
            'name' => 'required|string|max:50',
            'coins' => 'required|numeric|max:9999',
            'price' => 'required|numeric|max:9999',
        ]);

        $package->update($formData);

        return redirect()->back()->with('success', 'Reward updated successfully!');
    }
}
