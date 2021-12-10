<?php
namespace App\Http\Controllers;
use App\Models\Company;
use App\Models\Message;
use App\Models\Tuser;
use Illuminate\Http\Request;
use Telegram\Bot\Laravel\Facades\Telegram;
use App\Jobs\SendTelegramJob;
class SendMessage {
    public function __construct() {
        return "construct function was initialized.";
    }

    public function create() {
        // create notification
        // send email
        // return output
    }
    public function sendMessages()
    {
        $t_users = Tuser::where('company_id',$company_id)->where('active',true)->get(['t_id','first_name','username']);

        foreach ($t_users as $t_user) {
            foreach ($messages as $message) {
                Telegram::sendMessage([
                    'chat_id' =>$t_user->t_id,
                    'text' => $request['message'],
                    //'text' => '<strong>234234</strong> rty',
					'parse_mode' => 'HTML'
                ]);
            }

        }
        return redirect()->route('messages')->with('success_message', 'сообщение будет отправлено');

    }
}
