<?php

namespace App\Http\Controllers;

use App\Channel;
use App\Reply;
use App\Inspections\Spam;
use App\Thread;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RepliesController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth')->except('index');
    }

    public function index(Channel $channel, Thread $thread)
    {
        return $thread->replies()->paginate(20);
    }

    public function store($channelId, Thread $thread, Spam $spam)
    {
        $this->validate(request(), [
            'body' => 'required'
        ] );
        $reply = $thread->addReply([
                'body' => request('body'),
                'user_id' => auth()->id()
            ]
        );

        $spam->detect(request('body'));

//        if(stripos(request('body'), 'Yahoo Customer support') !== false ) {
//            throw new \Exception('Spam alert');
//        }

        if(request()->expectsJson()){
            return $reply->load('owner');
        }
        return back()
            ->with('flash', 'Your reply has been left.');
    }

    public function update(Reply $reply)
    {
        $this->authorize('update', $reply);
        $reply->update([
            'body' => request('body'),
        ]);
    }

    public function destroy(Reply $reply )
    {
//        if($reply->user_id != auth()->id()) {
//            return response([], Response::HTTP_FORBIDDEN);
//        }

        $this->authorize('update', $reply);
        $reply->delete();
        if(request()->expectsJson()) {
            return response(['status' => 'Reply deleted']);
        }
        return back();
    }
}
