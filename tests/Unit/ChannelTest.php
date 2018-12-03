<?php

namespace Tests\Unit;

use Tests\DataBaseTestCase;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ChannelTest extends DataBaseTestCase
{

    /**
     * @test
     */
    public function a_channel_consist_of_threads()
    {
        $channel = create('App\Channel');
        $thread = create('App\Thread', ['channel_id' => $channel->id]);
        $this->assertTrue($channel->threads->contains($thread));
    }
}
