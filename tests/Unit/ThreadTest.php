<?php

namespace Tests\Unit;

use App\Notifications\ThreadWasUpdated;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ThreadTest extends TestCase
{

    use DatabaseMigrations;

    private $thread;

    public function setUp()
    {
        parent::setUp();
        $this->thread = $thread = factory('App\Thread')->create();
    }

    /**
     * @test
     */
    public function a_thread_can_have_replies()
    {
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $this->thread->replies);
    }

    /**
     * @test
     */
    public function a_thread_has_a_path()
    {
        $this->assertEquals(
            "/threads/{$this->thread->channel->slug}/{$this->thread->slug}",
            $this->thread->path()
        );
    }

    /**
     * @test
     */
    public function a_thread_has_an_creator()
    {
        $this->assertInstanceOf('App\User', $this->thread->creator);

    }

    /**
     * @test
     */
    public function a_thread_can_add_a_reply()
    {
        $this->thread->addReply([
            'body' =>'FooBar',
            'user_id' => 1
        ]);
        $this->assertCount(1, $this->thread->replies );
    }

    /**
     * @test
     */
    public function a_thread_notify_all_registered_subscribers_when_a_reply_is_added()
    {
        Notification::fake( );
        $this->signIn();
        $this->thread->subscribe()
        ->addReply([
            'body' =>'FooBar',
            'user_id' => 1
        ]);
        Notification::assertSentTo(auth()->user(), ThreadWasUpdated::class);

    }

    /**
     * @test
     */
    public function a_thread_belongs_to_a_channel()
    {
        $this->assertInstanceOf('App\Channel', $this->thread->channel);
    }

    /**
     * @test
     */
    public function a_thread_can_be_subscribed_to()
    {
        //Given we have a thread
        $thread = create('App\Thread');
        //And authenticated user
        $this->signIn();
        //When the user subscribe to a thread
        $thread->subscribe();
        //Then we should be able to fetch all threads that the user has subscribed to

        $this->assertEquals(
            1,
            $thread->subscriptions()->where(['user_id'=> auth()->id()])->count()
        );
    }

    /**
     * @test
     */
    public function a_thread_can_be_unsubscribed_from()
    {
        //Given we have a thread
        $thread = create('App\Thread');
        //And authenticated user that has subscribed to a thread
        $this->signIn();
        $thread->subscribe();
        //When the user unsubscribed from the thread
        $thread->unsubscribe();

        //Then we shouldn't be fetch subscription for that thread and user
        $this->assertEquals(
            0,
            $thread->subscriptions()->where(['user_id'=> auth()->id()])->count()
        );
    }

    /**
     * @test
     */
    public function it_knows_if_the_authenticated_user_is_subscribed_to_it()
    {
        //Given we have a thread
        $thread = create('App\Thread');
        //And authenticated user that has subscribed to a thread
        $this->signIn();

        $this->assertFalse($thread->isSubscribedTo);
        $thread->subscribe();
        $this->assertTrue($thread->isSubscribedTo);

    }

    /**
     * @test
     */
    public function a_thread_can_check_if_subscriber_has_read_all_replies()
    {
        $this->signIn();
        $thread = create('App\Thread');
        tap(auth()->user(), function($user) use($thread){
            $this->assertTrue($thread->hasUpdatesFor($user));
            $user->read($thread);
            $this->assertFalse($thread->hasUpdatesFor($user));
        });

    }

//    /**
//     * @test
//     */
//    public function a_thread_records_each_visit()
//    {
//        $thread = make('App\Thread', ['id' => 1]);
//        $thread->visits()->reset();
//        $this->assertSame(0, $thread->visits()->count());
//        $thread->visits()->record();
//        $this->assertEquals(1, $thread->visits()->count());
//        $thread->visits()->record();
//        $this->assertEquals(2, $thread->visits()->count());
//    }


}
