<?php

namespace App\Services;

use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductService
{
    public function __construct(
        private readonly ProductRepositoryInterface $products,
        private readonly SettingService $settings,
    ) {
    }

    /**
     * @param  array<string, mixed>  $data  داده‌ی اعتبارسنجی‌شده از ProductRequest
     */
    public function create(array $data, ?UploadedFile $image, ?UploadedFile $pdf): Product
    {
        $attributes = $this->attributes($data);

        if ($image) {
            $attributes['image_path'] = $image->store('products/images', 'public');
        }
        if ($pdf) {
            $attributes['pdf_path'] = $pdf->store('products/pdfs', 'public');
        }

        $product = DB::transaction(fn () => $this->products->create($attributes));
        $this->settings->touchLastPriceUpdate();

        return $product;
    }

    public function update(Product $product, array $data, ?UploadedFile $image, ?UploadedFile $pdf): Product
    {
        $attributes = $this->attributes($data);

        if ($image || ! empty($data['remove_image'])) {
            $this->deleteFile($product->image_path);
            $attributes['image_path'] = $image?->store('products/images', 'public');
        }
        if ($pdf || ! empty($data['remove_pdf'])) {
            $this->deleteFile($product->pdf_path);
            $attributes['pdf_path'] = $pdf?->store('products/pdfs', 'public');
        }

        $product = DB::transaction(fn () => $this->products->update($product, $attributes));
        $this->settings->touchLastPriceUpdate();

        return $product;
    }

    public function delete(Product $product): void
    {
        $paths = [$product->image_path, $product->pdf_path];

        DB::transaction(fn () => $this->products->delete($product));

        foreach ($paths as $path) {
            $this->deleteFile($path);
        }
        $this->settings->touchLastPriceUpdate();
    }

    /** فقط فیلدهای مجاز ستون‌های جدول را برمی‌گرداند */
    private function attributes(array $data): array
    {
        $attributes = collect($data)->only([
            'category_id', 'code', 'name', 'description', 'brand', 'origin', 'status',
            'price_factory_cash', 'price_factory_installment', 'factory_installment_terms',
            'price_baloochi_cash', 'price_baloochi_installment', 'baloochi_installment_terms',
            'image_url', 'pdf_url', 'extra_info', 'sort_order',
        ])->all();

        foreach (['price_factory_cash', 'price_factory_installment', 'price_baloochi_cash', 'price_baloochi_installment', 'sort_order'] as $int) {
            $attributes[$int] = (int) ($attributes[$int] ?? 0);
        }

        return $attributes;
    }

    private function deleteFile(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }
}
