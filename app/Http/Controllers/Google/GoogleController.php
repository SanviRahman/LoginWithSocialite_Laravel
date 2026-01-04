<?php

namespace App\Http\Controllers\Google;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Http\Request;
use Auth;

class GoogleController extends Controller
{
    
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        $user = Socialite::driver('google')->user();
        $finder = User::where('google_id', $user->id)->first();

        if($finder)
        {
            Auth::login($finder);
            return redirect()->intended('user.dashboard');
        }else{
            $newUser = User::create([
                'name' => $user->name,
                'email' => $user->email,
                'google_id' => $user->id,
                'password' => bcrypt('123456dummy')
            ]);
            Auth::login($newUser);
            return redirect()->intended('user.dashboard');
        }
    }
}
