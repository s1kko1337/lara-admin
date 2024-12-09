@extends('layout')

@section('content')
<div class="card shadow-lg p-4 rounded" style="border-radius: 15px; margin-top: 10px; margin-bottom: 10px; width: 85%; max-width: 600px; margin-left: auto; margin-right: auto;">
    <h2>Результаты проверки моделей</h2>

    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Название модели</th>
                <th>В базе данных</th>
                <th>На FTP сервере</th>
            </tr>
        </thead>
        <tbody>
            @foreach($results as $model)
                <tr>
                    <td>{{ $model['id'] }}</td>
                    <td>{{ $model['model_name'] }}</td>
                    <td>
                        @if($model['exists_in_db'])
                            <span class="text-success">Да</span>
                        @else
                            <span class="text-danger">Нет</span>
                        @endif
                    </td>
                    <td>
                        @if($model['exists_on_ftp'])
                            <span class="text-success">Да</span>
                        @else
                            <span class="text-danger">Нет</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection