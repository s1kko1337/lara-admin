@extends('layout')

@section('content')
<div class="card shadow-lg p-4 rounded" style="border-radius: 15px; margin-top: 10px; margin-bottom: 10px; width: 85%; max-width: 600px; margin-left: auto; margin-right: auto;">
  <div class="card-header">
    <h4 class="card-title">Редактировать модель</h4>
  </div>

  <div class="card-body">
    <form action="{{ route('user.file.updateModel', $model->id) }}" method="POST" enctype="multipart/form-data">
      @csrf
      @method('PUT')

      <div class="form-group">
        <label for="model_name">Название модели:</label>
        <input type="text" name="model_name" class="form-control" value="{{ $model->model_name }}" required>
      </div>

      <div class="form-group">
        <label for="description">Описание модели:</label>
        <input type="text" name="description" class="form-control" value="{{ $model->additional_info }}" required>
      </div>

      <div class="form-group">
        <label for="preview">Обновить превью изображения</label>
        <input type="file" name="preview" id="preview" accept="image/*">
      </div>

      <div class="form-group">
        <label for="file">Загрузить новую модель (если необходимо):</label>
        <input type="file" name="file" class="form-control">
      </div>

      <div class="card-footer text-right">
        <button type="submit" class="btn btn-primary">Сохранить изменения</button>
        <button type="button" class="btn btn-secondary" onclick="window.history.back();">Закрыть</button>
      </div>
    </form>
  </div>
</div>
@endsection