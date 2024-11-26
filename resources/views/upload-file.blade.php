@extends('layout')

@section('content')
<div class="card shadow-lg p-4 rounded" style="border-radius: 15px; margin-top: 10px; margin-bottom: 10px; width: 85%; max-width: 700px; margin-left: auto; margin-right: auto;">
        <h2 class="mb-4">Загрузить файл модели</h2>

        <form method="POST" enctype="multipart/form-data" action="{{ route('user.file.upload') }}">
        @csrf

            <div class="form-group mb-3">
                <label for="description">Название модели:</label>
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
    <div class="card shadow-lg p-4 rounded" style="border-radius: 15px; margin-top: 10px; margin-bottom: 10px; width: 85%; max-width: 700px; margin-left: auto; margin-right: auto;">
    <a href="{{ route('user.file.downloadCsv') }}" class="btn btn-success mb-3">Скачать CSV</a>    
    <a href="{{ route('user.file.exportPdf') }}" class="btn btn-primary mb-3">Экспорт в PDF</a>
    <h3>Список загруженных моделей</h3>
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
    </div>
@endif
@endsection

@section('scripts')
<script>
    document.getElementById('uploadForm').addEventListener('submit', function(event) {
        event.preventDefault();

        const formData = new FormData(this);

        fetch('{{ route('user.file.upload') }}', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Файл успешно загружен!');
            } else {
                alert('Ошибка при загрузке файла: ' + data.error);
            }
        })
        .catch(error => {
            log.error('Ошибка:', error);
            alert('Произошла ошибка при загрузке файла.');
        });
    });
</script>
@endsection



