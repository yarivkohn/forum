<?php

namespace Tests\Feature;

use Illuminate\Notifications\DatabaseNotification;
use Tests\DataBaseTestCase;

class NotificationsTest extends DataBaseTestCase
{

    public function setUp()
    {
        parent::SetUp();
        $this->signIn();
    }
    /**
     * @test
     */
    public function a_notification_is_prepared_when_a_subscribed_thread_received_a_new_reply_that_is_not_by_the_current_user()
   {
       $thread = create('App\Thread')->subscribe();
       $this->assertCount(0, auth()->user()->notifications);

        // Then each time a reply is left by the thread owner
        $thread->addReply([
            'user_id' => auth()->id(),
            'body' => 'A reply',
        ]);
        // No notification should be prepared for the subscriber
        $this->assertCount(0, auth()->user()->fresh()->notifications);

       // However, each time a reply is left by other user
       $thread->addReply([
           'user_id' => create('App\User')->id,
           'body' => 'A reply',
       ]);
       // A notification should be prepared for the subscriber
       $this->assertCount(1, auth()->user()->fresh()->notifications);
   }

    /**
     * @test
     */
    public function a_user_can_fetch_their_unread_notifications()
   {
       create(DatabaseNotification::class);
       $this->assertCount(
           1,
           $this->getJson("/profile/".auth()->user()->name."/notifications/")->json()
       );

   }

    /**
     * @test
     */
    public function a_user_can_mark_a_notification_as_read()
   {
       create(DatabaseNotification::class);
       tap(auth()->user(), function($user){
           $this->assertCount(1, $user->unreadNotifications);
           $this->delete("/profile/{$user->name}/notifications/".$user->unreadNotifications->first()->id);
           $this->assertCount(0, $user->fresh()->unreadNotifications);
       });
   }
}
