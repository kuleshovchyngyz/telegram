<?php


namespace App\Services;
use App\Models\PaymentAmount;
use App\Models\SiteSetting;
use App\Models\TelegramBot;
use Carbon\Carbon;
use http\Client\Curl\User;

class ViewData
{
    private $method;
    private $response;
    private $model;
    public function init($model=null,$method='')
    {
        $this->method = $method;
        $this->model =  $model;
        $this->handeModel();
        return $this;
    }
    protected function handeModel(){
        $this->model = ($this->model===null) ? auth()->user() : $this->model;
    }

    protected function getResponse($view)
    {
        switch ($view) {
            case 'botName':
                if(session('selected_bot_id')){
                    $bot = TelegramBot::find(session('selected_bot_id'));
                    if($bot){
                        $this->response = $bot->name;
                        break;
                    }
                }
                $this->response ="";
                break;
            case 'username':
                if(session('selected_bot_id')){
                    $bot = TelegramBot::find(session('selected_bot_id'));
                    if($bot) {
                        $this->response = $bot->username;
                        break;
                    }
                }
                $this->response = "";
                break;
            case 'token':
                if(session('selected_bot_id')){
                    $bot = TelegramBot::find(session('selected_bot_id'));
                    if($bot) {
                        $this->response = $bot->token;
                        break;
                    }
                }
                $this->response = "";
                break;

        }
        return $this;
    }
    public function totalPaymentByLead(){
        if($this->model!=null){
            $this->response = $this->model->all_amount();
        }
    }
    public function leadStatusName(){
        if($this->model!=null){
            $this->response = $this->model->status()->user_statuses->comments;
        }
    }
    protected function uniqueAmount($type){

        if($this->model->UserPaymentAmounts->count()==0){
            $this->defaultAmount($type);
        }else{
            if($this->model->UserPaymentAmounts->where('reason_of_payment',$type)->first()===null){
                $this->response = 0;
            }else{
                $this->response = $this->model->UserPaymentAmounts->where('reason_of_payment',$type)->first()->amount;
            }
        }
    }

    protected function defaultAmount($type){
        $this->response = PaymentAmount::where('reason_of_payment',$type)->first()->amount;
    }
    protected function route($type){

    }

    protected function checkError()
    {
//        if (isset($this->error->error) && ($this->error->error)) {
//            $this->errorMsg = $this->error->error_msg ?? '';
//        }
        return $this;
    }

    public function view($view)
    {
        $this->response = null;
        $this->error = null;
        $this->errorMsg = '';
        $this->responseMsg = '';
        $this->getResponse($view)->checkError()->getResponseMsg();
        return $this->responseMsg;
    }

    protected function getResponseMsg()
    {
        if ($this->errorMsg) {
            $this->responseMsg = $this->errorMsg;
        } else {
            $this->responseMsg = ($this->response!==null) ? $this->response : 'Не обнаружено';
        }
        return $this;
    }

    public function error()
    {
        return ($this->errorMsg != '') ? $this->errorMsg : 'Не обнаружено';
    }









}

