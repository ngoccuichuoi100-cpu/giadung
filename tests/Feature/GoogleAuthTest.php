<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Mockery;
use Tests\TestCase;

class GoogleAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_google_redirect_route()
    {
        $response = $this->get('/auth/google');
        // Should redirect to Google accounts URL
        $response->assertStatus(302);
        $this->assertStringContainsString('accounts.google.com', $response->headers->get('Location'));
        $this->assertStringContainsString('509794401263-g7iivalbamcbcpqjd7abo3mes4rpdad0', $response->headers->get('Location'));
    }

    public function test_google_callback_logs_in_existing_user()
    {
        // Create user with matching email
        $existingUser = User::create([
            'username' => 'existinggoogle',
            'name' => 'Existing Customer',
            'email' => 'phamgina2906@gmail.com',
            'password' => bcrypt('password123'),
            'role' => 'customer',
            'status' => 'active',
        ]);

        // Mock Socialite
        $abstractUser = Mockery::mock('Laravel\Socialite\Two\User');
        $abstractUser->shouldReceive('getId')->andReturn('google-id-12345');
        $abstractUser->shouldReceive('getName')->andReturn('Phạm Gina');
        $abstractUser->shouldReceive('getEmail')->andReturn('phamgina2906@gmail.com');
        $abstractUser->shouldReceive('getAvatar')->andReturn('https://lh3.googleusercontent.com/avatar123');

        $provider = Mockery::mock('Laravel\Socialite\Two\GoogleProvider');
        $provider->shouldReceive('stateless')->andReturnSelf();
        $provider->shouldReceive('user')->andReturn($abstractUser);

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $response = $this->get('/auth/google/callback');

        // Should log in and redirect to home
        $response->assertRedirect(route('home'));
        $this->assertAuthenticatedAs($existingUser);

        // Verify google_id linked
        $this->assertDatabaseHas('users', [
            'id' => $existingUser->id,
            'google_id' => 'google-id-12345',
        ]);
    }
}
