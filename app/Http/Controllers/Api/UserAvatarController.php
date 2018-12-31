<?php

namespace App\Http\Controllers\Api;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Response;

class UserAvatarController extends Controller
{
    public function __construct()
    {
//        $this->middleware('auth');   // Since this is a weird logic to hit the controller and then get back to a middleware we moved the middleware to the route level
    }

    public function store()
    {
        $this->validate(request(), [
            'avatar' => 'required|image',
        ]);
        auth()->user()->update([
            'avatar_path' => request()->file('avatar')->store('avatars', 'public'),
        ]);
        return response([], Response::HTTP_NO_CONTENT);
    }
}
