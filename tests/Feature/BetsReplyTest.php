<?php

namespace Tests\Feature;

use Symfony\Component\HttpFoundation\Response;
use Tests\DataBaseTestCase;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BetsReplyTest extends DataBaseTestCase
{
    /**
     * @test
     */
    public function a_thread_creator_may_mark_any_reply_as_the_best_reply()
    {
        $this->signIn();
        $thread = create('App\Thread', ['user_id'=>auth()->id()]);
        $replies = create('App\Reply', ['thread_id' => $thread->id], 2);
        $this->postJson(route('best-replies.store', $replies[1]->id));
        $this->assertTrue($replies[1]->fresh()->isBest());
    }

    /**
     * @test
     */
    public function only_the_thread_creator_may_mark_a_reply_as_best()
    {
        $this->withExceptionHandling();
        $this->signIn();
        $thread = create('App\Thread', ['user_id' => auth()->id()]);
        $replies = create('App\Reply', ['thread_id' => $thread->id], 2);
        $this->signIn(create('App\User')); // This is a new user which is not the owner of the thread
        $this->postJson(route('best-replies.store', $replies[1]->id))->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertFalse($replies[1]->fresh()->isBest());

    }
}
