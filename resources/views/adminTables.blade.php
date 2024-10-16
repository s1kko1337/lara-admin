@extends('layout')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Таблицы</h1>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Наименование таблицы</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($tables as $key => $table)
                    @if ($table !== 'migrations' && $table !== 'failed_jobs')
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>
                                <a href="{{ route('user.admintables.edit', $table) }}">
                                    {{ $tableNames[$table] ?? $table }}
                                </a>
                            </td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
