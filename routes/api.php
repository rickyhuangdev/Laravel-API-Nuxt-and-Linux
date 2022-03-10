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

Route::group(['middleware'=>['auth:api']],function (){

});


Route::group(['middleware'=>['guest:api']],function (){
Route::post('register','App\Http\Controllers\Auth\RegisterController@register');
Route::post('verification/verify','App\Http\Controllers\Auth\VerificationController@verify')->name('verification.verify');
Route::post('verification/resend','App\Http\Controllers\Auth\VerificationController@resend');
Route::post('login','App\Http\Controllers\Auth\LoginController@login');
});
