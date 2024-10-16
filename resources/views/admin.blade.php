@extends('profile')

@section('content')
    @parent 
    <div class="d-flex align-items-start">
    <main class="container mt-10">
    <hr>
    <div class="row">
        <div class="col">
            <h2>Список пользователей</h2>
            <div class="mb-3">
            <a href="{{ route('user.add') }}" class="btn btn-primary">Добавить</a>
                </div>
            <table id="users-table" class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Имя пользователя</th>
                        <th>Эл. почта</th>
                        <th>Должность</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->username }}</td>
                        <td>{{ $user->email }}</td>
                        <a href="{{ route('user.edit', $user->id) }}" class="btn btn-primary">Редактировать</a>
                             <form action="{{ route('user.delete', $user->id) }}" method="POST" style="display:inline">
                                 @csrf
                                 @method('DELETE')
                                 <button type="submit" class="btn btn-danger">Удалить</button>
                             </form>
                        </td>

                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</main>
</div>


@endsection
@yield('script')
<script>

/*function getUserById(userId) {
    return $.ajax({
        url: '/user/edit/' + userId,
        type: 'GET',
        success: function(response) {
            return response.user; 
        },
        error: function(xhr, status, error) {
            console.error(xhr.responseText);
        }
    });
}

    /*$(document).ready(function() {
        $('.edit-user-btn').click(function() {
            var userId = $(this).data('user-id');
            var user = getUserById(userId); // Функция для получения данных пользователя по его ID
            fillEditUserForm(user);
        });

        function getUserById(userId) {
            // Ваш код для получения данных пользователя по его ID
            // Возможно, вам нужно будет использовать AJAX-запрос
            // Возвращайте объект пользователя с данными
        }

        function fillEditUserForm(user) {
            $('#edit-username').val(user.username);
            $('#edit-email').val(user.email);
            // Заполните другие поля формы, если необходимо
            $('#editUserForm').attr('action', '/user/update/' + user.id); // Установите атрибут action формы для отправки данных на сервер
        }
    });
*/
/*setInterval(function() {
    $.ajax({
        url: '{{-- route("user.admin.get.updated.users") --}}',
        type: 'GET',
        success: function(response) {
            $('#users-table tbody').empty();
            
            response.users.forEach(function(user) {
                var roleId = user.role_id; // Получаем ID роли пользователя
                var roleName = response.userRoles[roleId]; // Получаем название роли по ID из объекта userRoles

                var newRow = '<tr>' +
                    '<td>' + user.id + '</td>' +
                    '<td>' + user.username + '</td>' +
                    '<td>' + user.email + '</td>' +
                    '<td>' + roleName + '</td>' + // Используем полученное название роли
                    '<td>' +
                    '<a href="#" class="btn btn-primary">Редактировать</a>' +
                    '</td>' +
                    '</tr>';
                $('#users-table tbody').append(newRow);
            });
        }
    });
}, 5000);
*/
</script>
