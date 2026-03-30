<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

<<<<<<< HEAD
        $response->assertRedirect(route('guest.home'));
=======
        $response->assertStatus(200);
>>>>>>> e2927c017d800ba2c0919a3f2a14f7de18623268
    }
}
