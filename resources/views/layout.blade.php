<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>ADMIN CLIENT</title>
    <meta name="csrf-token" content="{{csrf_token()}}">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" href="...">
    <style>
        body {
            padding-top: 56px;
        }
        .navbar-brand {
            font-weight: bold;
        }
        .navbar-nav .nav-link {
            margin-right: 15px;
        }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
            <div class="container-fluid">
                <a class="navbar-brand" href="{{ route('user.home') }}">
                    ADMIN CLIENT
                </a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent"
                        aria-controls="navbarContent" aria-expanded="false" aria-label="Переключить навигацию">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarContent">
                    <!-- Левое меню -->
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a href="{{ route('user.home') }}" class="nav-link {{ request()->routeIs('user.home') ? 'active' : '' }}">
                                Главная
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('user.chats.index') }}" class="nav-link {{ request()->routeIs('user.chats.index') ? 'active' : '' }}">
                                Чаты
                            </a>
                        </li>
                    </ul>
                    <ul class="navbar-nav">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center"  id="userDropdown" role="button"
                               data-bs-toggle="dropdown" aria-expanded="false">
                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor"
                                     class="bi bi-person" viewBox="0 0 16 16">
                                    <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0m4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4m-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10s-3.516.68-4.168 1.332c-.678.678-.83 1.418-.832 1.664z"/>
                                </svg>
                                @auth
                                    <span class="ms-2">{{ Auth::user()->username }}</span>
                                @endauth
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <li><a class="dropdown-item" href="/profile">Настройки профиля</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="{{ route('user.logout') }}">Выйти</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <main class="col-12 " style="">
            @yield('content')
        </main>
    </div>
</div>

<!-- Подключение скриптов Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        @yield('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var dropdownToggle = document.querySelector('.nav-link dropdown-toggle d-flex align-items-center');
        var emailText = document.querySelector('.email-text');

        dropdownToggle.addEventListener('click', function(event) {
            event.preventDefault();
            var dropdownMenu = this.nextElementSibling;
            if (dropdownMenu.classList.contains('show')) {
                dropdownMenu.classList.remove('show');
                dropdownMenu.removeAttribute('style');
                emailText.classList.add('text-truncate');
            } else {
                dropdownMenu.classList.add('show');
                dropdownMenu.style.display = 'block';
                emailText.classList.remove('text-truncate');
            }
        });

        document.addEventListener('click', function(event) {
            var dropdownMenus = document.querySelectorAll('.dropdown-menu');
            dropdownMenus.forEach(function(menu) {
                if (!menu.contains(event.target) && !event.target.classList.contains('dropdown-toggle')) {
                    menu.classList.remove('show');
                    menu.removeAttribute('style');
                    emailText.classList.add('text-truncate');
                }
            });
        });
    });
</script>
</body>
</html>