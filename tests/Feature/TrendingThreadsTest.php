<?php

namespace Tests\Feature;

use App\Trending;
use Illuminate\Support\Facades\Redis;
use Tests\DataBaseTestCase;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TrendingThreadsTest extends DataBaseTestCase
{

    protected $trending;

    protected function setUp()
    {
        parent::setUp();
        $this->trending = new Trending();
        $this->trending->reset();
    }

    /**
     * @test
     */
    public function it_increments_thread_score_each_time_it_read()
    {
        $this->assertEmpty($this->trending->get());
        $thread = create('App\Thread');
        $this->call('GET', $thread->path());
        $redisStack = $this->trending->get();
        $this->assertCount(1, $redisStack);
        $this->assertEquals($thread->title, $redisStack[0]->title);
    }
}