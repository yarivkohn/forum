<?php

namespace Tests\Feature;

use App\Thread;
use Tests\DataBaseTestCase;
use Tests\TestCase;

class searchTest extends DataBaseTestCase
{
    /**
     * @test
     */
    public function a_user_can_search_threads()
    {
        config(['scout.driver' => 'algolia']);
        $searchKeyword = 'foobar';
        $this->signIn();
        create('App\Thread', [], 2);
        $desiredThreads = create('App\Thread', [
            'body' => "A thread with the {$searchKeyword} them"
        ], 2);
        do {
            sleep(.25);
            $results = $this->getJson("/threads/search?q={$searchKeyword}")->json()['data'];
        } while(empty($results));

        $this->assertCount(2, $results);
//        $desiredThreads->unSearchable();
        Thread::latest()->take(4)->unsearchable();
    }
}
