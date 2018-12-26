<?php

namespace Tests\Unit;

use App\Inspections\Spam;
use App\Inspections\SpamDetectedException;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SpamTest extends TestCase
{
    /**
     * @test
     */
    public function it_checks_for_invalid_keywords()
    {
        $spam = new Spam();

        $this->assertFalse($spam->detect('An innocent reply'));
    }

    /**
     * @test
     */
    public function it_checks_for_a_key_being_hold()
    {
       $spam = new Spam();

       $this->expectException(SpamDetectedException::class);

       $spam->detect('Hello world aaaaaaaa');
    }


}