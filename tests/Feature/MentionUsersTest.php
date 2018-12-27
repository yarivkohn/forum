<?php

namespace Tests\Feature;

use Tests\DataBaseTestCase;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MentionUsersTest extends DataBaseTestCase
{
    /**
     * @test
     */
    public function mentioned_users_in_a_reply_are_notified()
    {
        //Given I have a signed in user John doe
        $john = create('App\User',['name' => 'JohnDoe']);
        $this->signIn($john);
        // and another user Jane doe
        $jane = create('App\User',['name' => 'JaneDoe']);
        // if we have a thread
        $thread = create('App\Thread');
        //When John replies to that thread, and mention Jane doe
        $reply = create('App\Reply', [
            'body' => 'Hi Jane, I have just mentioned you in a reply @JaneDoe'
        ]);
        $this->json('post', $thread->path() . '/replies', $reply->toArray());
        //Then Jane should be notified
        $this->assertCount(1, $jane->notifications);
    }
}
