<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Socialite;
use App\User;

class SocialController extends Controller
{
    public function redirect($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function Callback($provider)
    {
        try {
            $userSocial =   Socialite::driver($provider)->stateless()->user();
        } catch (\Exception $e) {

            return redirect()->route('login');
        }

        $users = User::where(['email' => $userSocial->getEmail()])->first();

        if ($users) {
            auth()->login($users, true);
            return redirect('home');
        } else {
            $newUser                    = new User;
            $newUser->provider_name     = $provider;
            $newUser->provider_id       = $userSocial->getId();
            $newUser->name              = $userSocial->getName();
            $newUser->email             = $userSocial->getEmail();
            $newUser->email_verified_at = now();
            $newUser->avatar            = $userSocial->getAvatar();
            $newUser->save();

            auth()->login($newUser, true);
        }

        return redirect()->route('home');
    }
}
