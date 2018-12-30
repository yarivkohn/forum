<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Redis;
use Tests\DataBaseTestCase;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TrendingThreadsTest extends DataBaseTestCase
{

    protected function setUp()
    {
        parent::setUp();
        Redis::del('trending_threads');
    }

    /**
     * @test
     */
    public function it_increments_thread_score_each_time_it_read()
    {
        $this->assertEmpty(Redis::zrevrange('trending_threads', 0, -1));
        $thread = create('App\Thread');
        $this->call('GET', $thread->path());
        $redisStack = Redis::zrevrange('trending_threads', 0, -1);
        $this->assertCount(1, $redisStack);
        $this->assertEquals($thread->title, (json_decode($redisStack[0]))->title);
    }
}