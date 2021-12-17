<?php

use Illuminate\Support\Facades\Route;

use Telegram\Bot\Api;
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

Route::get('/register', function () {
    return redirect()->route('login');
});
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
    $telegram = new Api('1787584844:AAGLyrZ-1L8Ssg4Ys_sLIz8umQ5RrFJVI58');
    $sent = $telegram->sendMessage([
        'chat_id' => '555264497',
        'text' => 'Hello world!'
    ]);

    try {

        $telegram = new Api('1709979892:AAEhRif1lR7vNZzd0nOJ8MEx7_jtoU8Hs3M');
//        $response = $telegram->getMe();
        $response = $telegram->setWebhook(['url' => 'https://t.kuleshov.studio/api/webhook']);
        dump($response);
    } catch (Exception $e) {
        if($e->getMessage()=='Unauthorized'){
            dump('invalid token');
        }
    }

//    $response = $telegram->setWebhook(['url' => 'https://t.kuleshov.studio/api/webhook']);

//    $telegram->sendMessage([
//        'chat_id' => '555264497',
//        'text' => 'Hellzxczxo world!'
//    ]);

    return;
});


Auth::routes([

    'register' => false, // Register Routes...


    'reset' => false, // Reset Password Routes...

    'verify' => false, // Email Verification Routes...

]);
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
    Route::get('/deletebot/{bot}', [App\Http\Controllers\CompanyController::class, 'deleteBot'])->name('bot.destroy');
    Route::post('/editbot/{bot}', [App\Http\Controllers\CompanyController::class, 'editBot'])->name('bot.edit');
    Route::get('select/{company}', [App\Http\Controllers\CompanyController::class, 'select'])->name('company.select');
    Route::post('edit/{company}', [App\Http\Controllers\CompanyController::class, 'edit'])->name('company.edit');
    Route::get('destroy/{company}', [App\Http\Controllers\CompanyController::class, 'destroy'])->name('company.destroy');

});



