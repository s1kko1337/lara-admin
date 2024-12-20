@extends('layout')

@section('content')
<div class="card shadow-lg p-4 rounded" style="margin-top: 10px; margin-bottom: 10px; width: 85%; max-width: 700px; margin-left: auto; margin-right: auto;">
    <h2 class="mb-4">Загрузить файл модели</h2>

    <form method="POST" enctype="multipart/form-data" action="{{ route('user.file.upload') }}">
    @csrf

        <div class="form-group mb-3">
            <label for="model_name">Название модели:</label>
            <textarea class="form-control" id="model_name" name="model_name" rows="1" required></textarea>
        </div>

        <div class="form-group mb-3">
            <label for="preview">Превью изображения</label>
            <input type="file" name="preview" id="preview" accept=".jpg" required>
        </div>

        <div class="form-group mb-3">
            <label for="file">Выберите файл:</label>
            <input type="file" class="form-control" id="file" name="file" required>
        </div>

        <div class="form-group mb-3">
            <label for="description">Описание модели:</label>
            <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Загрузить модель</button>
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

@if ($models->count())
<div class="card shadow-lg p-4 rounded" style="margin-top: 10px; margin-bottom: 10px; width: 85%; max-width: 700px; margin-left: auto; margin-right: auto;">
    <div class="dropdown mb-3">
        <button class="btn btn-secondary dropdown-toggle" type="button" id="actionsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
            Действия
        </button>
        <ul class="dropdown-menu" aria-labelledby="actionsDropdown">
            <li><a class="dropdown-item" href="{{ route('user.file.downloadCsv') }}">Скачать CSV</a></li>
            <li><a class="dropdown-item" href="{{ route('user.file.exportPdf') }}">Экспорт в PDF</a></li>
            <li>
                <div class="px-4 py-3">
                    <form action="{{ route('user.file.checkModels') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="csv_file" class="form-label">Загрузите CSV файл:</label>
                            <input type="file" name="csv_file" id="csv_file" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary mb-3">Проверить модели</button>
                    </form>
                </div>
            </li>
        </ul>
    </div>
    <form method="GET" action="{{ route('user.file.listModels') }}" class="mb-4">
        <div class="row">
            <div class="col-md-4 mb-2">
                <input type="text" name="search_id" class="form-control" placeholder="Поиск по ID" value="{{ request('search_id') }}">
            </div>
            <div class="col-md-4 mb-2">
                <input type="text" name="search_name" class="form-control" placeholder="Поиск по названию" value="{{ request('search_name') }}">
            </div>
            <div class="col-md-4 mb-2">
                <button type="submit" class="btn btn-primary">Поиск</button>
                <a href="{{ route('user.file.listModels') }}" class="btn btn-secondary">Сбросить</a>
            </div>
        </div>
    </form>
    <h3>Список загруженных моделей</h3>
    @if ($models->isEmpty())
        <p class="text-center">Модели не найдены.</p>
    @else
        <table class="table table-bordered mt-3">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Название модели</th>
                    <th>Превью</th>
                    <th>Описание</th>
                    <th>Путь к модели</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($models as $model)
                <tr>
                    <td>{{ $model->id }}</td>
                    <td>{{ $model->model_name }}</td>
                    <td>
                        @if($model->binary_preview)
                            <img src="data:image/jpeg;base64,{{ $model->binary_preview }}" alt="Preview" width="100">
                        @else
                            Нет превью
                        @endif
                    </td>
                    <td>{{ $model->additional_info }}</td>
                    <td>{{ $model->path_to_model }}</td>
                    <td>
                        <form method="POST" action="{{ route('user.file.deleteModel', $model->id) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Вы уверены, что хотите удалить эту модель?')">Удалить</button>
                        </form>
                        <a href="{{ route('user.file.editModel', $model->id) }}" class="btn btn-primary">Редактировать</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endif
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var dropdowns = document.querySelectorAll('.dropdown-toggle');
        dropdowns.forEach(function (dropdownToggle) {
            new bootstrap.Dropdown(dropdownToggle);
        });
    });
</script>
@endsection