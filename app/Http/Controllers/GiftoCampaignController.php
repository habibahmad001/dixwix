<?php

namespace App\Http\Controllers;

use App\Models\GiftoCampaign;
use App\Models\Group;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\ApiErrorException;
use App\Services\GiftoGramService;

class GiftoCampaignController extends Controller
{
    protected $giftoGramService;
    /**
     * Display a listing of the resource.
     */
    public function __construct(GiftoGramService $giftoGramService)
    {
        $this->giftoGramService = $giftoGramService;
    }
    public function index()
    {
        try {
            $campaigns = GiftoCampaign::latest()->paginate(10);
            return response()->json($campaigns);
        } catch (\Exception $e) {
            Log::error('Error fetching campaigns: '.$e->getMessage());
            return response()->json(['error' => 'Failed to fetch campaigns.'], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Optional if using Vue/React frontend - can be skipped
        return response()->json(['message' => 'Display form to create campaign.']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'compaign_name' => 'required|string|max:255',
            'compaign_uuid' => 'nullable|string|max:255',
            'compaign_status' => 'nullable|string|max:255',
            'group_id' => 'nullable|array',
            'compaign_denominations' => 'nullable|string|max:255',
            'status' => 'nullable|in:enabled,disabled',
            'card_bg' => 'nullable|string|max:255',
            'card_title' => 'nullable|string|max:255',
            'card_message' => 'nullable|string',
        ]);

        try {
            $campaign = GiftoCampaign::create($validated);
            return response()->json(['message' => 'Campaign created successfully.', 'data' => $campaign]);
        } catch (\Exception $e) {
            Log::error('Error creating campaign: '.$e->getMessage());
            return response()->json(['error' => 'Failed to create campaign.'], 500);
        }
    }

    public function setupCampaignView($id)
    {
        try {
            // Fetch campaign using GiftoGramService
            $campaign = $this->giftoGramService->getCampaignById($id);
            $funds = ($this->giftoGramService->getFunding())["data"]["data"]["credit_available"];
            $points = (($this->giftoGramService->getFunding())["data"]["data"]["credit_available"]) * 100;

            // Check if campaign exists
            if (empty($campaign) || !isset($campaign['data'])) {
                return redirect()->back()->with('error', "Campaign not found!");
            }

            // Fetch available groups (adjust as necessary)
            $availableGroups = Group::get();
            $giftoInfo = GiftoCampaign::where("compaign_uuid", $id)->first();

            // Prepare data to pass to the view
            $data['title'] = 'Campaign Settings';
            $data['template'] = 'admin.campaign.campaign-setting';  // Template view name

            // Pass campaign data and available groups to the view
            return view('with_login_common', compact('data', 'campaign', 'availableGroups', 'id', 'giftoInfo', 'funds', 'points'));
        } catch (\Exception $e) {
            Log::error('Error fetching campaign: ' . $e->getMessage());
            return response()->json(['error' => 'Campaign not found.'], 404);
        }
    }


    public function setupCampaign(Request $request)
    {
        // Validate the incoming request data
        $validated = $request->validate([
            'compaign_uuid' => 'required|string|max:255',
            'group_id' => 'required|numeric', // group_id is a single ID, not array
            'card_title' => 'required|string|max:255', // card_title is required based on frontend validation
            'card_message' => 'nullable|string',
            'card_bg' => 'nullable|array', // Expecting an array of images
            'card_bg.*' => 'image|mimes:jpg,jpeg,png,bmp,gif|max:25600', // Each image must be valid
        ]);

        try {

            // Fetch the campaign using GiftoGramService by the UUID
            $campaignData = $this->giftoGramService->getCampaignById($request->compaign_uuid);

            // Check if the campaign data exists
            if (empty($campaignData) || !isset($campaignData['data'])) {
                return redirect()->back()->with('error', 'Campaign data not found!');
            }

            // Prepare attributes for saving
            $campaignAttributes = [
                'compaign_uuid' => $request->compaign_uuid,
                'group_id' => $request->group_id,
                'card_title' => $request->card_title,
                'card_message' => $request->card_message,
                'compaign_name' => $campaignData['data']['data']['name'],
                'compaign_denominations' => json_encode($campaignData['data']['data']['denominations']),
                'compaign_status' => (bool) $campaignData['data']['data']['active'],
            ];

            $currentPoints = 0;
            // Handle card background image uploads
            if ($request->hasFile('card_bg')) {
                $cardBgPaths = [];
                foreach ($request->file('card_bg') as $index => $file) {
                    // Get the original file name without extension
                    $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

                    $currentPoints += $request->image_price[$index];
                    // Build structured array for each image
                    $cardBgPaths[$originalName] = [
                        'id' => $index + 1,
                        'name' => $request->image_name[$index] ?? $originalName, // fallback if name missing
                        'path' => $file->store('campaign/card/bg', 'public'),
                        'filename' => $originalName,
                        'price' => $request->image_price[$index] ?? null,
                    ];
                }
                $campaignAttributes['card_bg'] = json_encode($cardBgPaths); // Store as JSON
            }

            if ($request->totalPoints <= $currentPoints) {
                return response()->json(['status' => 'error', 'message' => "Your current points are insufficient to proceed and complete the campaign setup."], 400);
            }

            // Save or update the campaign
            $campaign = GiftoCampaign::updateOrCreate(
                ['compaign_uuid' => $request->compaign_uuid],
                $campaignAttributes
            );

            return response()->json(['status' => 'success', 'message' => "Campaign configured successfully!"]);
        } catch (\Exception $e) {
            Log::error('Error configuring campaign: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => "Error configuring the campaign. Please try again."], 400);
        }
    }



    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $campaign = GiftoCampaign::findOrFail($id);
            return response()->json($campaign);
        } catch (\Exception $e) {
            Log::error('Error fetching campaign: '.$e->getMessage());
            return response()->json(['error' => 'Campaign not found.'], 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $campaign = GiftoCampaign::findOrFail($id);
            return response()->json($campaign);
        } catch (\Exception $e) {
            Log::error('Error fetching campaign for edit: '.$e->getMessage());
            return response()->json(['error' => 'Campaign not found.'], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'compaign_name' => 'sometimes|required|string|max:255',
            'compaign_uuid' => 'nullable|string|max:255',
            'compaign_status' => 'nullable|string|max:255',
            'group_id' => 'nullable|array',
            'compaign_denominations' => 'nullable|string|max:255',
            'status' => 'nullable|in:enabled,disabled',
            'card_bg' => 'nullable|string|max:255',
            'card_title' => 'nullable|string|max:255',
            'card_message' => 'nullable|string',
        ]);

        try {
            $campaign = GiftoCampaign::findOrFail($id);
            $campaign->update($validated);

            return response()->json(['message' => 'Campaign updated successfully.', 'data' => $campaign]);
        } catch (\Exception $e) {
            Log::error('Error updating campaign: '.$e->getMessage());
            return response()->json(['error' => 'Failed to update campaign.'], 500);
        }
    }

    /**
     * Remove the specified resource from storage (soft delete).
     */
    public function destroy(string $id)
    {
        try {
            $campaign = GiftoCampaign::findOrFail($id);
            $campaign->delete();

            return response()->json(['message' => 'Campaign deleted successfully.']);
        } catch (\Exception $e) {
            Log::error('Error deleting campaign: '.$e->getMessage());
            return response()->json(['error' => 'Failed to delete campaign.'], 500);
        }
    }
}
