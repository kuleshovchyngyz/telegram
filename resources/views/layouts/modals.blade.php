<!-- Button trigger modal -->


  <!-- Modal -->
@auth
  <div class="modal fade" id="CreateCompany" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
          <form method="POST" action="{{ route('company.create') }}">
              @csrf
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Добавление компании</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <input type="text" class="form-control input_type1" id="name" name="name" placeholder="Название компании">
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
          <button type="submit" class="btn btn-success">Создать</button>
        </div>
          </form>
      </div>
    </div>
  </div>


  <div class="modal fade" id="CreateBot" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
          <div class="modal-content">
              <form method="POST" action="{{ route('bot.create') }}">
                  @csrf
                  <div class="modal-header">
                      <h5 class="modal-title" id="exampleModalLabel">Добавление бота</h5>
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                      </button>
                  </div>
                  <div class="modal-body">
                      <div class="form-group">
                          <input type="text" class="form-control input_type1" id="name" name="name" placeholder="Название Бота">
                      </div>
                      <div class="form-group">
                          <input type="text" class="form-control input_type1" id="username" name="username" placeholder="@username">
                      </div>
                      <div class="form-group">
                          <input type="text" class="form-control input_type1" id="token" name="token" placeholder="Token">
                      </div>
                  </div>
                  <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                      <button type="submit" class="btn btn-success">Создать</button>
                  </div>
              </form>
          </div>
      </div>
  </div>

@if(session('selected_company_name'))
<div class="modal fade" id="EditCompany" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" action="{{ route('company.edit', session('selected_company_id')) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Редактирование компании</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        Компания<input type="text" class="form-control input_type1" id="name" name="name" value="{{ session('selected_company_name') }}">
                    </div>
                    <div class="form-group">
                        Код для получений сообщений<input type="text" class="form-control input_type1" id="botusername" name="botusername" value="{{ session('selected_company_botname') }}" disabled>
                    </div>
					<div class="form-group">
                        Код для регистраций телеграм пользователей<input type="text" class="form-control input_type1" id="botusername" name="botusername" value="{{ session('selected_company_bot_usercode') }}" disabled>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                    <a class="btn btn-danger" href="{{ route('company.destroy', session('selected_company_id')) }}" role="button">Удалить</a>
                    <button type="submit" class="btn btn-success">Редактировать</button>
                </div>
            </form>

        </div>
    </div>
</div>

<div class="modal fade" id="CommentUser" tabindex="-1" role="dialog" aria-labelledby="CommentUserLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="CommentUserLabel">New message</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form>
          <div class="form-group">
            <label for="recipient-name" class="col-form-label">Recipient:</label>
            <input type="text" class="form-control" id="recipient-name">
          </div>

        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
        <button type="button" class="btn btn-primary">Сохранить</button>
      </div>
    </div>
  </div>
</div>
@endif
@endauth
