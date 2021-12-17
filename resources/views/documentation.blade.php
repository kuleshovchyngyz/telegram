@extends('layouts.app')
@section('content')
    <div class="container">
        <ol>
                <li>Нажав на + создаем компанию. Автоматически выдает уникальний ID или код. </li>
                <li>Выбираем компанию после нажатии на редактирования получаем код компании</li>
                <li>Пользователи компании смогут зарегистрироваться нажав на start в телеграм боте <a target="_blank" href="http://t.me/{{str_replace('@','',ViewService::init()->view('username'))}}">{{ str_replace('@','',ViewService::init()->view('username')) }}</a>  и отправив код компании</li>
                <li>Сообщение получает в формате <a href="{{ route('apilink') }}" target="_blank">json</a> и отправляет всем пользователям. {{ route("api") }} </li>
                <li>Сообщение получает в формате <a href="{{ route('apilinkuser') }}" target="_blank">json</a> и отправляет конкретному пользователю(userId). (userId можно получить в разделе "Users" столбик Telegram ID)</li>
            </ol>

    </div>
@endsection
