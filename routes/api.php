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
//get designs
Route::get('designs','App\Http\Controllers\Designs\DesignController@index');
//get user
Route::get('users','App\Http\Controllers\User\UserController@index');
//find design by id
Route::get('designs/{id}','App\Http\Controllers\Designs\DesignController@findDesign');
// team slug
Route::get('teams/slug/{slug}','App\Http\Controllers\Teams\TeamsController@findBySlug');

Route::group(['middleware' => ['auth:api']], function () {
    Route::post('logout', 'App\Http\Controllers\Auth\LoginController@logout');

    Route::put('settings/profile','App\Http\Controllers\User\SettingController@updateProfile');
    Route::put('settings/password','App\Http\Controllers\User\SettingController@updatePassword');

    //upload designs
    Route::post('designs','App\Http\Controllers\Designs\UploadController@upload');
    Route::put('designs/{id}','App\Http\Controllers\Designs\DesignController@update');
    Route::delete('designs/{id}','App\Http\Controllers\Designs\DesignController@destroy');
    //like and unlike
    Route::post('designs/{id}/like','App\Http\Controllers\Designs\DesignController@like');
    Route::get('designs/{id}/liked','App\Http\Controllers\Designs\DesignController@checkIfUserHasLiked');

    //create comment
    Route::post('/designs/{designId}/comments','App\Http\Controllers\Designs\CommentController@store');
    Route::put('/comments/{id}','App\Http\Controllers\Designs\CommentController@update');
    Route::delete('/comments/{id}','App\Http\Controllers\Designs\CommentController@destroy');
    //teams
    Route::post('teams','App\Http\Controllers\Teams\TeamsController@store');
    Route::get('teams/{id}','App\Http\Controllers\Teams\TeamsController@findById');
    Route::get('teams','App\Http\Controllers\Teams\TeamsController@index');
    Route::get('users/teams','App\Http\Controllers\Teams\TeamsController@fetchUserTeams');
    Route::put('teams/{id}','App\Http\Controllers\Teams\TeamsController@update');
    Route::delete('teams/{id}','App\Http\Controllers\Teams\TeamsController@destroy');
    Route::delete('teams/{id}/user/{user_id}','App\Http\Controllers\Teams\TeamsController@removeFromTeam');
    //invitations
    Route::post('invitation/{teamId}','App\Http\Controllers\Teams\InvitationsController@invite');
    Route::post('invitation/{id}/resend','App\Http\Controllers\Teams\InvitationsController@resend');
    Route::post('invitation/{id}/respond','App\Http\Controllers\Teams\InvitationsController@respond');
    Route::delete('invitation/{id}','App\Http\Controllers\Teams\InvitationsController@destroy');

});


Route::group(['middleware' => ['guest:api']], function () {
    Route::post('register', 'App\Http\Controllers\Auth\RegisterController@register');
    Route::post('verification/verify', 'App\Http\Controllers\Auth\VerificationController@verify')->name('verification.verify');
    Route::post('verification/resend', 'App\Http\Controllers\Auth\VerificationController@resend');
    Route::post('login', 'App\Http\Controllers\Auth\LoginController@login');
    Route::post('password/email', 'App\Http\Controllers\Auth\ForgotPasswordController@sendResetLinkEmail');
    Route::post('password/reset', 'App\Http\Controllers\Auth\ResetPasswordController@reset');

});
