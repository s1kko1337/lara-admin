@extends('layout')

@section('content')
<div id="editModelModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <form action="{{ route('user.file.updateModel', $model->id) }}" method="POST" enctype="multipart/form-data">
      @csrf
      @method('PUT')

      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Редактировать модель</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>

        <div class="modal-body">

          <div class="form-group">
            <label for="description">Название модели:</label>
            <input type="text" name="model_name" class="form-control" value="{{ $model->model_name }}" required>
          </div>

          <div class="form-group">
            <label for="description">Описание модели:</label>
            <input type="text" name="description" class="form-control" value="{{ $model->additional_info }}" required>
          </div>

          <div class="form-group">
            <label for="file">Загрузить новую модель (если необходимо):</label>
            <input type="file" name="file" class="form-control">
          </div>
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Сохранить изменения</button>
         <button type="button" class="btn btn-default" onclick="window.history.back();">Закрыть</button>
        </div>
      </div>
    </form>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    $('#editModelModal').modal('show');

    $('#editModelModal').on('hidden.bs.modal', function () {
      window.history.back();
    });
  });
</script>
@endsection