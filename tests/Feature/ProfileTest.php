<?php

namespace Tests\Feature;

use Tests\DataBaseTestCase;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProfileTest extends DataBaseTestCase
{
    /**
     * @test
     */
    public function a_user_has_a_profile()
    {
        $user = create('App\User');

        $this->get("/profile/{$user->name}")
            ->assertSee($user->name);

    }

    /**
     * @test
     */
    public function profiles_display_all_threads_created_by_the_associated_user()
    {
        $user = create('App\User');
        $thread = create('App\Thread', ['user_id' => $user->id]);

        $this->get("/profile/{$user->name}")
            ->assertSee($thread->title)
            ->assertSee($thread->body);

    }
}
