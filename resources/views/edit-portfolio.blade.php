@extends('layout')

@section('content')
<div class="card shadow-lg p-4 rounded" style="border-radius: 15px; margin-top: 10px; margin-bottom: 10px; width: 85%; max-width: 600px; margin-left: auto; margin-right: auto;">
        <h2 class="mb-4">Редактировать Портфолио</h2>

        <!-- Форма редактирования -->
        <form method="POST"  enctype="multipart/form-data" action="{{ route('user.profile.updatePortfolio') }}">
            @csrf
            <!-- Поле для main_info -->
            <div class="form-group mb-3">
                <label for="main_info">Основная информация:</label>
                <textarea class="form-control" id="main_info" name="main_info[info]" rows="3">{{ $portfolio->main_info['info'] ?? '' }}</textarea>
            </div>

            <!-- Поле для additional_info -->
            <div class="form-group mb-3">
                <label for="additional_info">Дополнительная информация:</label>
                <textarea class="form-control" id="additional_info" name="additional_info[info]" rows="3">{{ $portfolio->additional_info['info'] ?? '' }}</textarea>
            </div>

            <!-- Поля для media_links с предустановленными частями ссылок -->
            <div class="form-group mb-3">
                <label for="artstation">ArtStation:</label>
                <input type="text" class="form-control" id="artstation" name="media_links[artstation]" 
                       placeholder="Введите ваш ID" 
                       value="{{ basename($portfolio->media_links['artstation'] ?? '') }}">
                <small class="form-text text-muted">https://artstation.com/</small>
            </div>

            <div class="form-group mb-3">
                <label for="tg">Telegram:</label>
                <input type="text" class="form-control" id="tg" name="media_links[tg]" 
                       placeholder="Введите ваш ID" 
                       value="{{ basename($portfolio->media_links['tg'] ?? '') }}">
                <small class="form-text text-muted">https://t.me/</small>
            </div>

            <div class="form-group mb-3">
                <label for="vk">ВКонтакте:</label>
                <input type="text" class="form-control" id="vk" name="media_links[vk]" 
                       placeholder="Введите ваш ID" 
                       value="{{ basename($portfolio->media_links['vk'] ?? '') }}">
                <small class="form-text text-muted">https://vk.com/</small>
            </div>

            <div class="form-group mb-3">
                <label for="inst">Instagram:</label>
                <input type="text" class="form-control" id="inst" name="media_links[inst]" 
                       placeholder="Введите ваш ID" 
                       value="{{ basename($portfolio->media_links['inst'] ?? '') }}">
                <small class="form-text text-muted">https://instagram.com/</small>
            </div>

            <button type="submit" class="btn btn-primary">Обновить Портфолио</button>
        </form>
        <form method="POST" action="{{ route('user.profile.deletePortfolio') }}">
             @csrf
             @method('DELETE')
             <button type="submit" class="btn btn-danger" onclick="return confirm('Вы уверены, что хотите удалить портфолио?')">Удалить</button>
        </form>
    </div>
@endsection
