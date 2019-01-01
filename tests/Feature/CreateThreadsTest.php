<?php

namespace Tests\Feature;

use App\Activity;
use App\Thread;
use Symfony\Component\HttpFoundation\Response;
use Tests\DataBaseTestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateThreadsTest extends DataBaseTestCase
{


    /**
     * @test
     */
    public function a_guest_may_not_crete_threads()
    {
        $this->withExceptionHandling();

        $this->get("/threads/create")
            ->assertRedirect(route('login'));

        $this->post(route('threads'))
            ->assertRedirect(route('login'));
    }

    /**
     * @test
     */
    public function new_users_must_first_confirm_their_email_address_before_creating_thread()
    {
        $user = factory('App\User')->state('unconfirmed')->create();
        $this->withExceptionHandling()->signIn($user);
        $thread = make('App\Thread');
        return $this->post(route('threads'), $thread->toArray())
            ->assertRedirect(route('threads'))
            ->assertSessionHas('flash');
    }


    /**
     * @test
     */
    public function an_authenticated_user_can_create_new_form_threads()
    {
//        $this->actingAs(factory('App\User')->create(['confirmed' => true]));
        $this->signIn();
        $thread = make('App\Thread');

        $response = $this->post(route('threads'), $thread->toArray());

        $this->get($response->headers->get('Location'))
            ->assertSee($thread->title)
            ->assertSee($thread->body);
    }

    /**
     * Test for required fields
     * @test
     */
    public function all_thread_data_should_be_valid()
    {
        $assertions = [
            'title' => null,
            'body' => null,
            'channel_id' => null,
        ];
        foreach ($assertions as $candidate => $value) {
            $this->publishThread([$candidate => $value])
                ->assertSessionHasErrors($candidate);
        }
    }

    /**
     * Test for non-exists ids
     * @test
     */
    public function a_thread_require_a_valid_channel()
    {
        factory('App\Channel', 2)->create();
        $this->publishThread(['channel_id' => 999])
            ->assertSessionHasErrors('channel_id');
    }

    /**
     * @test
     */
    public function a_thread_requires_a_unique_slug()
    {
        $this->signIn();

        $thread = create('App\Thread', [
            'title' => 'foo title',
            'slug' => 'foo-title',
        ]);
        $this->assertEquals($thread->fresh()->slug, 'foo-title');
        $this->post(route('threads'), $thread->toArray());
        $this->assertTrue(Thread::where('slug', 'foo-title-2')->exists());
    }

    /**
     * @test
     */
    public function a_thread_with_a_title_that_ends_in_a_number_should_generate_proper_slug()
    {
        $this->signIn();

        $thread = create('App\Thread', [
            'title' =>'foo title 24',
            'slug' => 'foo-title-24',
        ]);
        $this->assertEquals($thread->fresh()->slug, 'foo-title-24');

        $this->post(route('threads'), $thread->toArray());
        $this->assertTrue(Thread::where('slug', 'foo-title-24-2')->exists());
    }

    /**
     * @param array $overrides
     * @return \Illuminate\Foundation\Testing\TestResponse
     */
    public function publishThread($overrides = [])
    {
        $this->withExceptionHandling()->signIn();
        $thread = make('App\Thread', $overrides);
        return $this->post(route('threads'), $thread->toArray());
    }

    /**
     * @test
     */
    public function unauthorized_users_may_not_delete_threads()
    {
        $this->withExceptionHandling();
        $thread = create('App\Thread');

        //User is not signed in
        $this->json('DELETE', $thread->path())
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
        // User is signed in, However this is not his post

        $this->signIn();

        $this->json('DELETE', $thread->path())
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * @test
     */
    public function authorized_users_can_delete_thread()
    {
        $this->signIn();
        $thread = create('App\Thread', ['user_id' => auth()->id()]);
        $reply = create('App\Reply', ['thread_id' => $thread->id]);

        $response = $this->json('DELETE', $thread->path());
        $response->assertStatus(Response::HTTP_FOUND);

        $this->assertDatabaseMissing('threads', ['id' => $thread->id]);

        $this->assertDatabaseMissing('replies', ['id' => $reply->id]);

//        $this->assertDatabaseMissing('activities', [
//            'subject_id' => $thread->id,
//            'subject_type' => get_class($thread),
//        ]);
//
//        $this->assertDatabaseMissing('activities', [
//            'subject_id' => $reply->id,
//            'subject_type' => get_class($reply),
//        ]);

        $this->assertEquals(0, Activity::count());
    }

}
