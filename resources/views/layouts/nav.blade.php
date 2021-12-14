


<nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
    <div class="container">
    @auth
            <div class="dropdown show">
                @if (session('selected_bot_name') )
                    <div class="btn btn-secondary dropdown-toggle bg-light text-body" href="#" role="button" id="telegramLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        {{ session('selected_bot_name') }}
                    </div>
                @else
                    <div class="btn btn-secondary dropdown-toggle bg-light text-body" href="#" role="button" id="telegramLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Выберите компанию
                    </div>
                @endif
                <div class="dropdown-menu" id="telegramBot" aria-labelledby="telegramLink">
                    <div class="dropdown-item">
                        <a data-toggle="modal" data-target="#CreateBot" class="d-flex justify-content space-between"> <i class="fa fa-plus" title="Добавить бот"><span>Добавить бот</span></i></a>
                    </div>
                    @foreach (\App\Models\TelegramBot::all() as $bot)
                        <div class="dropdown-item" data-id="{{$bot->id}}">{{ $bot->name }}</div>
                    @endforeach
                </div>

            </div>

        <div class="dropdown show">
            @if (session('selected_company_name') )
                <a class="btn btn-secondary dropdown-toggle bg-light text-body" href="#" role="button" id="CompaniesLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    {{ session('selected_company_name') }}
                </a>
            @else
                <a class="btn btn-secondary dropdown-toggle bg-light text-body" href="#" role="button" id="CompaniesLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Выберите компанию
                </a>
            @endif

            <div class="dropdown-menu" id="companies" aria-labelledby="CompaniesLink">
                <div class="dropdown-item">
                    <a data-toggle="modal" data-target="#CreateCompany" class="d-flex justify-content space-between"> <i class="fa fa-plus" title="Добавить компанию"><span>Добавить компанию</span></i></a>
                </div>
                @foreach ($companies as $company)
                    <a class="dropdown-item" href="{{ route('company.select', $company->id) }}">{{ $company->name }}</a>
                @endforeach
            </div>
        </div>
{{--        <a  id="menu-toggle"> <i class="fa fa-bars"></i></a>--}}
    @endauth
    <div class="container">
        <a class="navbar-brand" href="{{ url('/') }}">
            Home
        </a>
        <button class="navbar-toggler" type="button" data-toggle    ="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <!-- Left Side Of Navbar -->
            <ul class="navbar-nav mr-auto">

            </ul>

            <!-- Right Side Of Navbar -->
            <ul class="navbar-nav ml-auto">
                <!-- Authentication Links -->
                @guest
                    @if (Route::has('login'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                        </li>
                    @endif

{{--                    @if (Route::has('register'))--}}
{{--                        <li class="nav-item">--}}
{{--                            <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>--}}
{{--                        </li>--}}
{{--                    @endif--}}
                @else
                    <li class="nav-item dropdown">
                        <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            {{ Auth::user()->name }}
                        </a>

                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="{{ route('logout') }}"
                               onclick="event.preventDefault();
                                             document.getElementById('logout-form').submit();">
                                {{ __('Logout') }}
                            </a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </div>
                    </li>
                @endguest
            </ul>
        </div>
    </div>



    </div>
</nav>

