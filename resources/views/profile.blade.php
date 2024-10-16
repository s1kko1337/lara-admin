@extends('layout')

@section('content')

<link rel="stylesheet" href="{{ asset('css/profile.css') }}">

<div class="card shadow-lg p-4 rounded" style="border-radius: 15px; margin-top: 10px; margin-bottom: 10px; width: 85%; max-width: 600px; margin-left: auto; margin-right: auto;">

    <h2 class="mb-2">Управление профилем</h2>
    <a href="{{ route('user.profile.editPortfolio') }}" class="btn btn-secondary">Редактировать Портфолио</a>

    <table class="table mb-1">
      <tr>
        <th>Имя</th>
        <td>@auth {{ Auth::user()->username }} @endauth</td> 
      </tr>
      <tr>
        <th>Адрес электронной почты</th>
        <td>@auth {{ Auth::user()->email }} @endauth</td>
      </tr>
    </table>

    <form method="POST" action="{{ route('user.profile.editName') }}">
      @csrf
      <div class="form-group mb-1">
        <label for="username">Изменить имя:</label>
        <input type="text" class="form-control" id="username" name="username" placeholder="Введите новое имя пользователя">
      </div>
      <button type="submit" class="btn btn-primary mb-2">Изменить имя</button>
    </form>

    <form method="POST" action="{{ route('user.profile.editEmail') }}">
      @csrf
      <div class="form-group mb-1">
        <label for="email">Эл.почта</label>
        <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com">
      </div>
      <button type="submit" class="btn btn-primary mb-2">Изменить эл.почту</button>
    </form>

    <h2 class="mb-4">Изменение пароля</h2>
    <form method="POST" action="{{ route('user.profile.updatePassword') }}">
      @csrf
      <div class="form-group mb-1">
        <label for="current_password">Текущий пароль:</label>
        <input type="password" class="form-control" id="current_password" name="current_password" required>
      </div>

      <div class="form-group mb-1">
        <label for="new_password">Новый пароль:</label>
        <input type="password" class="form-control" id="new_password" name="new_password" required>
      </div>

      <div class="form-group mb-1">
        <label for="new_password_confirmation">Подтверждение нового пароля:</label>
        <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation" required>
      </div>

      <button type="submit" class="btn btn-primary">Изменить пароль</button>
    </form>

    @if (session('success'))
      <div class="alert alert-success mt-4" role="alert">
        {{ session('success') }}
      </div>
    @elseif (session('error'))
      <div class="alert alert-danger mt-4" role="alert">
        {{ session('error') }}
      </div>
    @endif
  </div>

@endsection
