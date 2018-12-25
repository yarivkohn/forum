<?php

namespace Tests\Feature;

use Tests\DataBaseTestCase;

class SubscribeToThreadTest extends DataBaseTestCase
{
    /**
     * @test
     */
    public function a_user_can_subscribe_to_thread()
    {
        $this->signIn();
        // Given we have thread
        $thread = create('App\Thread');
        // And a user subscribe to that thread
        $this->post($thread->path(). '/subscription');
        $this->assertCount(1, $thread->fresh()->subscriptions);

    }

    /**
     * @test
     */
    public function a_user_can_unsubscribe_form_a_thread()
    {
        $this->signIn();
        // Given we have thread
        $thread = create('App\Thread');
        // And a user subscribe to that thread
        $thread->subscribe();

        $this->delete($thread->path(). '/subscription');
        $this->assertCount(0, $thread->subscriptions);

    }
}
