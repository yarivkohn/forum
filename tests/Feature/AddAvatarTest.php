<?php

namespace Tests\Feature;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use Tests\DataBaseTestCase;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AddAvatarTest extends DataBaseTestCase
{
    /**
     * @test
     */
    public function only_members_can_add_avatars()
   {
       $this->withExceptionHandling();
        $response = $this->json('POST', 'api/users/1/avatar')
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
   }

    /**
     * @test
     */
    public function a_valid_avatar_must_be_supplied()
    {
        $this->withExceptionHandling()
        ->signIn();

        $this->json('POST',
            'api/users/'.auth()->id().'/avatar',
            ['avatar' => 'not-an-image']
        )
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $this->json('POST',
            'api/users/'.auth()->id().'/avatar'
        )
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * @test
     */
    public function a_user_may_add_avatar_to_their_profile()
    {
        $this->signIn();
        Storage::fake('public');
        $avatarFile = UploadedFile::fake()->image('avatar.jpg');
        $this->json('POST',
            'api/users/'.auth()->id().'/avatar',
            ['avatar' => $avatarFile]
        );
        $this->assertEquals('avatars/'.$avatarFile->hashName(), auth()->user()->avatar_path);
        Storage::disk('public')->assertExists('avatars/'. $avatarFile->hashName());
    }
}
