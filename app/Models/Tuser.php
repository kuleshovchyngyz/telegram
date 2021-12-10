<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tuser extends Model
{
    use HasFactory;
    protected $fillable = ['t_id',
                'first_name',
                'username',
                'active',
                'company_id',
        ];
}
