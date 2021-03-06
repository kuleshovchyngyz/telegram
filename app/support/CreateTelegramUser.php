<?php

namespace App\support;

use App\Jobs\SendTelegramJob;
use App\Models\Company;
use App\Models\TelegramBot;
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
    public $fromLinkButton;
    protected $continue;
    protected $replyText;
    protected $companyId;
    protected $company;
    protected $column;
    protected $updateId;
    protected $remoteUserId;
    protected $bot;
    public function __construct($request)
    {
        if ($request->isJson()) {
            $this->request = $request;
            $this->load = $request->all();
            $this->user_id = $this->load['message']['chat']['id'] ?? 0;
            $this->first_name = $this->load['message']['chat']['first_name'] ?? '';
            $this->last_name = $this->load['message']['chat']['last_name'] ?? '';
            $this->user_name =  $this->load['message']['chat']['username'] ?? '';
            $this->updateId =   substr($this->load['update_id'],0,4);
            $this->continue = true;
            $t = TelegramBot::where('update_id',$this->updateId);
            if($t->exists()){
                $this->bot = $t->first();
            }else{
                $this->bot = TelegramBot::where('update_id',null)->first();
            }
            $this->replyText = '';
            $this->company = null;
            $this->column = 'usercode';
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

        $this->text  = $this->load['message']['text'] ?? '';
        $this->text = trim($this->text);
        if($this->text == ''){
            $this->continue = false;
        }
        if(!$this->continue) return $this;

        $rightButton = str_contains($this->text,'/start');
        $this->fromLinkButton = $rightButton && (isset($this->load['message']['entities'])&& $this->text!=='/start') ? true : false;


        if($this->fromLinkButton){
            $this->text = trim(str_replace('/start ','',$this->text));
            $this->text =base64_decode($this->text);
            if(preg_match('/\[(.*?)\]/', $this->text, $matches)){
                $user = $matches[0];
                $this->text = trim(str_replace($user,'',$this->text));
                $this->remoteUserId = str_replace(['[',']'],'',$user);
            }
            $this->column = 'companycode';
            $this->company =  Company::where($this->column,$this->text)->first();
        }
        return $this;
        //preg_match('/\[(.*?)\]/', '[http://partner.kuleshov.studio/api/telegram]', $matches);
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
                $this->replyText = '????????????????????, ?????????????????? ?????? ??????????????????????';
            }
            $this->replyText = '<strong>'.strval($this->user_name).'</strong>,'.' ????????????????????, ?????????????????? ?????? ??????????????????????';
            $this->continue = false;
        }
        return $this;
    }
    public function checkForValidCompany(){
        if(!$this->continue) return $this;
        if($this->bot===null){
            $this->replyText = 'PLese add the created bot!';
            $this->continue = false;
            return $this;
        }
        $company = $this->bot->companies->where($this->column,$this->text)->first();

        if(!$company) {
                $this->replyText = '?????????????? ???????????????????? ?????? ??????????????????????!';
                $this->continue = false;
        }
        else
        {
                $this->companyId = $company->id;
                $this->company = Company::find($this->companyId);
                $bot = $this->company->telegramBot;
                $bot->update_id = $this->updateId;
                $bot->save();
        }
        return $this;
    }
    public function checkForDuplicateOrUpdateUsers()
    {
        if (!$this->continue) return $this;
        $user = Tuser::where('t_id', $this->user_id)
            ->where('company_id', $this->companyId);
        if ($user ->count()) {
            $user->update([
                    't_id' => $this->user_id,
                    'first_name' => $this->first_name,
                    'last_name' => $this->last_name,
                    'username' => $this->user_name,
                    'company_id' => $this->companyId
                ]);
            if ($this->user_name == "") {
                $this->replyText = '???? ?????? ?????????????????????? ???? ??????????????????????!';
            }
            $this->replyText = '<strong>' . strval($this->user_name) . '</strong>,' . ' ???? ?????? ?????????????????????? ???? ??????????????????????!';
            $this->continue = false;
        }
        return $this;
    }
    public function createTelegramUser(){
        if (!$this->continue) return $this;
        Tuser::create([
            't_id' => $this->user_id,
            'first_name' => $this->first_name ,
            'last_name' => $this->last_name ,
            'username' =>  $this->user_name,
            'company_id' => $this->companyId
        ]);

        if($this->user_name ==""){
            $this->replyText = '???? ???????? ?????????????? ????????????????????????????????';
        }
        $this->replyText = '<strong>'.strval($this->user_name).'</strong>,'.' ???? ???????? ?????????????? ????????????????????????????????';
        $this->continue = false;
        return $this;
    }
    public function reply(){
        \Storage::append('updateId.txt', $this->updateId );
        if($this->replyText !=''){
                SendTelegramJob::dispatch([
                    'chat_id' => $this->user_id,
                    'text' => $this->replyText,
                    'bot' => $this->bot->token,
                    'parse_mode'=>'HTML'
                ]);
            }

        return $this;
    }


    /**
     * @return int|mixed
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * @return mixed
     */
    public function getCompanyId()
    {
        return $this->companyId;
    }

    /**
     * @return null
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @return mixed
     */
    public function getRemoteUserId()
    {
        return $this->remoteUserId;
    }

}


//function check($string) {
//    $string = str_replace(['()', '{}', '[]'], '', $string, $count);
//    while ($count){
//        $string = str_replace(['()', '{}', '[]'], '', $string, $count);
//    }
//
//    return $string==='' ? 'true' : 'false';
//}


