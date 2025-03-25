<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use QuickBooksOnline\API\DataService\DataService;

class QuickBooksServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
//        return DataService::Configure([
////            'auth_mode' => 'oauth2',
////            'ClientID' => config('quickbooks.client_id'),
////            'ClientSecret' => config('quickbooks.client_secret'),
////            'accessTokenKey' => config('quickbooks.access_token'),
////            'refreshTokenKey' => config('quickbooks.refresh_token'),
////            'QBORealmID' => config('quickbooks.realm_id'),
////            'baseUrl' => config('quickbooks.env') === 'sandbox' ? 'Development' : 'Production'
//            'auth_mode' => 'oauth2',
//            'ClientID' =>  config('quickbooks.client_id'),
//            'ClientSecret' =>  config('quickbooks.client_secret'),
//            'RedirectURI' => config('quickbooks.redirect_uri'),
//            'QBORealmID' => env('quickbooks.realm_id'),
//            'scope' => 'com.intuit.quickbooks.accounting, com.intuit.quickbooks.payment, openid, profile, email, phone, address',
//            'baseUrl' => env('quickbooks.env')
//        ]);

        $this->app->bind(DataService::class, function ($app) {
            $dataService =  DataService::Configure([
                'auth_mode' => 'oauth2',
                'ClientID' =>  config('quickbooks.client_id'),
                'ClientSecret' =>  config('quickbooks.client_secret'),
                'RedirectURI' => config('quickbooks.redirect_uri'),
                'QBORealmID' => env('quickbooks.realm_id'),
                'scope' => 'com.intuit.quickbooks.accounting',
                'baseUrl' => env('quickbooks.env')
            ]);


            $dataService->throwExceptionOnError(true);

            return $dataService;
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
