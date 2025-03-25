<?php

return [
    'client_id' => env('QB_CLIENT_ID'),
    'client_secret' => env('QB_CLIENT_SECRET'),
    'access_token' => env('QB_ACCESS_TOKEN'),
    'refresh_token' => env('QB_REFRESH_TOKEN'),
    'redirect_uri' => env('QB_REDIRECT_URI'),
    'realm_id' => env('QB_REALM_ID'),
    'env' => env('QB_ENV', 'development'),
];
