<?php

namespace App\Http\Controllers;

use App\Helpers\QuickBooksHelper;
use App\Models\IntuitToken;
use App\Services\QuickBooksService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use QuickBooksOnline\API\Core\OAuth\OAuth2\OAuth2LoginHelper;


class QuickBookController extends Controller
{

    public function connect()
    {
        $oauth2LoginHelper = new OAuth2LoginHelper(
            config('quickbooks.client_id'),
            config('quickbooks.client_secret'),
            config('quickbooks.redirect_uri'),
                    'com.intuit.quickbooks.accounting'

        );

        $authorizationUrl = $oauth2LoginHelper->getAuthorizationCodeURL();

        return redirect($authorizationUrl);
    }

    public function store(Request $request)
    {
        $data = [
            'display_name' => 'Shoaib Ansari1',
            'first_name' => 'Shoaib3',
            'last_name' => 'Ansari3',
            'email' => 'shoaib234@example.com',
            'phone' => '+923001234567'
        ];

        $result = QuickBooksHelper::createCustomer($data);

        return response()->json($result);
    }

    public function transferPayment(Request $request){

        $customerId = $request->customer_id;
        $amount = $request->amount;

        $result = QuickBooksHelper::transferRewards($customerId, $amount);

        return response()->json($result);
    }

    public function callback(Request $request)
    {
        if (!$request->has('code')) {
            return response()->json(['error' => 'Authorization Code not found']);
        }

        $oauth2LoginHelper = new OAuth2LoginHelper(
            config('quickbooks.client_id'),
            config('quickbooks.client_secret'),
            config('quickbooks.redirect_uri')
        );

        $accessToken = $oauth2LoginHelper->exchangeAuthorizationCodeForToken($request->code, $request->realmId);

        IntuitToken::updateOrCreate(
            ['realm_id' => $request->query('realmId')],
            [
                'access_token' => $accessToken->getAccessToken(),
                'refresh_token' => $accessToken->getRefreshToken(),
                'expires_at' => Carbon::now()->addSeconds($accessToken->getAccessTokenExpiresAt())
            ]
        );

        return response()->json(['message' => 'QuickBooks Connected Successfully']);
    }

}
