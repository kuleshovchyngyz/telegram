<?php

use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/bot/getupdates', function() {
    $updates = Telegram::getUpdates();
    return (json_encode($updates));
});
Route::get('/bot/deletewebhook', function() {
    $updates = Telegram::deleteWebhook();
    //return (json_encode($updates));
});
Route::get('/api/sample', function() {
    $json = \Storage::disk('local')->get('sample.json');
    $json = json_decode($json, true);
    return $json;
})->name('apilink');
Route::get('bot/sendmessage', function() {
    $sent = Telegram::sendMessage([
        'chat_id' => '5552644',
        'text' => 'Hello world!'
    ]);
    dump($sent);
    return;
});

Auth::routes();
Route::group(['middleware' => 'auth'], function () {
//https://api.telegram.org/bot1746041949:AAEvdrG0yeR6W2Wn1FmKu9zB7qCuILhK6Jk/setWebhook?url=http://telegram/webhook

Route::get('/tuserstatus/{tuser}', [App\Http\Controllers\TuserController::class, 'changestatus'])->name('user.changestatus');
Route::get('/tusercomment/{tuser}', [App\Http\Controllers\TuserController::class, 'commentuser'])->name('user.comment');
Route::get('/deleteuser/{tuser}', [App\Http\Controllers\TuserController::class, 'destroy'])->name('user.delete');
Route::get('/tusers/{company}', [App\Http\Controllers\TuserController::class, 'show'])->name('tusers');
Route::get('/messages', [App\Http\Controllers\CompanyController::class, 'messages'])->name('messages');
Route::post('/sendmessage/{company}', [App\Http\Controllers\CompanyController::class, 'sendMessage'])->name('company.sendmessage');
Route::post('/sendMessages/{company}', [App\Http\Controllers\CompanyController::class, 'sendMessages'])->name('company.sendMessages');
Route::get('/documentation', [App\Http\Controllers\HomeController::class, 'documentation'])->name('documentation');
Route::get('/sendmessage', [App\Http\Controllers\HomeController::class, 'sendMessage']);
Route::get('/allmessages', [App\Http\Controllers\MessageController::class, 'index']);
Route::get('/testing', [App\Http\Controllers\HomeController::class, 'test']);
Route::get('/bot/{id}', [App\Http\Controllers\HomeController::class, 'companySelector']);

Route::get('/send', [App\Http\ControllersControllers\MessageController::class, 'sendMessageToUsers']);
Route::get('/test', [App\Http\Controllers\MessageController::class, 'test'])->name('test');
    Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::post('/createcompany', [App\Http\Controllers\CompanyController::class, 'create'])->name('company.create');
    Route::post('/createbot', [App\Http\Controllers\CompanyController::class, 'createBot'])->name('bot.create');
    Route::get('select/{company}', [App\Http\Controllers\CompanyController::class, 'select'])->name('company.select');
    Route::post('edit/{company}', [App\Http\Controllers\CompanyController::class, 'edit'])->name('company.edit');
    Route::get('destroy/{company}', [App\Http\Controllers\CompanyController::class, 'destroy'])->name('company.destroy');

});



