<?php
use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/register-user', [ApiController::class, 'RegisterUser']);
Route::post('/login-user', [ApiController::class, 'LoginUser']);
Route::post('/qb/store-user', [\App\Http\Controllers\QuickBookController::class, 'store']);
Route::post('/qb/transfer-payment', [\App\Http\Controllers\QuickBookController::class, 'transferPayment']);
