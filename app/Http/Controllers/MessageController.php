<?php

namespace App\Http\Controllers;

use App\Jobs\SendTelegramJob;
use App\Models\Company;
use App\Models\Message;
use App\Models\Tuser;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

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
     public function test()
    {
        http://api.megaindex.com/scanning/check?key=540485616b3ae46c687fbc79f6c9be1f&method=google_position&task_id=cb025bb2ee9a5c018d0fd347cdfa25db
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getmessages(Request $request){
        if ($request->isJson()) {
            \Storage::disk('local')->append('example.txt', json_encode($request->all(),JSON_UNESCAPED_UNICODE));
            $company_code = $request->all()["companycode"];
            $hash = md5(serialize( $request->all()));
            $c_id = Company::where('companycode',$company_code)->value('id');//Just for comaring for uniqueness of the given telegram id
            $spam = Message::where('uniquecode',$hash)->whereBetween('created_at', [now()->subMinutes(1), now()])->get()->count();//Making sure not to send too many identical json content
            if($spam==0)
            {
                if($c_id){
                    $data = ['status' => 'success'];
                    $messages = $request->all()["data"];
                    if($messages){
                        foreach ($messages as $message) {
                            if(!isset($message['userId'])){
                                \Storage::disk('local')->append('whi.txt',1);
                                Message::create([
                                    'message' => $message["message"],
                                    'status' => false,
                                    'company_id' => $c_id,
                                    'companycode' => "companycode",
                                    'uniquecode' => $hash
                                ]);
                            }else{
                                \Storage::disk('local')->append('whi.txt',2);
                                SendTelegramJob::dispatch([
                                    'chat_id' =>$message["userId"],
                                    'text' => $message["message"],
                                    'company' => Company::find($company_code),
                                    'parse_mode' => 'HTML'
                                ])->delay(now()->addSeconds(5));
                            }
                        }
                    }else{
                        $data = ['status' => 'failed','error'=>'no message'];
                    }

                }else{
                    $data = ['status' => 'failed','error'=>'invalid code'];

                }

            }else{
                $data = ['status' => 'failed','error'=>'Many identical message content'];
            }
            echo response()->json($data);
            $this->sendMessageToUsers();
        }
    }
    public function sendMessageToUsers()
    {
        $messages = Message::where('status', false)->get(['*']);
        $t_users = Tuser::where('active',true)->get(['company_id','t_id','first_name','username']);
        foreach ($t_users as $t_user)
        {
            foreach ($messages as $message)
            {
                if($t_user["company_id"]==$message["company_id"])
                {
                    SendTelegramJob::dispatch([
                        'chat_id' =>$t_user['t_id'],
                        'text' => $message["message"],
						'company' => Company::find($message["company_id"]),
						'parse_mode' => 'HTML'
                    ])->delay(now()->addSeconds(20));
                    Message::where("id",$message["id"])->update(['status'=>true]);
                }
            }
        }
    }
    public function store(Request $request)
    {
        dd($request->all());
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
    public function destroy($id)
    {
        //
    }
}
