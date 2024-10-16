@extends('admin')

@section('content')
    @parent 
    <main class="container mt-10">
        <hr>
        <div class="row">
            <div class="col">
                <h2>Добавить пользователя</h2>
                <main class="form-signin w-100 m-auto">
<form method="POST" action="{{ route('user.saveUser') }}">
  @csrf

  <div class="form-floating my-1">
    <input type="text" class="form-control" id="username" name="username" placeholder="Username" required>
    <label for="username">Имя пользователя</label>
</div>

<div class="form-floating">
    <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com" required>
    <label for="email">Эл.почта</label>
</div>

<div class="form-floating">
    <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
    <label for="password">Пароль</label>
</div>

  <button class="btn btn-primary w-100 py-2" type="submit">Добавить</button>
            </div>
        </div>
    </main>
@endsection