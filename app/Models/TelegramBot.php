<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TelegramBot extends Model
{
    use HasFactory;
    protected $fillable = ['name','token','username', 'update_id'];
    public function companies(){
        return $this->hasMany(Company::class);
    }
}
