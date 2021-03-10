<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::post('login', [\App\Http\Controllers\Api\AuthController::class, 'login'])
->name('auth.login');
Route::post('register', [\App\Http\Controllers\Api\AuthController::class, 'register'])
->name('auth.register');

Route::get('/email/verify/{id}/{hash}', function (\Illuminate\Foundation\Auth\EmailVerificationRequest $request) {
    $request->fulfill();

    return redirect(config('kal.frontend_url') . '/#/email-verified');
})->middleware(['auth:api'])->name('verification.verify');

Route::group(['middleware' => 'auth:api', 'namespace' => 'Api'], function(){
    Route::get('token', [\App\Http\Controllers\Api\AuthController::class, 'token'])
        ->name('auth.token');
    Route::get('logout', [\App\Http\Controllers\Api\AuthController::class, 'logout'])
        ->name('auth.logout');
});
