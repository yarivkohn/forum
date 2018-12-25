<?php

namespace Tests\Feature;

use Tests\DataBaseTestCase;

class FavoriteTest extends DataBaseTestCase
{

    /**
     * @test
     */
    public function guests_can_not_favorite_anything()
    {
        $this->withExceptionHandling();
        $this->post('/replies/1/favorites')
            ->assertRedirect('login');
    }

    /**
     * @test
     */
    public function an_authenticated_user_can_favorite_any_reply()
    {
        $this->signIn();
        $reply = create('App\Reply');
        $this->post("/replies/{$reply->id}/favorites");
        $this->assertCount(1, $reply->favorites);
    }

    /**
     * @test
     */
    public function an_authenticated_user_can_un_favorite_a_reply()
    {
        $this->signIn();
        $reply = create('App\Reply');

        // We can use $reply->favorite() instead of the following 2 rows.
        // We know this works due to other tests that we run.
        // If we use this approach than $reply-favorites in not being calculated here, and no use of fresh() is due on the following assertion.
        $this->post("/replies/{$reply->id}/favorites");
        $this->assertCount(1, $reply->favorites);

        $this->delete("/replies/{$reply->id}/favorites");
        // We need to add the fresh function in order to reset the reply and reload its attributes
        // w/o the refresh function thr $reply->favorites is already calculated, hence equal to 1 and the test will fail.
        $this->assertCount(0, $reply->fresh()->favorites);
    }

    /**
     * @test
     */
    public function an_authenticated_user_may_only_favorite_a_reply_once()
    {
        $this->signIn();
        $reply = create('App\Reply');
        try{
            $this->post("/replies/{$reply->id}/favorites");
            //Try to favorite the same reply again
            $this->post("/replies/{$reply->id}/favorites");
        } catch( \Exception $ex) {
            $this->fail('Can\'t favorite the same reply more than once');
        }
        $this->assertCount(1, $reply->favorites);
    }
}
