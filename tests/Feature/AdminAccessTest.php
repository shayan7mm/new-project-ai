<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/admin/products')->assertRedirect(route('login'));
    }

    public function test_normal_user_gets_403_on_every_admin_page(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $this->actingAs($user);
        $this->get('/admin/products')->assertForbidden();
        $this->get('/admin/categories')->assertForbidden();
        $this->get('/admin/settings')->assertForbidden();
        $this->get('/admin/users')->assertForbidden();
        $this->get('/admin/export')->assertForbidden();
        $this->delete("/admin/products/{$product->id}")->assertForbidden();

        $this->assertDatabaseHas('products', ['id' => $product->id]);
    }

    public function test_admin_can_open_all_pages(): void
    {
        $product = Product::factory()->create();
        $this->actingAs(User::factory()->admin()->create());

        foreach (['/admin/products', '/admin/products/create', "/admin/products/{$product->id}/edit", '/admin/categories', '/admin/categories/create', '/admin/settings', '/admin/users', '/admin/help'] as $url) {
            $this->get($url)->assertOk();
        }
        $this->get('/admin/export')->assertOk()->assertDownload();
    }

    public function test_admin_cannot_demote_or_delete_self(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->patch("/admin/users/{$admin->id}/role", ['is_admin' => 0])->assertSessionHas('error');
        $this->actingAs($admin)->delete("/admin/users/{$admin->id}")->assertSessionHas('error');

        $this->assertTrue($admin->fresh()->is_admin);
    }

    public function test_admin_can_promote_user(): void
    {
        $user = User::factory()->create();

        $this->actingAs(User::factory()->admin()->create())
            ->patch("/admin/users/{$user->id}/role", ['is_admin' => 1])
            ->assertSessionHas('status');

        $this->assertTrue($user->fresh()->is_admin);
    }
}
