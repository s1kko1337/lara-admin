<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MainContentController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\AdminTablesController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\FileUploadController;
use App\Http\Controllers\ChatController;

Route::name('user.')->group(function() {
    // Маршруты для незалогиненных пользователей
    Route::middleware('guest')->group(function() {
        Route::get('/', function() {
            return view('login');
        })->name('login');

        Route::post('/', [LoginController::class, 'login']);

        Route::get('/registration', function() {
            return view('register');
        })->name('register');

        Route::post('/registration', [RegisterController::class, 'save']);
    });

    // Маршруты для авторизованных пользователей
    Route::middleware('auth')->group(function() {
        Route::get('/home', [MainContentController::class, 'showHome'])->name('home');
        Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
        Route::post('/profile/update-password', [ProfileController::class, 'updatePassword'])->name('profile.updatePassword');
        Route::get('/profile/edit-portfolio', [ProfileController::class, 'editPortfolio'])->name('profile.editPortfolio');
        Route::post('/profile/update-portfolio', [ProfileController::class, 'updatePortfolio'])->name('profile.updatePortfolio');
        Route::delete('/profile/delete-portfolio', [ProfileController::class, 'deletePortfolio'])->name('profile.deletePortfolio');    

        // Route::get('/admintables', [AdminTablesController::class, 'showTables'])->name('admintables');
        // Route::get('/admintables/{tableName}/edit', [AdminTablesController::class, 'editTable'])->name('admintables.edit');
        // Route::put('/admintables/{tableName}/update/{id}', [AdminTablesController::class, 'updateTable'])->name('admintables.update');
        // Route::get('/admintables/{tableName}/edit/add', [AdminTablesController::class, 'addTable'])->name('admintables.add');
        // Route::post('/admintables/{tableName}/edit/add', [AdminTablesController::class, 'addTable'])->name('admintables.add');
        // Route::delete('/admintables/{tableName}/delete/{id}', [AdminTablesController::class, 'destroy'])->name('admintables.delete');

        Route::post('/profile/edit-name', [ProfileController::class, 'editName'])->name('profile.editName');
        Route::post('/profile/edit-email', [ProfileController::class, 'editEmail'])->name('profile.editEmail');
        Route::get('/get-updated-users', [ProfileController::class, 'getUpdatedUsers'])->name('admin.get.updated.users');

        Route::get('/user/add', [ProfileController::class, 'add'])->name('add');
        Route::post('/user/add', [ProfileController::class, 'saveUser'])->name('saveUser');

        Route::get('/user/edit/{id}', [ProfileController::class, 'edit'])->name('edit');
        Route::put('/user/update/{id}', [ProfileController::class, 'update'])->name('update');
        Route::delete('/user/delete/{id}', [ProfileController::class, 'destroy'])->name('delete');

        Route::get('home/check-ftp', [FileUploadController::class, 'checkFtpConnection'])->name('check.ftp');
        Route::post('profile/upload', [FileUploadController::class, 'upload'])->name('file.upload');
        Route::get('profile/get', [FileUploadController::class, 'get'])->name('file.get');
        Route::get('profile/models', [FileUploadController::class, 'listModels'])->name('file.listModels');
        Route::delete('profile/models/delete/{id}', [FileUploadController::class, 'deleteModel'])->name('file.deleteModel');
        Route::get('profile/models/{id}/edit', [FileUploadController::class, 'editModel'])->name('file.editModel');
        Route::put('profile/models/{id}', [FileUploadController::class, 'updateModel'])->name('file.updateModel');
        Route::get('/admin/chats', [ChatController::class, 'index'])->name('chats.index');
        Route::get('/admin/chats/{chat_id}', [ChatController::class, 'show'])->name('chats.show');
        Route::post('/admin/chats/{chat_id}/send', [ChatController::class, 'sendMessage'])->name('chats.send');
        Route::get('/admin/chats/{chatId}/messages', [ChatController::class, 'getMessages'])->name('chats.getMessages');
        Route::get('/admin/chats/{chat_id}/get-new-messages', [ChatController::class, 'getNewMessages'])->name('chats.getNewMessages');
        Route::post('/admin/chats/{chat_id}/activate', [ChatController::class, 'activate'])->name('chats.activate');
        Route::post('/admin/chats/{chat_id}/deactivate', [ChatController::class, 'deactivate'])->name('chats.deactivate');
        Route::delete('/admin/chats/delete/{chat_id}', [ChatController::class, 'deleteChat'])->name('chats.delete');    
        Route::get('/admin/chats/get-updated-chats', [ChatController::class, 'getUpdatedChats'])->name('chats.getUpdatedChats');
        Route::get('/download-model/{id}', [FileUploadController::class, 'downloadModel'])->name('download.model');
        Route::get('/chat-stats', [MainContentController::class, 'getChatStats'])->name('chat.stats');
        Route::get('/logout', function() {
            Auth::logout();
            return redirect(route('user.login'));
        })->name('logout');
    });
});
