<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Exceptions\LoginException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Render login view
     *
     * @return void
     */
    public function index()
    {
        return inertia()->render('Login');
    }

    /**
     * Attempt user login
     *
     * @param Request $request
     * @return void
     */
    public function login(Request $request)
    {
        try {
            $request->validate([
                'username' => 'required',
                'password' => 'required'
            ]);
    
            $user = User::where('username', $request->username)
                ->orWhere('email', $request->username)
                ->first();
    
            if (!$user || !Hash::check($request->password, $user->password)) {
                throw new LoginException();
            }
    
            Auth::login($user);
    
            return redirect()->intended(route('home'));
        } catch (LoginException $e) {
            throw ValidationException::withMessages([
                'auth' => 'Invalid credentials.'
            ]);
        }
    }

    /**
     * Attempt user login for mobile app
     *
     * @return void
     */
    public function appLogin(Request $request)
    {
        try {
            $request->validate([
                'username' => 'required',
                'password' => 'required',
                'device_name' => 'required',
            ]);
    
            $user = User::where('username', $request->username)
                ->orWhere('email', $request->username)
                ->first();
    
            if (!$user || !Hash::check($request->password, $user->password)) {
                throw new LoginException();
            }
    
            return $user->createToken($request->device_name)->plainTextToken;
        } catch (LoginException $e) {
            throw ValidationException::withMessages([
                'auth' => 'Invalid credentials.'
            ]);
        }
    }
}
