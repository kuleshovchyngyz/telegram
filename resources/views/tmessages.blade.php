@if (isset($message ))

    <h1>Telegram-рассылка</h1>
    <br>
    <h2>Массовая (всем пользователям) рассылка</h2>
    <br>
    <form method="POST" action="{{ route('company.sendMessages', session('selected_company_id')) }}">
        @csrf
    <div class="form-group">
        <textarea rows="5"  class="form-control w-50 mb-3" name="message" placeholder="сообщение"></textarea>
        <button type="submit" class="btn btn-success w-50 ">Отправить</button>
    </div>
    </form>


    <br>
    <h2>Пользовательская (конкретному пользователю) рассылка</h2>
    <br>
@if(isset($users))

    <form method="POST" action="{{ route('company.sendmessage', session('selected_company_id')) }}">
        @csrf
        <div class="w-25 mb-3">

        <select class="form-control" name="t_id">
            @foreach ($users as $user)
            <option value="{{ $user->t_id }}">{{ $user->username  }}</option>
            @endforeach
        </select>
        </div>


    <div class="form-group">
        <textarea rows="5"  class="form-control w-50 mb-3" id="message"  name= "message" placeholder="сообщение"></textarea>
        <button type="submit" class="btn btn-success w-50">Отправить</button>
    </div>
    </form>
@endif
    @if($messages)
    <table class="table">
        <thead>
        <tr>
            <th scope="col">ID</th>
            <th scope="col">сообщение</th>
            <th scope="col">Статус</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($messages as $message)


            <tr>
                <th scope="row"> {{  $message->id }}</th>
                <td>{{  $message->message }}</td>
                <td>{{  $message->status }}</td>


            </tr>
        @endforeach
        </tbody>
    </table>
    {{ $messages->links() }}

@endif
@endif

