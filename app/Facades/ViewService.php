<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class ViewService extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'init';
    }
}
