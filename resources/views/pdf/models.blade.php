<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Отчет о загруженных моделях</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        h1, h2, p { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        ul { list-style-type: none; padding: 0; }
        li { margin-bottom: 5px; }
    </style>
</head>
<body>
    <h1>Отчет о загруженных моделях</h1>

    <p>Файлов загружено: {{ $filesCount }}</p>

    <h2>Список файлов</h2>
    <ul>
        @foreach ($files as $file)
            <li>{{ $file['name'] }} - Загружен: {{ $file['uploaded_at'] }}</li>
        @endforeach
    </ul>

    <br>

    <h2>Таблица моделей</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Название модели</th>
                <th>Превью модели</th>
                <th>Описание</th>
                <th>Путь к модели</th>
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
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>