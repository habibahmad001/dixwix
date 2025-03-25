<?php

namespace App\Services;

class QuickBooksService
{
    protected $dataService;

    public function __construct()
    {
        $this->dataService = app('quickbooks');
    }

    public function refreshToken()
    {
        $OAuth2LoginHelper = $this->dataService->getOAuth2LoginHelper();
        $accessToken = $OAuth2LoginHelper->refreshAccessTokenWithRefreshToken(config('quickbooks.refresh_token'));

        if ($accessToken) {
            file_put_contents(storage_path('access_token.txt'), json_encode($accessToken));
            return [
                'status' => 'success',
                'access_token' => $accessToken->getAccessToken(),
                'refresh_token' => $accessToken->getRefreshToken(),
            ];
        }

        return ['status' => 'error', 'message' => 'Token Refresh Failed'];
    }

    public function createCustomer($customerData)
    {
        $customer = \QuickBooksOnline\API\Facades\Customer::create($customerData);
        $result = $this->dataService->Add($customer);
        $error = $this->dataService->getLastError();

        if ($error) {
            if ($error->getHttpStatusCode() == 401) {
                $this->refreshToken();
                return $this->createCustomer($customerData);
            }

            return ['status' => 'error', 'message' => $error->getResponseBody()];
        }

        return ['status' => 'success', 'customer_id' => $result->Id];
    }
}


