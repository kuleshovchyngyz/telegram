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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/getmessages', [App\Http\Controllers\MessageController::class, 'getmessages'])->middleware('throttle:10,1')->name('api');

Route::post('/webhook', [App\Http\Controllers\TuserController::class, 'webhook']);
Route::post('/webhook-link', [App\Http\Controllers\TuserController::class, 'webhookLink']);
//Route::post('/getmessages', function (Request $request){
//    if ($request->isJson()) {
//        echo response()->json($request->json()->all());
//        //echo $request->all()["name"];
//    }
//
//});
