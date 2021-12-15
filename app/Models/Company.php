<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;
    protected $fillable  = [

        'name',
        'companycode',
		'usercode',
		'telegram_bot_id',
        'webhook'

    ];
    /**
     * @var mixed
     */
    private $id;
    /**
     * @var mixed
     */
    private $botusername;
    public function telegramBot(){
        return $this->belongsTo(TelegramBot::class);
    }
}
