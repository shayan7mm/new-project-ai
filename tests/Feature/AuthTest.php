<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_creates_normal_user_even_if_is_admin_is_sent(): void
    {
        $this->post('/register', [
            'name' => 'Test',
            'email' => 'test@example.com',
            'password' => 'Secret123',
            'password_confirmation' => 'Secret123',
            'is_admin' => 1,
        ])->assertRedirect(route('home'));

        $user = User::firstWhere('email', 'test@example.com');
        $this->assertNotNull($user);
        $this->assertFalse($user->is_admin);
        $this->assertNotSame('Secret123', $user->password);
        $this->assertAuthenticatedAs($user);
    }

    public function test_register_rejects_weak_password(): void
    {
        $this->post('/register', [
            'name' => 'Test',
            'email' => 'weak@example.com',
            'password' => '12345678',
            'password_confirmation' => '12345678',
        ])->assertSessionHasErrors('password');

        $this->assertGuest();
    }

    public function test_admin_login_redirects_to_panel(): void
    {
        $admin = User::factory()->admin()->create(['password' => 'Secret123']);

        $this->post('/login', ['email' => $admin->email, 'password' => 'Secret123'])
            ->assertRedirect(route('admin.products.index'));
    }

    public function test_wrong_password_fails_and_is_rate_limited(): void
    {
        $user = User::factory()->create(['password' => 'Secret123']);

        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', ['email' => $user->email, 'password' => 'wrong'])->assertSessionHasErrors('email');
        }

        // حتی با رمز درست، تا پایان زمان قفل اجازه ورود نیست
        $this->post('/login', ['email' => $user->email, 'password' => 'Secret123'])->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_logout(): void
    {
        $this->actingAs(User::factory()->create())->post('/logout')->assertRedirect(route('home'));
        $this->assertGuest();
    }
}
