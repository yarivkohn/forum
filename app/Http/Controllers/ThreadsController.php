<?php

namespace App\Http\Controllers;

use App\Filters\ThreadFilter;
use App\Channel;
use App\Rules\Recaptcha;
use App\Thread;
use App\Trending;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ThreadsController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
    }

    /**
     * Display a listing of the resource.
     *
     * @param Channel $channel
     * @param ThreadFilter $filter
     * @param Trending $trending
     * @return \Illuminate\Http\Response
     */
    public function index(Channel $channel, ThreadFilter $filter, Trending $trending)
    {
        $threads = $this->getThreads($channel, $filter);
        if (request()->wantsJson()) {
            return $threads;
        }
        return view('threads.index', [
            'threads' => $threads,
            'trending' => $trending->get()
        ]); //->with(['threads' => $threads]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('threads.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Recaptcha $recaptcha
     * @return \Illuminate\Http\Response
     */
    public function store(Recaptcha $recaptcha)
    {
        request()->validate([
            'title' => 'required | spamfree',
            'body' => 'required | spamfree',
            'channel_id' => 'required|exists:channels,id',
            'g-recaptcha-response' => ['required', $recaptcha]
        ]);

        $thread = Thread::create([
            'user_id' => auth()->id(),
            'channel_id' => request('channel_id'),
            'title' => request('title'),
            'body' => request('body'),
        ]);

        if(request()->wantsJson()){
            return response($thread, Response::HTTP_CREATED);
        }
        return redirect($thread->path())
            ->with('flash', 'Your thread has been published');
    }

    /**
     * Display the specified resource.
     *
     * @param $channelId
     * @param  \App\Thread $thread
     * @param Trending $trending
     * @return \Illuminate\Http\Response
     */
    public function show($channelId, Thread $thread, Trending $trending)
    {
        return $thread;
        //Record that the subscriber has visited this link
        if(auth()->check()){
            auth()->user()->read($thread);
        }
        $trending->push($thread);
        $thread->increment('visits');
        return view('threads.show', [
            'thread' => $thread,
            'replies' => $thread->replies()->paginate(20),
        ]);
    }

    public function update($channel, Thread $thread)
    {
        $this->authorize('update', $thread);
        request()->validate([
            'title' => 'required | spamfree',
            'body' => 'required | spamfree',
        ]);

        $thread->update([
            'title' => request('title'),
            'body' => request('body'),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Thread $thread
     * @return \Illuminate\Http\Response
     */
    public function edit(Thread $thread)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Channel $channel
     * @param  \App\Thread $thread
     * @return array
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(Channel $channel, Thread $thread)
    {
//         if($thread->user_id != auth()->id()) {
//            return response(['status' => 'Permission denied'], Response::HTTP_UNAUTHORIZED);
//        }
        $this->authorize('update', $thread);
        try {
//            $thread->replies()->delete(); // This was replaced by the static deleting function at Thread.php
            $thread->delete();
        } catch (\Exception $e) {
            return response([
                'status' => 'failure',
                'message' => $e->getMessage(),
            ],
                Response::HTTP_UNAUTHORIZED);
        }
        return redirect('/threads/')
            ->with('flash', 'Your thread has been deleted.');
    }

    /**
     * @param Channel $channel
     * @param ThreadFilter $filter
     * @return mixed
     */
    private function getThreads(Channel $channel, ThreadFilter $filter)
    {
        $threads = Thread::latest()->filter($filter);
        if ($channel->exists) {
            $threads->where('channel_id', $channel->id);
        }
        return $threads->paginate(5);
    }
}
