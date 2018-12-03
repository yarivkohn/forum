<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ParticipateInForumTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @test
     */
    public function unauthorized_users_may_not_add_replies()
    {
        $this->withExceptionHandling()
            ->post('threads/bla-b la/1/replies', [])
            ->assertRedirect('/login/');
    }

    /**
     * @test
     */
    public function  an_authenticated_user_may_participate_in_forum_threads()
    {
        //Given we have an authenticated user
        $this->signIn($user = factory('App\User')->create());
        // And an existing Thread
        $thread = factory('App\Thread')->create();
        //When a user adds a reply to the threads
        $reply = factory('App\Reply')->make();
        $this->post($thread->path().'/replies', $reply->toArray());
        $this->get($thread->path())
            ->assertSee($reply->body);

    }

    /**
     * @test
     */
    public function a_reply_requires_a_body()
    {
        $this->withExceptionHandling()->signIn();
        $thread = create('App\Thread');
        $reply = make('App\Reply',['body' => null]);
        $this->post($thread->path().'/replies', $reply->toArray())
            ->assertSessionHasErrors('body');
    }
}
