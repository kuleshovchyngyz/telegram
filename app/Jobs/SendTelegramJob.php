<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Telegram\Bot\Laravel\Facades\Telegram;
use App\Models\Company;
use Telegram\Bot\Api;

class SendTelegramJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @param $data
     */

    protected $data,$bot,$token;

    public function __construct($data)
    {
        $this->bot = $data['company'] ?? false;
        if($this->bot!==false){
            $this->token = $this->bot->telegramBot->token;
        }
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if(!$this->bot){
            Telegram::sendMessage($this->data);
        }else{
            $telegram = new Api($this->token);
            $response = $telegram->sendMessage($this->data);
        }
    }
}
