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

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});

// Route group for authenticated user only

Route::group(['middleware'=>['auth:api']],function (){

});
// Route group for guest user only
Route::group(['middleware'=>['guest:api']],function (){
//         Route::post('register','App\Http\Controllers\Auth\RegisterController');

});

