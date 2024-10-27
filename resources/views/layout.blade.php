<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADMIN CLIENT</title>
    <meta name="csrf-token" content="{{csrf_token()}}">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-cart3' viewBox='0 0 16 16'%3E%3Cpath d='M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .49.598l-1 5a.5.5 0 0 1-.465.401l-9.397.472L4.415 11H13a.5.5 0 0 1 0 1H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5M3.102 4l.84 4.479 9.144-.459L13.89 4zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4m7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4m-7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2m7 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2'/%3E%3C/svg%3E" type="image/svg+xml">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>
<body>
<div class="container-fluid">
    <div class="row">
    <div class="col-md-3 col-lg-2 d-flex flex-column flex-shrink-0 p-3">
    <div class="card rounded shadow-sm p-3"> <!-- Добавлена обертка для карточки -->
        <div class="dropdown mb-3">
            <button class="btn btn-light btn-secondary dropdown-toggle w-100 text-truncate" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-person" viewBox="0 0 16 16">
                    <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0m4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4m-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10s-3.516.68-4.168 1.332c-.678.678-.83 1.418-.832 1.664z"/>
                </svg>
                @auth
                    {{ Auth::user()->username }} 
                @endauth
            </button>

            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                <li><a class="dropdown-item" href="/profile">Настройки профиля</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="{{ route('user.logout') }}">Выйти</a></li>
            </ul>
        </div>
        <hr>
        <ul class="nav nav-pills flex-column mb-auto">
            <li class="nav-item">
            <a href="{{ route('user.chats.index') }}" class="nav-link {{ request()->routeIs('user.chats.index') ? 'active' : '' }}" aria-current="page">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chat-dots" viewBox="0 0 16 16">
                <path d="M5 8c0 .552.224 1 .5 1s.5-.448.5-1-.224-1-.5-1-.5.448-.5 1zm3 0c0 .552.224 1 .5 1s.5-.448.5-1-.224-1-.5-1-.5.448-.5 1zm3 0c0 .552.224 1 .5 1s.5-.448.5-1-.224-1-.5-1-.5.448-.5 1z"/>
                <path d="M14 1H2a1 1 0 0 0-1 1v9a1 1 0 0 0 1 1h1.586l2.707 2.707a1 1 0 0 0 1.414 0L9.414 12H14a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1zm0 1a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H9.586l-3 3L3 11H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h12z"/>
            </svg>
            Чаты
        </a>
            </li>
            <li>
                <a href="{{ route('user.admintables') }}" class="nav-link {{ request()->routeIs('tables') ? 'active' : '' }}">
                    <svg class="bi pe-none me-2" width="16" height="16"><use xlink:href="#speedometer2"></use></svg>
                    Администрирование таблиц
                </a>
            </li>
        </ul>
    </div> <!-- Закрываем карточку -->
</div>

        
        <main class="col-md-9 col-lg-10 vh-100" style="overflow-y: auto;">
            @yield('content')
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
@yield('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var dropdownToggle = document.querySelector('.dropdown-toggle');
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