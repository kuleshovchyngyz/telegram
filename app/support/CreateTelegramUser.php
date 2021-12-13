<?php

namespace App\support;

use App\Jobs\SendTelegramJob;
use App\Models\Company;
use App\Models\Tuser;

class CreateTelegramUser
{
    protected $text;
    protected $request;
    protected $load;
    protected $user_id;
    protected $last_name;
    protected $first_name;
    protected $user_name;
    protected $fromLinkButton;
    protected $continue;
    protected $replyText;
    protected $companyId;
    public function __construct($request)
    {
        if ($request->isJson()) {
            $this->request = $request;
            $this->load = $request->all();
            $this->user_id = $this->load['message']['chat']['id'] ?? 0;
            $this->first_name = $this->load['message']['chat']['first_name'] ?? '';
            $this->last_name = $this->load['message']['chat']['last_name'] ?? '';
            $this->user_name =  $this->load['message']['chat']['username'] ?? '';
            $this->continue = true;
            $this->replyText = '';
            $this->register();
        }
    }
    public function register(){
        $this->determineText()
            ->dealWithUnfollowedUser()
            ->emptyString()
            ->checkForValidCompany()
            ->checkForDuplicateOrUpdateUsers()
            ->createTelegramUser()
            ->reply()
        ;
    }
    public function determineText(){
        if(!$this->continue) return $this;
        $this->text  = $this->load['message']['text'] ?? '';
        $this->text = trim($this->text);
        if($this->text == ''){
            $this->continue = false;
        }
        $this->fromLinkButton = isset($this->load['message']['entities']) ? true : false;
        if($this->fromLinkButton && $this->text!=='/start'){
            $this->text = trim(str_replace('/start ','',$this->text));
        }
        return $this;
    }
    public function dealWithUnfollowedUser(){
        if(!$this->continue) return $this;
        $old_member_status = "member";
        $new_member_status = "member";
        $request_arr = json_decode($this->request->getContent(), true);
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
    public function emptyString(){
        if(!$this->continue) return $this;
        if($this->text ==='/start'){
            if($this->user_name ==""){
                $this->replyText = 'Пожалуйста, отправьте код организации';
            }
            $this->replyText = '<strong>'.strval($this->user_name).'</strong>,'.' пожалуйста, отправьте код организации';
            $this->continue = false;
        }
        return $this;
    }
    public function checkForValidCompany(){
        if(!$this->continue) return $this;
        $company = Company::where('usercode',$this->text)->first();
        if(!$company) {
            if($this->user_name ==""){
                $this->replyText = 'Укажите правилный код организации!';
            }
            $this->replyText = '<strong>'.strval($this->user_name).'</strong>,	'.'укажите правилный код организации!';
            $this->continue = false;
        }else{
            $this->companyId = $company->id;
        }
        return $this;
    }
    public function checkForDuplicateOrUpdateUsers()
    {
        if (!$this->continue) return $this;
        $user = Tuser::where('t_id', $this->user_id)
            ->where('company_id', $this->companyId)
            ->count();
        if ($user) {
            //update user name last name first name
            Tuser::where('t_id', $this->user_id)
                ->where('company_id', $this->companyId)
                ->update([
                    't_id' => $this->user_id,
                    'first_name' => $this->first_name,
                    'last_name' => $this->last_name,
                    'username' => $this->user_name,
                    'company_id' => $this->companyId
                ]);
            if ($this->user_name == "") {
                $this->replyText = 'Вы уже подписались на уведомления!';
            }
            $this->replyText = '<strong>' . strval($this->user_name) . '</strong>,' . ' вы уже подписались на уведомления!';
            $this->continue = false;
        }
        return this;
    }
    public function createTelegramUser(){
        if (!$this->continue) return $this;
        Tuser::create([
            't_id' => $this->user_id,
            'first_name' => $this->first_name ,
            'last_name' => $this->last_name ,
            'username' =>  $this->user_name,
            'company_id' => $this->company_id
        ]);

        if($this->user_name ==""){
            $this->replyText = 'Вы были успешно зарегистрированы';
        }
        $this->replyText = '<strong>'.strval($this->user_name).'</strong>,'.' вы были успешно зарегистрированы';
        $this->continue = false;
        return $this;
    }
    public function reply(){
        if($this->replyText !=''){
            SendTelegramJob::dispatch([
                'chat_id' => $this->user_id,
                'text' => $this->replyText,
                'parse_mode'=>'HTML'
            ]);
        }
        return $this;
    }





}
