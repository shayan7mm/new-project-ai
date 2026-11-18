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

        foreach (['/admin/products', '/admin/products/create', "/admin/products/{$product->id}/edit", '/admin/categories', '/admin/categories/create', '/admin/settings', '/admin/users', '/admin/users/create', '/admin/help'] as $url) {
            $this->get($url)->assertOk();
        }
        $this->get('/admin/export')->assertOk()->assertDownload();
    }

    public function test_public_price_list_is_visible_to_guests(): void
    {
        $product = Product::factory()->create(['name' => 'یونیت عمومی']);

        $this->get('/')->assertOk()->assertSee('یونیت عمومی')->assertSee((string) $product->price_baloochi_cash);
    }

    public function test_admin_cannot_delete_self(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->delete("/admin/users/{$admin->id}")->assertSessionHas('error');
        $this->assertModelExists($admin);
    }

    public function test_admin_can_create_another_admin_who_can_log_in(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->post('/admin/users', [
                'name' => 'مدیر دوم',
                'email' => 'Second@Example.com',
                'password' => 'Secret123',
                'password_confirmation' => 'Secret123',
            ])->assertRedirect(route('admin.users.index'));

        $second = User::firstWhere('email', 'second@example.com');
        $this->assertTrue($second->is_admin);

        auth()->logout();
        $this->post('/login', ['email' => 'second@example.com', 'password' => 'Secret123'])
            ->assertRedirect(route('admin.products.index'));
    }

    public function test_admin_can_change_password_and_blank_keeps_old_one(): void
    {
        $admin = User::factory()->admin()->create(['password' => 'Secret123']);
        $this->actingAs($admin);

        $this->put("/admin/users/{$admin->id}", ['name' => 'نام تازه', 'email' => $admin->email, 'password' => ''])->assertSessionHasNoErrors();
        $this->assertTrue(\Hash::check('Secret123', $admin->fresh()->password));

        $this->put("/admin/users/{$admin->id}", ['name' => 'نام تازه', 'email' => $admin->email, 'password' => 'NewPass99', 'password_confirmation' => 'NewPass99'])->assertSessionHasNoErrors();
        $this->assertTrue(\Hash::check('NewPass99', $admin->fresh()->password));
    }
}
