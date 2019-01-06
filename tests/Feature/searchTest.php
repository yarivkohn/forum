<?php

namespace Tests\Feature;

use Tests\DataBaseTestCase;
use Tests\TestCase;

class searchTest extends DataBaseTestCase
{
    /**
     * @test
     */
    public function a_user_can_search_threads()
    {
        $searchKeyword = 'foobar';
        $this->signIn();
        create('App\Thread', [], 2);
        create('App\Thread', [
            'body' => "A thread with the {$searchKeyword} them"
        ], 2);
        $results = $this->getJson("/threads/search?q={$searchKeyword}")->json();

        $this->assertCount(2, $results['data']);


    }
}
