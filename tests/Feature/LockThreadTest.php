<?php
/**
 * Created by PhpStorm.
 * User: yariv
 * Date: 1/1/19
 * Time: 6:11 PM
 */


namespace Tests\Feature;

use Symfony\Component\HttpFoundation\Response;
use Tests\DataBaseTestCase;

class LockThreadTest extends DataBaseTestCase
{

    /**
     * @test
     */
    public function a_non_administrator_may_not_lock_any_thread()
    {
        $this->withExceptionHandling();
        $this->signIn();
        $thread = create('App\Thread', ['user_id' => auth()->id()]);
        $this->post(route('locked-threads.store', $thread))
            ->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertFalse(!!$thread->fresh()->locked);
    }

    /**
     * @test
     */
    public function an_administrator_can_lock_any_thread()
    {
        $this->signIn();
        $thread = create('App\Thread', ['user_id' => auth()->id()]);
        $this->signIn(factory('App\User')->state('administrator')->create());
        $this->post(route('locked-threads.store', $thread))
            ->assertStatus(Response::HTTP_CREATED);
        $this->assertTrue(!!$thread->fresh()->locked);
    }

    /**
     * @test
     */
    public function once_locked_a_thread_may_not_receive_new_replies()
    {
        $this->signIn();
        $thread = create('App\Thread');
        $thread->lock();

        $this->post($thread->path() . '/replies', [
            'body' => 'This reply will not be saved',
            'user_id' => auth()->id(),
        ])->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}