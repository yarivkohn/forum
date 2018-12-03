<?php

namespace Tests\Feature;

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
                ->assertSessionHasErrors($candidate);
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
            ->assertSessionHasErrors('channel_id');
    }

    public function publishThread($overrides = [])
    {
        $this->withExceptionHandling()->signIn();
        $thread = make('App\Thread', $overrides);
        return $this->post('/threads', $thread->toArray());
    }

}
