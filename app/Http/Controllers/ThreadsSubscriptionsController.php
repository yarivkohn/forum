<?php

namespace App\Http\Controllers;

use App\Thread;
use Illuminate\Http\Request;

class ThreadsSubscriptionsController extends Controller
{
    public function store($chanelId, Thread $thread)
    {
        $thread->subscribe();
    }
}
