<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{csrf_token()}}">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    @vite(['resources/js/app.js'])
</head>
<body class="d-flex align-items-center py-4 bg-body-tertiary">

<main class="form-signin w-100 m-auto">
  <form method="POST" action="{{ route('user.login')}}">
  @csrf
  <h1 class="h2 mb-3 fw-normal">Добро пожаловать</h1>

  <div class="form-floating">
    <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com">
    <label for="email">Эл.почта</label>
  </div>
  <div class="form-floating">
    <input type="password" class="form-control" id="password" name="password" placeholder="Password">
    <label for="password">Пароль</label>
  </div>

  <button class="btn btn-primary w-100 py-2" type="submit">Войти</button>

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
  <a class="btn btn-primary w-100 py-2 my-1" type="button" href="{{ ('registration') }}" >Зарегестрироваться</a>
  <p class="mt-3 mb-3 text-body-secondary">© UI 2024</p>
</main>
</body>
</html>