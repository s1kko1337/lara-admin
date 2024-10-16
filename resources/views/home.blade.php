@extends('layout')

@section('content')
<button id="checkFtpButton" class="btn btn-secondary">Проверить подключение к FTP</button>
<div id="ftpStatus"></div>


<div class="card">
    <div class="card-header">
        <h5 class="card-title">Загрузка файла</h5>
    </div>
    <div class="card-body">
        <form id="uploadForm" enctype="multipart/form-data" method="POST" action="{{ route('user.upload') }}">
            @csrf
            <div class="form-group">
                <label for="fileInput">Выберите файл</label>
                <input type="file" class="form-control-file" id="fileInput" name="file" required>
            </div>
            <div class="form-group">
                <label for="description">Описание</label>
                <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Загрузить</button>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

<script>
    $(document).ready(function() {
        $('#checkFtpButton').on('click', function() {
            $.ajax({
                url: '{{ route('user.check.ftp') }}',
                method: 'GET',
                success: function(data) {
                    if (data.success) {
                        $('#ftpStatus').html('<div class="alert alert-success">Подключение к FTP успешно! Файлы: ' + data.files.join(', ') + '</div>');
                    } else {
                        $('#ftpStatus').html('<div class="alert alert-danger">Ошибка: ' + data.error + '</div>');
                    }
                },
                error: function(xhr, status, error) {
                    $('#ftpStatus').html('<div class="alert alert-danger">Произошла ошибка при проверке подключения к FTP.</div>');
                }
            });
        });
    });
</script>

<script>
    document.getElementById('uploadForm').addEventListener('submit', function(event) {
        event.preventDefault();

        const formData = new FormData(this);

        fetch('{{ route('user.upload') }}', {
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
