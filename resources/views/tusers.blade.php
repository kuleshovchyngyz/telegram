

@if (isset($t_users ))




            <h1>Пользователи</h1>

            <table class="table">
                <thead>
                <tr>
                    <th scope="col">Telegram ID</th>
                    <th scope="col">First name</th>
                    <th scope="col">Last name</th>
                    <th scope="col">Username</th>
                    <th scope="col">Статус</th>
                    <th scope="col">Действия</th>
                </tr>
                </thead>
                <tbody>
                    @foreach ($t_users as $t_user)


                            <tr>
                                <th scope="row"> {{  $t_user->t_id }}</th>
                                <td>{{  $t_user->first_name }}</td>
								<td>{{  $t_user->last_name }}</td>
                                <td>{{  $t_user->username }}</td>
                                <td>{{  $t_user->active }} </td>
                                <td>
								
									<a type="button" class="btn btn-success" data-toggle="modal" data-target="#CommentUser" data-whatever="@mdo">Примечание
									</a>
									<a href="{{route('user.changestatus', $t_user->id)}}" type="button" class="btn btn-info">Поменять статус</a>
                                    <a href="{{route('user.delete', $t_user->id)}}" type="button" class="btn btn-danger">Удалить
									</a>
                                </td>
                            </tr>
                    @endforeach
                </tbody>
            </table>



@endif

