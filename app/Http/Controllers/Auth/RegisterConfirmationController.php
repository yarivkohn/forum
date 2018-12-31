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
        try {
            $user = User::where('confirmation_token', request('token'))
                ->where('confirmed', false)
                ->firstOrFail();
            $user->confirm();
        } catch (ModelNotFoundException $ex) {
            return redirect(route('threads'))
                ->with('flash', 'Unknown token. Is it possible that you have already confirmed you account?');
        }

        auth()->login($user);
        return redirect(route('threads'))
            ->with('flash', 'Your account is now confirmed.');
    }
}
