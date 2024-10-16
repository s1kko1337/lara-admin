@extends('layout')

@section('content')
    @parent
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Редактирование  {{ $tableNames[$tableName] }}</h1>
    </div>

    <div class="table-responsive small">
        <table class="table table-sm">
            <thead>
                <tr>
                    <th>#</th>
                    @foreach ($editableColumns as $column)
                        <th>{{ $column }}</th> 
                    @endforeach
                    <th>Действия</th> 
                </tr>
            </thead>
            <tbody>
                @foreach ($tableData as $row)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <form action="{{ route('user.admintables.update', ['tableName' => $tableName, 'id' => $row->$tableId ]) }}" method="POST">
                            @csrf
                            @method('PUT')
                            @foreach ($editableColumns as $column)
                                <td>
                                    
                                    <input type="text" class="form-control" id="{{ $column }}" name="{{ $column }}" value="{{ $row->$column }}" required>
                                
                                </td>
                            @endforeach 
                            <td class="inline" style="display: flex;">
                                <button type="submit" class="btn btn-primary">Сохранить</button>
                            </td> 
                        </form>
                        <td>
                            <form action="{{ route('user.admintables.delete', ['tableName' => $tableName, 'id' => $row->$tableId]) }}" method="POST" style="margin-left: 10px;">
                                @csrf
                                @method('DELETE') 
                                <button type="submit" class="btn btn-danger">Удалить</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                @if ($tableName !== 'users' && $tableName !== 'sales' && $tableName !== 'sale_details' && $tableName !=='supplies_status')
                <tr>
                    <td>#</td>
                    <form action="{{ route('user.admintables.add', ['tableName' => $tableName]) }}" method="POST">
                        @csrf
                        @foreach ($editableColumns as $column)
                            <td>
                                <input type="text" class="form-control" name="{{ $column }}" placeholder="{{ $column }}" required>
                            </td>
                        @endforeach
                        <td class="inline" style="display: flex;">
                            <button type="submit" class="btn btn-success">Добавить</button>
                        </td>
                    </form>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
@endsection