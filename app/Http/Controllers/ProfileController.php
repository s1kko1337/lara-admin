<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\Portfolio;

class ProfileController extends Controller
{
    public function show(){
        $user = Auth::user();
        $users = User::all();

        return view('profile', compact('users'));
    }
    
    public function editName(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255',
        ]);

        $userId = Auth::id();

        DB::table('users')
            ->where('id', $userId)
            ->update(['username' => $request->username]);

        return redirect()->back()->with('success', 'Имя пользователя успешно обновлено.');
    }

    public function editEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $userId = Auth::id(); 

        DB::table('users')
            ->where('id', $userId)
            ->update(['email' => $request->email]);

            return redirect()->back()->with('success', 'Электронная почта пользователя успешно обновлена.');
    }

    public function add()
    {
    $users = User::all();
    return view('add', compact('users'));
    }

public function saveUser(Request $request){
    $validateFields = $request->validate([
        'username' => 'required|min:8',
        'email' => 'required|email',
        'password' => 'required|min:8',
    ]);

    if(User::where('email', $validateFields['email'])->exists()){
        return redirect()->back()->withErrors([
            'email' => 'Такой пользователь уже зарегистрирован.'
        ]);
    }

    $user = User::create($validateFields);

    return redirect()->route('user.profile')->with('success', 'Пользователь успешно добавлен');
}
        public function edit($id)
    {
        $user = User::findOrFail($id);
        $users = User::all();
        return view('edit', compact('user','users'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->update($request->all());
        return redirect()->route('user.profile')->with('success', 'Пользователь успешно обновлен');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return redirect()->route('user.profile')->with('success', 'Пользователь успешно удален');
    }


public function updatePassword(Request $request)
{
    // Валидация данных
    $request->validate([
        'current_password' => 'required',
        'new_password' => 'required|min:8|confirmed',
    ]);

    $user = Auth::user();

    // Проверка текущего пароля
    if (!Hash::check($request->current_password, $user->password)) {
        return back()->with('error', 'Текущий пароль введен неверно.');
    }

    // Обновление пароля
    $user->password = Hash::make($request->new_password);
    $user->save();

    return back()->with('success', 'Пароль успешно обновлен.');
}


public function editPortfolio()
{
    $userId = Auth::user()->id;
    
    // Получаем портфолио текущего пользователя
    $portfolio = Portfolio::where('id', $userId)->first();
    
    if (!$portfolio) {
        $portfolio = Portfolio::create([
            'id' => $userId, // Используем id пользователя в качестве id портфолио
            'main_info' => json_encode(['info' => '']), // Пустой JSON
            'additional_info' => json_encode(['info' => '']), // Пустой JSON
            'media_links' => json_encode(['artstation' => '', 'tg' => '', 'vk' => '', 'inst' => '']), // Пустой JSON
        ]);
    } else {
        $portfolio = Portfolio::where('id', $userId)->first();
    }
    
    return view('edit-portfolio', compact('portfolio'));
}


public function updatePortfolio(Request $request)
{
    $request->validate([
        'main_info' => 'required|array',
        'additional_info' => 'required|array',
        'media_links' => 'required|array',
    ]);

    // Находим портфолио пользователя и обновляем его
    $portfolio = Portfolio::where('id', Auth::user()->id)->firstOrFail();
    $portfolio->update($request->all());

    return redirect()->route('user.profile')->with('success', 'Портфолио обновлено успешно');
}


}


