<?php

namespace App\Http\Controllers;

use App\Reply;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BestRepliesController extends Controller
{
    public function store(Reply $reply)
    {
        $this->authorize('update', $reply->thread);// This can replace the abort_if function, and it relies upon policies
//        abort_if($reply->thread->user_id !== auth()->id(), Response::HTTP_FORBIDDEN);
        $reply->thread->markBestReply($reply);
    }
}
