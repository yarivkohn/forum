<?php

namespace Tests\Unit;

use App\Reply;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ReplyTest extends TestCase
{

    use DatabaseMigrations;

     /**
     * @test
     */
    public function it_has_an_owner()
    {
        $reply = factory('App\Reply')->create();
        $this->assertInstanceOf('App\User', $reply->owner);
    }

    /**
     * @test
     */
    public function it_knows_if_it_was_just_published()
    {
        $reply = create('App\Reply');
        $this->assertTrue($reply->wasJustPublished());
        $reply->created_at = Carbon::now()->subMOnth();
        $this->assertFalse($reply->wasJustPublished());
    }

    /**
     * @test
     */
    public function it_knows_if_it_is_the_best_reply()
    {
        $reply = create('App\Reply');
        $this->assertFalse($reply->isBest());
        $reply->thread->update(['best_reply_id' => $reply->id]);
        $this->assertTrue($reply->fresh()->isBest());
    }

    /**
     * @test
     */
    public function a_reply_sanitize_automatically()
    {
        $thread = make('App\Reply', [
            'body' => '<p>Something malicious like <scrip>alert("bad")</script></scrip></p>'
        ]);

        $this->assertEquals('<p>Something malicious like alert("bad")</p>', $thread->body);
    }
}
