<?php

namespace App\Http\Controllers;
use App\Models\Company;
use App\Models\TelegramBot;
use App\Models\Tuser;
use App\Jobs\SendTelegramJob;
use Illuminate\Http\Request;

use Telegram\Bot\Laravel\Facades\Telegram;
class HomeController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void

     */
	public function test()
    {
		//$user = Tuser::where('t_id','555264497')->get();

		$user = Tuser::where('t_id',555264497)
					->update(['active'=>1]);

        //$company = Company::where('usercode',"eb9c7e")->first();
		if(!$user){
			return "no";
		}
        return $user;
    }
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function companySelector($id)
    {
        $bot = TelegramBot::find($id);
        session(['selected_bot_id'=> $bot->id ]);
        session(['selected_bot_name'=> $bot->name ]);
        session()->forget(['selected_company_id', 'selected_company_name']);
        return Company::where('telegram_bot_id',$id)->get();
    }
    public function sendMessage()
    {
        /*$tele = Telegram::sendMessage([
            'chat_id' => '555264497',
            'text' => 'got message from you!'
        ]);*/




//        var_dump( env('QUEUE_CONNECTION') );

    }
    public function index()
    {
        $companies = [];
        if (session('selected_bot_id') ){
            $companies = Company::where('telegram_bot_id',session('selected_bot_id'))->select('id','name')->get();
        }
        return view('home', [
            'companies'=> $companies,
            'bots'=>TelegramBot::all()
        ]);
    }
    public function documentation()
    {
        $companies = Company::select('id','name')->get();
        return view('documentation', [
            'companies'=> $companies,
        ]);
    }
}
