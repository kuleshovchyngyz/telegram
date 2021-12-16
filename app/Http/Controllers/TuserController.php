<?php

namespace App\Http\Controllers;

use App\Models\Tuser;
use App\support\CreateTelegramUser;
use Illuminate\Http\Request;
use App\Models\Company;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Telegram\Bot\Laravel\Facades\Telegram;
use App\Jobs\SendTelegramJob;
class TuserController extends Controller
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

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
    public function toPartner($webhook_url, $data) {
        // $webhook_url = "https://b24-4goccw.bitrix24.ru/rest/1/gfb5rzf8p5iwam80/";//test


        $res = Http::timeout(5)->post($webhook_url,$data);
//        return json_decode($res->body(), 1);

    }
    public function webhookLink(Request $request)
    {
        if($request->isJson()){
            \Storage::append('link.txt', time());
            \Storage::append('link.txt', json_encode($request->all(), JSON_UNESCAPED_UNICODE));
            $webhook = $request->all()["webhook"];
            $company = $request->all()["companycode"];
            Company::where('companycode',$company)->update(['webhook'=>$webhook]);
            $c = Company::where('companycode',$company)->first()->telegramBot;
            \Storage::append('link.txt', $webhook);
            \Storage::append('link.txt', $company);
            return response()->json($c->toArray());
        }
        return 'Unknown format';
    }
    public function webhook(Request $request)
    {
                \Storage::append('responses.txt', time() );
        \Storage::append('responses.txt', json_encode($request->all(),JSON_UNESCAPED_UNICODE));
        $user = new CreateTelegramUser($request);
        if($user->fromLinkButton){
            $webhook_url =  $user->getCompany()->webhook ;

                \Storage::append('ssdd.txt',$webhook_url);
                $data = ["telegramUserId" => $user->getUserId(),'userId'=>$user->getRemoteUserId()];
                $res = Http::post($webhook_url,$data);
                //\Storage::append('ssdd.txt', json_encode($res->collect()->toArray()));


        }


//        \Storage::append('responses.txt', time() );
//        \Storage::append('responses.txt', json_encode($request->all(),JSON_UNESCAPED_UNICODE));
//
//		// $reply_text = '<strong>'.strval($user_name).'</strong>,'.' вы были успешно зарегистрированы';
//					// SendTelegramJob::dispatch([
//					// 'chat_id' => $user_id,
//					// 'text' => $reply_text,
//					// 'parse_mode'=>'HTML'
//					// ]);
//
//		$update = $request->all();
//		$user_id = isset($update['message']['chat']['id']) ? $update['message']['chat']['id'] : 0;
//		$user_id = intval($user_id);
//		$request_arr = json_decode($request->getContent(), true);
//
//		//if(!$user_id) return;
//
//		$first_name = isset($update['message']['chat']['first_name']) ? $update['message']['chat']['first_name'] : '';
//		$last_name = isset($update['message']['chat']['last_name']) ? $update['message']['chat']['last_name'] : '';
//		$user_name =  isset($update['message']['chat']['username']) ? $update['message']['chat']['username'] : '';
//		$text  = isset($update['message']['text']) ? $update['message']['text'] : '';
//		$button = isset($update['message']['entities']) ? true : false;
//
//
//		if($button){
//            SendTelegramJob::dispatch([
//                'chat_id' => '555264497',
//                'text' => $text.'===',
//                'parse_mode'=>'HTML'
//            ]);
//        }
//		$text = trim($text);
//		$old_member_status = "member";
//		$new_member_status = "member";
//
//
//		$request_arr = json_decode($request->getContent(), true);
//
//		\Storage::append('responses1.txt','===');
//		if(isset($request_arr['my_chat_member']) && !empty($request_arr['my_chat_member'])) {
//			$new_member_status = (isset($request_arr['my_chat_member']['new_chat_member'])) ? $request_arr['my_chat_member']['new_chat_member']['status'] : '';
//
//			$old_member_status = (isset($request_arr['my_chat_member']['old_chat_member'])) ? $request_arr['my_chat_member']['old_chat_member']['status'] : '';
//
//
//		}
//				\Storage::append('responses1.txt', $new_member_status);
//				\Storage::append('responses1.txt', $old_member_status);
//
//		if($new_member_status != $old_member_status) {
//			$id = $request_arr['my_chat_member']['chat']['id'];
//
//			if($new_member_status=="kicked" ){
//
//					Tuser::where('t_id' , $id )
//					->update([
//						'active' =>  0
//
//					]);
//			}else if($new_member_status=="member"){
//									Tuser::where('t_id',$id)
//					->update([
//						'active' =>  1
//
//					]);
//			}
//			return;
//
//		}
//
//
//		if($text == '') return;
//
//		switch($text) {
//
//			case '/start':
//				if($user_name ==""){
//					$text = 'Пожалуйста, отправьте код организации';
//				}
//				$reply_text = '<strong>'.strval($user_name).'</strong>,'.' пожалуйста, отправьте код организации';
//				SendTelegramJob::dispatch([
//				'chat_id' => $user_id,
//				'text' => $reply_text,
//				'parse_mode'=>'HTML'
//				]);
//				return;
//
//			break;
//			default:
//
//				//ищем команию по коду
//				$company = Company::where('usercode',$text)->first();
//
//				if(!$company) {
//					if($user_name ==""){
//						$reply_text = 'Укажите правилный код организации!';
//					}
//
//					$reply_text = '<strong>'.strval($user_name).'</strong>,	'.'укажите правилный код организации!';
//					SendTelegramJob::dispatch([
//					'chat_id' => $user_id,
//					'text' => $reply_text,
//					'parse_mode'=>'HTML'
//					]);
//					return;
//				}
//				$company_id = $company["id"];
//
//				$user = Tuser::where('t_id',$user_id)
//					->where('company_id',$company_id)
//					->count();
//				if($user) {
//					//update user name last name first name
//					 Tuser::where('t_id',$user_id)
//					->where('company_id',$company_id)
//					->update([
//						't_id' => $user_id,
//						'first_name' => $first_name ,
//						'last_name' => $last_name ,
//						'username' =>  $user_name,
//						'company_id' => $company_id
//
//					]);
//
//
//					if($user_name ==""){
//						$reply_text = 'Вы уже подписались на уведомления!';
//					}
//					$reply_text = '<strong>'.strval($user_name).'</strong>,'.' вы уже подписались на уведомления!';
//					SendTelegramJob::dispatch([
//					'chat_id' => $user_id,
//					'text' => $reply_text,
//					'parse_mode'=>'HTML'
//					]);
//					return;
//
//				}
//
//
//
//				Tuser::create([
//					't_id' => $user_id,
//					'first_name' => $first_name ,
//					'last_name' => $last_name ,
//					'username' =>  $user_name,
//					'company_id' => $company_id
//
//				]);
//
//					if($user_name ==""){
//						$reply_text = 'Вы были успешно зарегистрированы';
//					}
//					$reply_text = '<strong>'.strval($user_name).'</strong>,'.' вы были успешно зарегистрированы';
//					SendTelegramJob::dispatch([
//					'chat_id' => $user_id,
//					'text' => $reply_text,
//					'parse_mode'=>'HTML'
//					]);
//
//			break;
//		}
//
//




	}
//        var_dump( env('QUEUE_CONNECTION') );


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function commentuser(Tuser $tuser){

	}
    public function changestatus(Tuser $tuser)
    {
        $status = $tuser->active;
        $tuser->update(["active" => !$status]);
        $t_users = Tuser::where('company_id',session('selected_company_id'))->get(['id','t_id','first_name','last_name','username','active']);
        $companies = Company::select('id','name')->get();
        return view('home', [
            'companies'=> $companies,
            't_users'=> $t_users
        ]);
    }
    public function show($id)
    {
        $t_users = Tuser::where('company_id',$id)->get(['id','t_id','first_name','last_name','username','active']);
        $companies = Company::select('id','name')->get();
        return view('home', [
            'companies'=> $companies,
            't_users'=> $t_users
        ]);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
    public function destroy(Tuser $tuser)
    {
        $result = $tuser->delete();
        if($result){
            $t_users = Tuser::where('company_id',session('selected_company_id'))->get(['id','t_id','first_name','username','active']);
            $companies = Company::select('id','name')->get();
            return view('home', [
                'companies'=> $companies,
                't_users'=> $t_users
            ])->with('success_message', 'Удалено');

        }
    }
}
