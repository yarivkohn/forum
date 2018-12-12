<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReadThreadsTest extends TestCase
{

    use DatabaseMigrations;

    private $thread;

    public function setUp()
    {
        parent::setUP();
        $this->thread = factory('App\Thread')->create();
    }

    /**
     * @test
     */
    public function a_user_can_view_all_threads()
    {
        $response = $this->get('/threads');
        $response->assertSee($this->thread->title);
    }

    /**
     * @test
     */
    public function a_user_can_read_a_single_thread()
    {
        $response = $this->get($this->thread->path());
        $response->assertSee($this->thread->title);
    }

    /**
     * @test
     */
    public function  a_user_can_read_a_replay_that_is_associated_with_a_thread()
    {
        $reply = factory('App\Reply')->create(['thread_id' => $this->thread->id]);
        $response = $this->get($this->thread->path());
        $response->assertSee($reply->body);
    }

    /**
     * @test
     */
    public function a_user_can_filter_threads_according_to_a_tag()
    {
        $channel = create('App\Channel');
        $threadInChannel = create('App\Thread', ['channel_id' => $channel->id]);
        $threadNotInChannel = create('App\Thread');

        $this->get('/threads/'.$channel->slug)
                ->assertSee($threadInChannel->title)
                ->assertDontSee($threadNotInChannel->title);
    }

    /**
     * @test
     */
    public function a_user_can_filter_threads_by_any_username()
    {
        $this->signIn(create('App\User', ['name'=> 'JohnDoe']));

        $threadByJohnDoe = create('App\Thread',['user_id' => auth()->id()]);
        $threadNotByJohnDoe = create('App\Thread');

         $this->get('threads?by=JohnDoe')
            ->assertSee($threadByJohnDoe->title)
            ->assertDontSee($threadNotByJohnDoe->title);
    }

    /**
     * @test
     */
    public function a_user_can_filter_threads_by_popularity()
    {
        $threadWIthTwoReplies = create('App\Thread');
        create('App\Reply', ['thread_id' =>$threadWIthTwoReplies->id], 2);

        $threadWIthThreeReplies = create('App\Thread');
        create('App\Reply', ['thread_id' =>$threadWIthThreeReplies->id], 3);

        $threadWithNoReplies = $this->thread;

        $response = $this->getJson('/threads?popular=1')->json();

        $this->assertEquals([3,2,0] ,array_column($response, 'replies_count'));
    }
}
