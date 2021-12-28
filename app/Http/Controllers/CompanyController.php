<?php

namespace App\Http\Controllers;
use App\Models\Company;
use App\Models\Message;
use App\Models\TelegramBot;
use App\Models\Tuser;
use Illuminate\Http\Request;
use Telegram\Bot\Laravel\Facades\Telegram;
use App\Jobs\SendTelegramJob;
use Telegram\Bot\Api;
class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }
    public function messages()
    {
        $companies = Company::select('id','name')->get();
        $t_users = Tuser::where('company_id',session('selected_company_id'))->get(['t_id','first_name','username']);
        $messages = Message::where('company_id',session('selected_company_id'))->sortByDesc('created_at')->paginate(15);
        return view('home', [
            'companies'=> $companies,
            'message'=>'true',
            'users'=>$t_users,
            'messages'=>$messages

        ]);
    }
    public function sendMessage(Request $request, Company $company)
    {
        SendTelegramJob::dispatch([
            'chat_id' => $request['t_id'],
            'text' => $request['message'],
            'company' => $company,
			'parse_mode'=>'HTML'

        ])->delay(now()->addSeconds(1));
        return redirect()->route('messages')->with('success_message', 'сообщение будет отправлено');
    }
    public function sendMessages(Request $request, Company $company)
    {
        $t_users = Tuser::where('company_id',$company->id)->where('active',true)->get(['t_id','first_name','username']);

        foreach ($t_users as $t_user) {
            SendTelegramJob::dispatch([
                    'chat_id' =>$t_user->t_id,
                'text' => urlencode($request['message']),
				'parse_mode'=>'HTML'
            ])->delay(now()->addSeconds(1));
        }
        return redirect()->route('messages')->with('success_message', 'сообщение будет отправлено');

    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function createBot(Request $request)
    {
        TelegramBot::create(['name'=>$request->name,
            'username'=>$request->username,
            'token'=>$request->token
        ]);


        $telegram = new Api($request->token);
        $response = $telegram->setWebhook(['url' => route('telegramhook')]);


        return redirect()->back()->with('success_message', 'Бот создан');
    }
    public function editBot(Request $request, TelegramBot $bot)
    {

        if($bot->token != $request->token){
            $telegram = new Api($bot->token);
            //$response = $telegram->removeWebhook();
            $telegram = new Api($request->token);
            $response = $telegram->setWebhook(['url' => route('telegramhook')]);
        }
        $bot->update($request->all());



        return redirect()->back()->with('success_message', 'Бот обновлен');
    }
    public function create(Request $request)
    {
        if (session('selected_bot_id') ){
            $company = Company::create([
                'name' =>$request['name'],
                'companycode' =>'co'.$this->uniqidReal(13),
                'usercode' => $this->uniqidReal(6),
                'telegram_bot_id'=> session('selected_bot_id')
            ]);
            $company->save();
            return redirect()->route('home')->with('success_message', 'Project was created');
        }else{
            return redirect()->route('home')->with('success_message', 'Выберите бота');
        }

    }
	public function uniqidReal($lenght) {

		if (function_exists("random_bytes")) {
			$bytes = random_bytes(ceil($lenght / 2));
		} elseif (function_exists("openssl_random_pseudo_bytes")) {
			$bytes = openssl_random_pseudo_bytes(ceil($lenght / 2));
		} else {
			throw new Exception("no cryptographically secure random function available");
		}
		return substr(bin2hex($bytes), 0, $lenght);

	}
    public function select(Company $company)
    {
		//dd($company);
        if( !$company ) {
            return redirect()->route('home')->with('error_message', 'Something went wrong');
        }
        session(['selected_company_id'=> $company->id ]);
        session(['selected_company_name'=> $company->name ]);
        session(['selected_company_botname'=> $company->companycode ]);
		session(['selected_company_bot_usercode'=> $company->usercode ]);
        //dd($company->name);
        return redirect()->route('home', ['selected_company' => $company]);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, Company $company)
    {
        $result = $company->update($request->all());
        return ( $result )
            ? redirect()->route('home' )->with('success_message','Успешно обновлено' )
            : redirect()->route('home')->with('error_message', 'Error');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Company $company)
    {

        $userids = Tuser::
        where('company_id', $company->id)
            ->pluck('id')->toArray();
        Tuser::destroy($userids);
        $result = Company::destroy($company->id);
        session()->forget(['selected_company_id', 'selected_company_name']);
        return ( $result )
            ? redirect()->route('home')->with('success_message', 'Успешно удалено' )
            : redirect()->route('home')->with('error_message', 'Error');
    }
    public function deleteBot(TelegramBot $bot)
    {
        $companies=$bot->companies;
        foreach ($companies as $company){
            $userids = Tuser::
            where('company_id', $company->id)
                ->pluck('id')->toArray();
            Tuser::destroy($userids);
            Company::destroy($company->id);
            session()->forget(['selected_company_id', 'selected_company_name']);
        }
        $result = TelegramBot::destroy($bot->id);
        session()->forget(['selected_bot_id', 'selected_bot_name']);
        return ( $result )
            ? redirect()->route('home')->with('success_message', 'Успешно удалено' )
            : redirect()->route('home')->with('error_message', 'Error');
    }
}
