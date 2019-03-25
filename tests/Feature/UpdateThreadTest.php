<?php

namespace Tests\Feature;

use Symfony\Component\HttpFoundation\Response;
use Tests\DataBaseTestCase;

class UpdateThreadTest extends DataBaseTestCase
{
    protected function setUp()
    {
        parent::setUp();
        $this->signIn();
        $this->withExceptionHandling();
    }


    /**
     * @test
     */
    public function a_thread_require_a_title_and_a_body_to_be_update()
    {
        $thread = create('App\Thread', ['user_id' => auth()->id()]);
        $this->patch($thread->path(), [
            'title' => 'Updated title',
        ])->assertSessionHasErrors('body');

        $this->patch($thread->path(), [
            'body' => 'Updated body',
        ])->assertSessionHasErrors('title');

    }

    /**
     * @test
     */
    public function unauthorized_users_may_not_update_a_thread()
    {
        $thread = create('App\Thread', ['user_id' => create('App\User')]);
        $this->patch($thread->path(), [
            'title' => 'Updated title',
            'body' => 'Updated body',
        ])->assertStatus(Response::HTTP_FORBIDDEN);
        // Make sure that thread indeed not update
        $this->assertEquals($thread->title, $thread->fresh()->title);
        $this->assertEquals($thread->body, $thread->fresh()->body);
    }

    /**
     * @test
     */
    public function a_thread_can_be_updated_by_its_creator()
    {
        $thread = create('App\Thread', ['user_id' => auth()->id()]);
        $this->patch($thread->path(), [
            'title' => 'Updated title',
            'body' => 'Updated body',
        ]);
        $this->assertEquals('Updated title', $thread->fresh()->title);
        $this->assertEquals('Updated body', $thread->fresh()->body);
    }
}
