<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test the root URL redirects to the boards index page.
     * This is the first step in the redirect chain.
     */
    public function test_the_application_root_redirects_to_boards(): void
    {
        $response = $this->get('/');

        $response->assertStatus(302);
        $response->assertRedirect(route('boards.index'));
    }

    /**
     * Test that guests trying to access a protected route are redirected to the login page.
     * This tests the authentication middleware.
     */
    public function test_guests_are_redirected_to_login(): void
    {
        // Try to access a protected route
        $response = $this->get(route('boards.index'));

        // Assert it redirects to the login page
        $response->assertStatus(302);
        $response->assertRedirect(route('login'));
    }
    
    /**
     * Test an authenticated user can access the boards page.
     */
    public function test_authenticated_user_can_access_boards(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('boards.index'));

        $response->assertStatus(200);
    }
}
