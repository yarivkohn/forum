<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RegisterConfirmationController extends Controller
{
    public function index()
    {
        $user = User::where('confirmation_token', request('token'))
            ->firstOrFail();
        $user->confirm();
//            ->update(['confirmed'=> true]);
        auth()->login($user);
        return redirect('/threads')
            ->with('flash', 'Your account is now confirmed.');
    }
}
