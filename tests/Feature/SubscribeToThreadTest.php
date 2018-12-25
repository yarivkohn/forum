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

        // Then each time a reply is left...
        $thread->addReply([
            'user_id' => auth()->id(),
            'body' => 'A reply',
        ]);
        // A notification should be prepared for the subscriber
//        $this->assertEquals(1, auth()->user()->notifications);
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
