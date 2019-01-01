<?php
/**
 * Created by PhpStorm.
 * User: yariv
 * Date: 1/1/19
 * Time: 6:11 PM
 */


namespace Tests\Feature;

use Symfony\Component\HttpFoundation\Response;
use Tests\DataBaseTestCase;

class LockThreadTest extends DataBaseTestCase
{
    /**
     * @test
     */
    public function an_administrator_can_lock_any_thread()
    {
        $this->signIn();
        $thread = create('App\Thread');
        $thread->lock();

        $this->post($thread->path().'/replies', [
            'body' => 'This reply will not be saved',
            'user_id' => auth()->id(),
        ])->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}