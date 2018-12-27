<?php

namespace App\Http\Controllers;

use App\Channel;
use App\Http\Form\CreatePostForm;
use App\Notifications\YouWereMwntioned;
use App\Reply;
use App\Inspections\Spam;
use App\Thread;
use App\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
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
        return $thread->replies()->paginate(15);
    }

    /**
     * @param $channelId
     * @param Thread $thread
     * @param CreatePostForm $form
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Http\RedirectResponse
     */
    public function store($channelId, Thread $thread, CreatePostForm $form)
    {
//        This Gate check was replaced with the CreateFormRequest@authorize

//        if(Gate::denies('create', new Reply)) {
//            return response(
//                'You are posting too frequently. Please take a break :)', Response::HTTP_UNPROCESSABLE_ENTITY
//            );
//        }

            $this->authorize('create', new Reply);
            $reply = $thread->addReply([
                    'body' => request('body'),
                    'user_id' => auth()->id()
                ]
            );
            // Inspect the reply body for mentioned users
            // And the FOREACH mentioned user - notify them.
            preg_match_all( '/\@([^\s\.]+)/', $reply->body, $matches);
            $mentionedNames = $matches[1];
            foreach($mentionedNames as $name){
                $user = User::where('name', $name)->first();
                if($user) {
                    $user->notify(new  YouWereMwntioned($reply));
                }
            }
            return $reply->load('owner');

// This catch block was replace with the Gate facade within this function
//        }
// catch (AuthorizationException $ex) {
//            return response(
//                'You are posting too frequently. Please take a break :)', Response::HTTP_UNPROCESSABLE_ENTITY
//            );
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
