<?php

namespace App\Helpers;

use QuickBooksOnline\API\Core\OAuth\OAuth2\OAuth2LoginHelper;
use QuickBooksOnline\API\DataService\DataService;
use App\Models\IntuitToken;
use Carbon\Carbon;
use QuickBooksOnline\API\Facades\Customer;
use QuickBooksOnline\API\Facades\Payment;
use QuickBooksOnline\API\Facades\CreditMemo;
use QuickBooksOnline\API\Facades\Deposit;
use QuickBooksOnline\API\Facades\Invoice;

class QuickBooksHelper
{
    public static function getDataService()
    {
        $token = IntuitToken::first();

        if (!$token) {
            return null;
        }

        // Check if token is expired
        if (Carbon::now()->gt(Carbon::parse($token->expires_at))) {
            self::refreshToken();
            $token = IntuitToken::first();
        }

        $dataService = DataService::Configure([
            'auth_mode' => 'oauth2',
            'ClientID' => config('quickbooks.client_id'),
            'ClientSecret' => config('quickbooks.client_secret'),
            'RedirectURI' => config('quickbooks.redirect_uri'),
            'accessTokenKey' => $token->access_token,
            'refreshTokenKey' => $token->refresh_token,
            'QBORealmID' => $token->realm_id,
            'scope' => 'com.intuit.quickbooks.accounting com.intuit.quickbooks.payment',
            'baseUrl' => 'development',
        ]);

        return $dataService;
    }

    public static function refreshToken()
    {
        $token = IntuitToken::first();

        if (!$token) {
            return ['error' => 'Token not found'];
        }

        try {
            $oauth2LoginHelper = new OAuth2LoginHelper(
                config('quickbooks.client_id'),
                config('quickbooks.client_secret'),
                config('quickbooks.redirect_uri')
            );

            $newAccessToken = $oauth2LoginHelper->refreshAccessTokenWithRefreshToken($token->refresh_token);

            // Update Token in Database
            $token->update([
                'access_token' => $newAccessToken->getAccessToken(),
                'refresh_token' => $newAccessToken->getRefreshToken(),
                'expires_at' => Carbon::now()->addSeconds($newAccessToken->getAccessTokenExpiresAt()),
            ]);

            return ['success' => 'Token refreshed successfully'];
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public static function createCustomer($data)
    {
        $dataService = self::getDataService();

        if (!$dataService) {
            return ['error' => 'QuickBooks service not available'];
        }

        try {
            $customer = Customer::create([
                "DisplayName" => $data['display_name'],
                "GivenName" => $data['first_name'],
                "FamilyName" => $data['last_name'],
                "CompanyName" => env('APP_NAME'),
                "PrimaryEmailAddr" => [
                    "Address" => $data['email']
                ],
                "PrimaryPhone" => [
                    "FreeFormNumber" => $data['phone']
                ]
            ]);

            $result = $dataService->Add($customer);
            return $result;
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public static function createPayment($customerId, $amount)
    {
        $dataService = self::getDataService();
        $invoice = self::createInvoice($customerId, $amount);

//        try {
            $payment = Payment::create([
                "CustomerRef" => [
                    "value" => $customerId
                ],
                "TotalAmt" => $amount,
//                'PaymentRefNum' => 'REWARD-' . uniqid(),
                "PaymentMethodRef" => [
                    "value" => "3"
                ],
                "DepositToAccountRef" => [
                    "value" => "35" // Your Business Bank Account ID in QuickBooks
                ],
                "PaymentRefNum" => time(), // Unique Transaction ID
                "TxnDate" => now()->format('Y-m-d'),
                "PrivateNote" => "Reward Points Transfer",
                "CheckPayment" => [
                    "BankAccountRef" => [
                        "value" => $customerId
                    ]
                ]
//                "DepositToAccountRef" => [
//                    "value" => 1
//                ],
//                'Line' => [
//                    [
//                        'Amount' => $amount,
//                        'DetailType' => 'PaymentLineDetail',
//                        'PaymentLineDetail' => [
//                            'ItemRef' => [
//                                'value' => 1, // Item ID (e.g., 1 for Discount)
//                            ],
//                        ],
//                    ],
//                ]
            ]);

            $result = $dataService->Add($payment);
            return $result;
//        } catch (\Exception $e) {
//            return ['error' => $e->getMessage()];
//        }
    }

    public static function createInvoice($customerId, $amount)
    {
        $dataService = self::getDataService();

        $invoice = Invoice::create([
            "CustomerRef" => [
                "value" => $customerId
            ],
            "TxnDate" => now()->format('Y-m-d'),
            "PrivateNote" => "Reward Points Transfer Invoice",
            "Line" => [
                [
                    "Amount" => $amount,
                    "DetailType" => "DescriptionOnly",
                    "Description" => "Reward Points Transfer"
                ]
            ]
        ]);

        $result = $dataService->Add($invoice);

        return $result;
    }


    public static function transferRewards($customerId, $amount)
    {
        $dataService = self::getDataService();

        $deposit = [
            "DepositToAccountRef" => [
                "name" => "Checking",
                "value" => "35" // Bank Account ID from QuickBooks
            ],
            "Line" => [
                [
                    "Amount" => $amount,
                    "DetailType" => "DepositLineDetail",
                    "DepositLineDetail" => [
                        "AccountRef" => [
                            "value" =>"87",
                             "name" =>"Unapplied Cash Payment Income"
                        ]
//                        "Entity" => [
////                            "Type" => "Customer",
//                            "EntityRef" => [
//                                "value" => $customerId,
//                                "name" => "abc123802"
//                            ]
//                        ]
                    ]
                ]
            ]
        ];


        $depositObj = Deposit::create($deposit);
        $result = $dataService->Add($depositObj);

        return $result;
    }

    public static function  transferRewardPoints($customerId, $amount)
    {
        $dataService = self::getDataService();

        $creditMemo = [
            "CustomerRef" => [
                "value" => $customerId,
            ],
            "Line" => [
                [
                    "DetailType" => "SalesItemLineDetail",
                    "Amount" => $amount,
//                    "SalesItemLineDetail" => [
//                        "ItemRef" => [
//                            "value" => "3",
//                            "name" => "Reward Points"
//                        ]
//                    ]
                ]
            ]
        ];

        $creditMemoObj = CreditMemo::create($creditMemo);
        $result = $dataService->Add($creditMemoObj);

        return $result;
    }

}
