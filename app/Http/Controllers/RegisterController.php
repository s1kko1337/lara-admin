<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class RegisterController extends Controller
{
    public function save(Request $request){
    if (Auth::check()) {
        return redirect(route('user.home'));
    }

    $validateFields = $request->validate([
        'username' => 'required|min:6',
        'email' => 'required|email',
        'password' => 'required|min:8',
    ]);

    if(User::where('email', $validateFields['email'])->exists()){
        return redirect(route('user.login'))->withErrors([
            'email' => 'Такой пользователь уже зарегестрирован.'
        ]);
    }
    $user = User::create($validateFields);
    if($user){
        Auth::login($user);
        return redirect(route('user.home'));
    }
    
    return redirect(route('user.login'))->withErrors([
        'formError' => 'Ошибка при заполнении формы'
    ]);
    }
}
