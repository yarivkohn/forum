<?php

namespace App\Http\Controllers;

use App\Thread;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LockedThreadsController extends Controller
{
    public function store(Thread $thread)
    {
//        if (!auth()->user()->isAdmin()) {
//            return response('You don\'t have permissions to lock this thread', Response::HTTP_FORBIDDEN);
//        }
        $thread->update([
            'locked' => true,
        ]);

        return response('', Response::HTTP_CREATED);
    }

    public function destroy(Thread $thread)
    {
        $thread->update([
            'locked' => false,
        ]);
        return response('', Response::HTTP_CREATED);
    }
}
