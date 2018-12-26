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

    /**
     * @param $channelId
     * @param Thread $thread
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function store($channelId, Thread $thread)
    {
        $this->validateReply();
        $reply = $thread->addReply([
                'body' => request('body'),
                'user_id' => auth()->id()
            ]
        );
        if(request()->expectsJson()){
            return $reply->load('owner');
        }
        return back()
            ->with('flash', 'Your reply has been left.');
    }

    /**
     * @param Reply $reply
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Exception
     */
    public function update(Reply $reply)
    {
        $this->authorize('update', $reply);
        $this->validateReply();
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

    /**
     * @throws \Exception
     */
    private function validateReply()
    {
        $this->validate(request(), [
            'body' => 'required'
        ] );
        resolve(Spam::class)->detect(request('body'));
    }
}
