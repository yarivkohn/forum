<?php

namespace App\Http\Controllers;

use App\Channel;
use App\Reply;
use App\Inspections\Spam;
use App\Thread;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
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
        try {
            $this->validate(request(), [
                'body' => 'required|spamfree'
            ]);
            $reply = $thread->addReply([
                    'body' => request('body'),
                    'user_id' => auth()->id()
                ]
            );
            return $reply->load('owner');
        } catch (\Exception $ex) {
            return response(
                'Sorry, yor reply could not be saved at this time.', Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
    }

    /**
     * @param Reply $reply
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws \Exception
     */
    public function update(Reply $reply)
    {
        try {
            $this->authorize('update', $reply);
            $this->validate(request(), [
                'body' => 'required|spamfree'
            ]);
            $reply->update([
                'body' => request('body'),
            ]);
        } catch (ValidationException $ex) {
            return response(
                'Sorry, yor reply could not be saved at this time.', Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        return response('Your reply has been updated', Response::HTTP_OK);
    }

    public function destroy(Reply $reply)
    {
//        if($reply->user_id != auth()->id()) {
//            return response([], Response::HTTP_FORBIDDEN);
//        }

        $this->authorize('update', $reply);
        $reply->delete();
        if (request()->expectsJson()) {
            return response(['status' => 'Reply deleted']);
        }
        return back();
    }
}
