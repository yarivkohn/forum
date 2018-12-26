<?php

namespace Tests\Unit;

use App\Spam;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SpamTest extends TestCase
{
    /**
     * @test
     */
    public function it_validates_spam()
    {
        $spam = new Spam();

        $this->assertFalse($spam->detect('Yahoo Customer Service'));
//        $this->assertTrue($spam->detect('Reply contains SPAM'));
    }
}