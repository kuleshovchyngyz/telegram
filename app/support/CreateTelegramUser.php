<?php

namespace App\support;

use App\Jobs\SendTelegramJob;
use App\Models\Company;
use App\Models\Tuser;

class CreateTelegramUser
{
    protected $text;
    protected $load;
    protected $user_id;
    protected $last_name;
    protected $first_name;
    protected $user_name;
    protected $fromLinkButton;
    protected $continue;
    protected $replyText;
    public function __construct($request)
    {
        if ($request->isJson()) {
            $this->load = $request->all();
            $this->user_id = $this->load['message']['chat']['id'] ?? 0;
            $this->first_name = $this->load['message']['chat']['first_name'] ?? '';
            $this->last_name = $this->load['message']['chat']['last_name'] ?? '';
            $this->user_name =  $this->load['message']['chat']['username'] ?? '';
            $this->continue = true;
            $this->register();
        }
    }
    public function register(){
        $this->determineText()
            ->dealWithUnfollowedUser()
        ;
    }
    public function dealWithUnfollowedUser(){
        if(!$this->continue) return $this;
        $old_member_status = "member";
        $new_member_status = "member";
        $request_arr = json_decode($this->load->getContent(), true);
        if(isset($request_arr['my_chat_member']) && !empty($request_arr['my_chat_member'])) {
            $new_member_status = (isset($request_arr['my_chat_member']['new_chat_member'])) ? $request_arr['my_chat_member']['new_chat_member']['status'] : '';
            $old_member_status = (isset($request_arr['my_chat_member']['old_chat_member'])) ? $request_arr['my_chat_member']['old_chat_member']['status'] : '';
        }
        if($new_member_status != $old_member_status) {
            $id = $request_arr['my_chat_member']['chat']['id'];

            if($new_member_status=="kicked" ){

                Tuser::where('t_id' , $id )
                    ->update([
                        'active' =>  0

                    ]);
            }else if($new_member_status=="member"){
                Tuser::where('t_id',$id)
                    ->update([
                        'active' =>  1
                    ]);
            }
            $this->continue = false;
        }
        return $this;
    }
    public function determineText(){
        $this->text  = $this->load['message']['text'] ?? '';
        $this->text = trim($this->text);
        if($this->text == ''){
            $this->continue = false;
        }
        $this->fromLinkButton = isset($this->load['message']['entities']) ? true : false;
        if($this->fromLinkButton && $this->text!=='/start'){
            $this->text = trim(str_replace('\/start ','',$this->text));
        }
        return $this;
    }
    public function emptyString(){
        if($this->user_name ==""){
            $this->replyText = 'Пожалуйста, отправьте код организации';
        }
        $this->replyText = '<strong>'.strval($this->user_name).'</strong>,'.' пожалуйста, отправьте код организации';

//        SendTelegramJob::dispatch([
//            'chat_id' => $this->user_id,
//            'text' => $reply_text,
//            'parse_mode'=>'HTML'
//        ]);
    }
    public function test(){
        \Storage::append('responses.txt', time() );
        \Storage::append('responses.txt', json_encode($request->all(),JSON_UNESCAPED_UNICODE));

        // $reply_text = '<strong>'.strval($user_name).'</strong>,'.' вы были успешно зарегистрированы';
        // SendTelegramJob::dispatch([
        // 'chat_id' => $user_id,
        // 'text' => $reply_text,
        // 'parse_mode'=>'HTML'
        // ]);

    $update = $request->all();
    $user_id = isset($update['message']['chat']['id']) ? $update['message']['chat']['id'] : 0;
    $user_id = intval($user_id);
    $request_arr = json_decode($request->getContent(), true);

    //if(!$user_id) return;

    $first_name = isset($update['message']['chat']['first_name']) ? $update['message']['chat']['first_name'] : '';
    $last_name = isset($update['message']['chat']['last_name']) ? $update['message']['chat']['last_name'] : '';
    $user_name =  isset($update['message']['chat']['username']) ? $update['message']['chat']['username'] : '';
    $text  = isset($update['message']['text']) ? $update['message']['text'] : '';
    $button = isset($update['message']['entities']) ? true : false;


    if($button){
        SendTelegramJob::dispatch([
            'chat_id' => '555264497',
            'text' => $text,
            'parse_mode'=>'HTML'
        ]);
    }
    $text = trim($text);
    $old_member_status = "member";
    $new_member_status = "member";


    $request_arr = json_decode($request->getContent(), true);

    \Storage::append('responses1.txt','===');
    if(isset($request_arr['my_chat_member']) && !empty($request_arr['my_chat_member'])) {
        $new_member_status = (isset($request_arr['my_chat_member']['new_chat_member'])) ? $request_arr['my_chat_member']['new_chat_member']['status'] : '';

        $old_member_status = (isset($request_arr['my_chat_member']['old_chat_member'])) ? $request_arr['my_chat_member']['old_chat_member']['status'] : '';


    }
    \Storage::append('responses1.txt', $new_member_status);
    \Storage::append('responses1.txt', $old_member_status);

    if($new_member_status != $old_member_status) {
        $id = $request_arr['my_chat_member']['chat']['id'];

        if($new_member_status=="kicked" ){

            Tuser::where('t_id' , $id )
                ->update([
                    'active' =>  0

                ]);
        }else if($new_member_status=="member"){
            Tuser::where('t_id',$id)
                ->update([
                    'active' =>  1

                ]);
        }
        return;

    }


    if($text == '') return;

    switch($text) {

        case '/start':
            if($user_name ==""){
                $text = 'Пожалуйста, отправьте код организации';
            }
            $reply_text = '<strong>'.strval($user_name).'</strong>,'.' пожалуйста, отправьте код организации';
            SendTelegramJob::dispatch([
                'chat_id' => $user_id,
                'text' => $reply_text,
                'parse_mode'=>'HTML'
            ]);
            return;

            break;
        default:

            //ищем команию по коду
            $company = Company::where('usercode',$text)->first();

            if(!$company) {
                if($user_name ==""){
                    $reply_text = 'Укажите правилный код организации!';
                }

                $reply_text = '<strong>'.strval($user_name).'</strong>,	'.'укажите правилный код организации!';
                SendTelegramJob::dispatch([
                    'chat_id' => $user_id,
                    'text' => $reply_text,
                    'parse_mode'=>'HTML'
                ]);
                return;
            }
            $company_id = $company["id"];

            $user = Tuser::where('t_id',$user_id)
                ->where('company_id',$company_id)
                ->count();
            if($user) {
                //update user name last name first name
                Tuser::where('t_id',$user_id)
                    ->where('company_id',$company_id)
                    ->update([
                        't_id' => $user_id,
                        'first_name' => $first_name ,
                        'last_name' => $last_name ,
                        'username' =>  $user_name,
                        'company_id' => $company_id

                    ]);


                if($user_name ==""){
                    $reply_text = 'Вы уже подписались на уведомления!';
                }
                $reply_text = '<strong>'.strval($user_name).'</strong>,'.' вы уже подписались на уведомления!';
                SendTelegramJob::dispatch([
                    'chat_id' => $user_id,
                    'text' => $reply_text,
                    'parse_mode'=>'HTML'
                ]);
                return;

            }



            Tuser::create([
                't_id' => $user_id,
                'first_name' => $first_name ,
                'last_name' => $last_name ,
                'username' =>  $user_name,
                'company_id' => $company_id

            ]);

            if($user_name ==""){
                $reply_text = 'Вы были успешно зарегистрированы';
            }
            $reply_text = '<strong>'.strval($user_name).'</strong>,'.' вы были успешно зарегистрированы';
            SendTelegramJob::dispatch([
                'chat_id' => $user_id,
                'text' => $reply_text,
                'parse_mode'=>'HTML'
            ]);

            break;
    }




}
}
