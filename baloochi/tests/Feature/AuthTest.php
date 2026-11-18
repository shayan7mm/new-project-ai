<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_is_disabled(): void
    {
        $this->get('/register')->assertNotFound();
        $this->post('/register', ['name' => 'x', 'email' => 'x@example.com', 'password' => 'Secret123'])->assertNotFound();
        $this->assertDatabaseCount('users', 0);
    }

    public function test_non_admin_account_cannot_log_in(): void
    {
        $user = User::factory()->create(['password' => 'Secret123']);

        $this->post('/login', ['email' => $user->email, 'password' => 'Secret123'])->assertSessionHasErrors('email');
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
        $user = User::factory()->admin()->create(['password' => 'Secret123']);

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
