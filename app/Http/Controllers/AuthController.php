<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    function create()
    {
        return view('auth.create');
    }

    /**
     * Register new user
     *
     * @param Request $request
     * @return void
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'first_name' => 'required',
            'last_name' => 'required',
            'password' => 'required'
        ]);

        $user = User::create([
            'email' => $request->email,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'password' => Hash::make($request->password),
            'role' => User::$role[3]
        ]);

        Auth::login($user);

        return redirect()->intended('/')->with('success', "Ви успішно зареєструвалися");
    }

    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Log in the user
     *
     * @param Request $request
     * @return void
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->filled('remember');

        if (Auth::attempt($credentials, $remember)) {
            return redirect()->intended('/')->with('success', 'Ви увійшли в аккаунт!');
        } else {
            return redirect()->back()->with('error', 'Неправильні вхідні дані!');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy()
    {
        Auth::logout();

        // Clearing out user session data
        request()->session()->invalidate();

        // Regenerating token for csrf forms
        request()->session()->regenerateToken();

        return redirect('/');
    }
}
