<?php

namespace Tests\Feature;

use App\Mail\PleaseConfirmYourEmail;
use App\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Mail;
use Tests\DataBaseTestCase;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RegistrationTest extends DataBaseTestCase
{
    /**
     * @test
     */
    public function a_confirmation_email_is_sent_upon_registration()
    {
        Mail::fake();
        event(new Registered(create('App\User')));
        Mail::assertQueued(PleaseConfirmYourEmail::class);
    }

    /**
     * @test
     */
    public function users_can_fully_confirm_their_email_address()
    {
       $this->post(route('register'), [
           'name' => 'TestUser',
           'email' => 'tets.user@test.env.com',
           'password' => 'foobar_password',
           'password_confirmation' => 'foobar_password',
       ]);

       $user = User::where('name', 'TestUser')->first();
       $this->assertFalse($user->confirmed);
       $this->assertNotNull($user->confirmation_token);

       $response = $this->get(route('register.confirm', ['token' => $user->confirmation_token]));
       $this->assertTrue($user->fresh()->confirmed);
       $response->assertRedirect(route('threads'));
    }
}
