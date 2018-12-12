<?php

namespace Tests\Feature;

use Tests\DataBaseTestCase;

class FavoriteTest extends DataBaseTestCase
{

    /**
     * @test
     */
    public function guests_can_noT_favorite_anything()
    {
        $this->withExceptionHandling();
        $this->post('/replies/1/favorites')
            ->assertRedirect('login');
    }
    /**
     * @test
     */
    public function an_authenticated_user_can_favoriute_any_reply()
    {
        $this->signIn();
        $reply = create('App\Reply');
        $this->post("/replies/{$reply->id}/favorites");
        $this->assertCount(1, $reply->favorites);
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
