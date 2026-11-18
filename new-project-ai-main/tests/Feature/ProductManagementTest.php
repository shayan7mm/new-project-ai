<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductManagementTest extends TestCase
{
    use RefreshDatabase;

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'code' => 'UNT-100',
            'category_id' => Category::factory()->create()->id,
            'name' => 'یونیت تست',
            'brand' => 'RTF',
            'status' => 'in',
            'price_factory_cash' => '۱۱۰,۰۰۰,۰۰۰',
            'price_baloochi_cash' => 115000000,
            'price_factory_installment' => 0,
            'price_baloochi_installment' => 0,
        ], $overrides);
    }

    public function test_admin_can_create_product_with_image_and_persian_digits(): void
    {
        Storage::fake('public');
        $this->actingAs(User::factory()->admin()->create());

        $this->post('/admin/products', $this->payload([
            'image' => UploadedFile::fake()->image('p.jpg', 200, 200),
        ]))->assertRedirect(route('admin.products.index'));

        $product = Product::firstWhere('code', 'UNT-100');
        $this->assertSame(110000000, $product->price_factory_cash);
        Storage::disk('public')->assertExists($product->image_path);
    }

    public function test_javascript_url_and_svg_upload_are_rejected(): void
    {
        $this->actingAs(User::factory()->admin()->create());

        $this->post('/admin/products', $this->payload([
            'image_url' => 'javascript:alert(1)',
            'image' => UploadedFile::fake()->create('x.svg', 1, 'image/svg+xml'),
        ]))->assertSessionHasErrors(['image_url', 'image']);
    }

    public function test_admin_can_update_and_delete_product(): void
    {
        $this->actingAs(User::factory()->admin()->create());
        $product = Product::factory()->create();

        $this->put("/admin/products/{$product->id}", $this->payload(['code' => $product->code, 'name' => 'نام جدید']))
            ->assertRedirect(route('admin.products.index'));
        $this->assertSame('نام جدید', $product->fresh()->name);

        $this->delete("/admin/products/{$product->id}")->assertRedirect();
        $this->assertModelMissing($product);
    }

    public function test_duplicate_code_is_rejected(): void
    {
        $this->actingAs(User::factory()->admin()->create());
        $existing = Product::factory()->create();

        $this->post('/admin/products', $this->payload(['code' => $existing->code]))->assertSessionHasErrors('code');
    }

    public function test_category_with_products_cannot_be_deleted(): void
    {
        $this->actingAs(User::factory()->admin()->create());
        $product = Product::factory()->create();

        $this->delete("/admin/categories/{$product->category_id}")->assertSessionHas('error');
        $this->assertDatabaseHas('categories', ['id' => $product->category_id]);
    }

    public function test_category_icon_must_be_whitelisted(): void
    {
        $this->actingAs(User::factory()->admin()->create());

        $this->post('/admin/categories', [
            'name' => 'تست', 'type' => 'equipment', 'is_digital' => 0, 'icon' => '"><script>',
        ])->assertSessionHasErrors('icon');
    }

    public function test_public_page_shows_products_and_escapes_html(): void
    {
        Product::factory()->create(['name' => '</script><script>alert(1)</script>']);

        $response = $this->get('/')->assertOk();
        $response->assertDontSee('</script><script>alert(1)', false);
        $response->assertSee('\\u003C\\/script\\u003E', false);
        $response->assertHeader('Content-Security-Policy');
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
    }

    public function test_product_save_updates_last_price_update_setting(): void
    {
        $this->actingAs(User::factory()->admin()->create());
        $this->post('/admin/products', $this->payload());

        $this->assertDatabaseHas('settings', ['key' => 'lastPriceUpdate']);
    }
}
