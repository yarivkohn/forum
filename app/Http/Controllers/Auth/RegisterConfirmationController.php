<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RegisterConfirmationController extends Controller
{
    public function index()
    {
        $user = User::where('confirmation_token', request('token'))->first();
//        $flashMessage = 'Unknown token.'; // Every request is guilty unless proven otherwise.
        if (!$user) {
            return redirect(route('threads'))
                ->with('flash', 'Unknown token.');
        }
        $user->confirm();
        auth()->login($user);
        return redirect(route('threads'))
            ->with('flash', 'Your account is now confirmed.');
    }
}
