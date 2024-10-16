<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class LoginController extends Controller
{
    public function login(Request $request){
        if (Auth::check()) {
            return redirect(route('user.home'));
        }
        $formFields = $request->only(['email', 'password']);

        if(Auth::attempt($formFields)){
            return redirect()->intended(route('user.home'));
        } else {
            if ($request->has('password')) {
                return redirect(route('user.login'))->withErrors([
                    'password' => 'Пароль неверен, попробуйте еще раз'
                ]);
            } else {
                return redirect(route('user.login'))->withErrors([
                    'email' => 'Пользователя с заданным email не существует'
                ]);
            }
        }
    }
}
