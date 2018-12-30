<?php

namespace Tests\Unit;

use Tests\DataBaseTestCase;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends DataBaseTestCase
{
    /**
     * @test
     */
    public function a_user_can_fetch_their_most_recent_reply()
    {
        $user = create('App\User');
        $reply = create('App\Reply', ['user_id' => $user->id]);
        $this->assertEquals($reply->id, $user->lastReply->id);
    }

    /**
     * @test
     */
    public function a_user_can_determine_their_avatar_path()
    {
        $user = create('App\User');
        // If user does not have avatar return default image
        $this->assertEquals('images/default.png', $user->avatar());
        $user->avatar_path =  'avatars/me.jpg';
        $this->assertEquals('avatars/me.jpg', $user->avatar());
    }
}