<?php

use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


// Route group for guest user only
// Routes for guests only
//Route::prefix('v1')->name('api.v1.')->group(function () {
//    Route::group([
//        'middleware' => 'api',
//        'prefix' => 'auth'
//    ], function ($router) {
//        Route::post('/login', [AuthController::class, 'login']);
//        Route::post('/verification/verify', [VerificationController::class, 'verify']);
//        Route::post('/verification/resend', [VerificationController::class, 'resend']);
//        Route::post('/register', [AuthController::class, 'register']);
//        Route::post('/logout', [AuthController::class, 'logout']);
//        Route::post('/refresh', [AuthController::class, 'refresh']);
//        Route::get('/user-profile', [AuthController::class, 'userProfile']);
//    });
//});
Route::get('me', 'App\Http\Controllers\User\MeController@getMe');


Route::group(['middleware' => ['auth:api']], function () {
    Route::post('logout', 'App\Http\Controllers\Auth\LoginController@logout');

    Route::put('settings/profile','App\Http\Controllers\User\SettingController@updateProfile');
    Route::put('settings/password','App\Http\Controllers\User\SettingController@updatePassword');
});


Route::group(['middleware' => ['guest:api']], function () {
    Route::post('register', 'App\Http\Controllers\Auth\RegisterController@register');
    Route::post('verification/verify', 'App\Http\Controllers\Auth\VerificationController@verify')->name('verification.verify');
    Route::post('verification/resend', 'App\Http\Controllers\Auth\VerificationController@resend');
    Route::post('login', 'App\Http\Controllers\Auth\LoginController@login');
    Route::post('password/email', 'App\Http\Controllers\Auth\ForgotPasswordController@sendResetLinkEmail');
    Route::post('password/reset', 'App\Http\Controllers\Auth\ResetPasswordController@reset');

});
