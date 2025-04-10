<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class GiftoGramService
{
    protected $apiKey;
    protected $baseUrl;
    protected $campaignId;

    public function __construct()
    {
        $this->apiKey = env('GIFTOGRAM_API_KEY');
        $this->baseUrl = env('GIFTOGRAM_API_URL', 'https://sandbox-api.giftogram.com/api/v1/');
        $this->campaignId = env('GIFTOGRAM_CAMPAIGN_ID');
    }

    public function sendGift($email, $amount, $message, $campaignId, $notes="Sending a gift", $name="John Doe")
    {
        // Ensure the denomination is a multiple of 5
        if ($amount % 5 !== 0) {
            return [
                'success' => false,
                'error' => 'Denomination must be a multiple of 5.'
            ];
        }

        $response = Http::withHeaders([
            'Authorization' => $this->apiKey,
            'Accept' => 'application/json',
        ])->post("{$this->baseUrl}orders", [
            "external_id" => uniqid(),
//            "campaign_id" => $this->campaignId,
            "campaign_id" => $campaignId,
            "notes" => $notes,
            "reference_number" => "ORDER" . rand(1000, 9999),
            "message" => $message,
            "subject" => "Your Giftogram Reward",
            "recipients" => [
                [
                    "email" => $email,
                    "name" => $name
                ]
            ],
            "denomination" => (string) $amount, // Ensure it's a string
        ]);

        return $response->successful()
            ? ['success' => true, 'data' => $response->json()]
            : ['success' => false, 'error' => $response->body()];
    }

    public function getGiftCatalog()
    {
        $response = Http::withHeaders([
            'Authorization' => $this->apiKey,
            'Accept' => 'application/json',
        ])->get("{$this->baseUrl}catalog");

        return $response->successful()
            ? ['success' => true, 'data' => $response->json()]
            : ['success' => false, 'error' => $response->body()];
    }

    public function getFunding()
    {
        $response = Http::withHeaders([
            'Authorization' => $this->apiKey,
            'Accept' => 'application/json',
        ])->get("{$this->baseUrl}funding");

        return $response->successful()
            ? ['success' => true, 'data' => $response->json()]
            : ['success' => false, 'error' => $response->body()];
    }
    public function getOrders()
    {
        $response = Http::withHeaders([
            'Authorization' => $this->apiKey,
            'Accept' => 'application/json',
        ])->get("{$this->baseUrl}orders");

        return $response->successful()
            ? ['success' => true, 'data' => $response->json()]
            : ['success' => false, 'error' => $response->body()];
    }

    public function getCampaigns()
    {
        $response = Http::withHeaders([
            'Authorization' => $this->apiKey,
            'Accept' => 'application/json',
        ])->get("{$this->baseUrl}campaigns");

        return $response->successful()
            ? ['success' => true, 'data' => $response->json()]
            : ['success' => false, 'error' => $response->body()];
    }

    public function getCampaignById($id)
    {
        $response = Http::withHeaders([
            'Authorization' => $this->apiKey,
            'Accept' => 'application/json',
        ])->get("{$this->baseUrl}campaigns/{$id}");

        return $response->successful()
            ? ['success' => true, 'data' => $response->json()]
            : ['success' => false, 'error' => $response->body()];
    }


}


?>
