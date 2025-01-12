<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    @vite(['resources/js/app.js'])
</head>
<body class="d-flex align-items-center py-4 bg-body-tertiary">

<main class="form-signin w-100 m-auto">
  <form method="POST" action="{{ route('user.register')}}">
  @csrf
  <h1 class="h2 mb-3 fw-normal">Регистрация</h1>

  <div class="form-floating my-1">
    <input type="text" class="form-control" id="username" name="username" placeholder="Username">
    <label for="username">Имя пользователя</label>
  </div>

  <div class="form-floating">
    <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com">
    <label for="email">Эл.почта</label>
  </div>
  <div class="form-floating">
    <input type="password" class="form-control" id="password" name="password" placeholder="Password">
    <label for="password">Пароль</label>
  </div>

  <button class="btn btn-primary w-100 py-2" type="submit">Зарегестрироваться</button>

  @if($errors->any())
    <div class="alert alert-danger my-1">
      <ul>
        @foreach($errors->all() as $error)
        <li>{{$error}}</li>
        @endforeach
      </ul>
    </div>
  @endif


</form>
  <p class="mt-3 mb-3 text-body-secondary">© UI 2024</p>
</main>
</body>
</html>