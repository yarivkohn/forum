<?php

namespace App\Http\Controllers;

use App\Thread;
use Illuminate\Http\Request;

class ThreadsSubscriptionsController extends Controller
{
    /**
     * Given a Thread, subscribe the current Logged in user to that Thread
     * @param $chanelId
     * @param Thread $thread
     */
    public function store($chanelId, Thread $thread)
    {
        $thread->subscribe();
    }

    /**
     * Given a Thread, unsubscribed the current signed in user from it
     * @param $chanelId
     * @param Thread $thread
     */
    public function destroy($chanelId, Thread $thread)
    {
        $thread->unsubscribe();
    }
}
