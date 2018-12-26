<?php

namespace Tests\Feature;

use App\Activity;
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
            ->assertRedirect('/login');

        $this->post('/threads')
            ->assertRedirect('/login');
    }


    /**
     * @test
     */
    public function an_authenticated_user_can_create_new_form_threads()
    {
        $this->actingAs(factory('App\User')->create());
        $thread = make('App\Thread');

        $response = $this->post('/threads/', $thread->toArray());

        $this->get($response->headers->get('Location'))
                ->assertSee($thread->title)
                ->assertSee($thread->body);
    }

//    /**
//     * @test
//     */
//    public function a_thread_require_a_title()
//    {
//        $this->publishThread(['title' => null])
//            ->assertSessionHasErrors('title');
//    }
//
//    /**
//     * @test
//     */
//    public function a_thread_require_a_body()
//    {
//        $this->publishThread(['body' => null])
//            ->assertSessionHasErrors('body');
//    }
//

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
        foreach($assertions as $candidate => $value){
            $this->publishThread([$candidate => $value])
                ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Test for non-exists ids
     * @test
     */
    public function a_thread_require_a_valid_channel()
    {
        factory('App\Channel',2)->create();
        $this->publishThread(['channel_id' => 999])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * @param array $overrides
     * @return \Illuminate\Foundation\Testing\TestResponse
     */
    public function publishThread($overrides = [])
    {
        $this->withExceptionHandling()->signIn();
        $thread = make('App\Thread', $overrides);
        return $this->json('/threads', $thread->toArray());
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

        $response  = $this->json('DELETE', $thread->path());
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
