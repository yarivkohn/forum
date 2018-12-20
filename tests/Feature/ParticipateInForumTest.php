<?php

namespace Tests\Feature;

use App\Http\Controllers\ProfilesController;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Symfony\Component\HttpFoundation\Response;
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

        $this->assertDatabaseHas('replies', ['body' => $reply->body]);

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

    /**
     * @test
     */
    public function unauthorized_users_can_not_delete_replies()
    {
        $this->withExceptionHandling();

        $reply = create('App\Reply');

        $this->delete("/replies/{$reply->id}")
            ->assertRedirect('login');

        $this->signIn()
            ->delete("/replies/{$reply->id}")
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * @test
     */
    public function authorized_users_can_delete_reply()
    {
        $this->signIn();
        $reply = create('App\Reply', ['user_id' => auth()->id()]);
        $this->delete("/replies/{$reply->id}")
            ->assertStatus(Response::HTTP_FOUND);
        $this->assertDatabaseMissing('replies', ['id' => $reply->id]);

    }


    /**
     * @test
     */
    public function unauthorized_users_can_not_update_replies()
    {
        $this->withExceptionHandling();

        $reply = create('App\Reply');

        $this->patch("/replies/{$reply->id}", ['body' => 'This is not going to happen'])
            ->assertRedirect('login');

        $this->signIn()
            ->patch("/replies/{$reply->id}")
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }


    /**
     * @test
     */
    public function authorized_users_can_update_replies()
    {
        $updateBodyText = 'New updated reply body';
        $this->signIn();
        $reply = create('App\Reply', ['user_id' => auth()->id()]);
        $this->patch("/replies/{$reply->id}", ['body' => $updateBodyText]);
        $this->assertDatabaseHas('replies', ['id' => $reply->id, 'body' => $updateBodyText]);
    }
}
