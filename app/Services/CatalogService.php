<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Product;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;

/**
 * داده‌ی صفحه‌ی عمومی (و خروجی پشتیبان) را با همان ساختار فایل HTML اولیه می‌سازد
 * تا جاوااسکریپت فیلتر و جستجو بدون تغییر ساختار کار کند.
 */
class CatalogService
{
    public function __construct(
        private readonly CategoryRepositoryInterface $categories,
        private readonly ProductRepositoryInterface $products,
        private readonly SettingService $settings,
    ) {
    }

    public function publicData(): array
    {
        return [
            'settings' => $this->settings->forPublic(),
            'categories' => $this->categories->allOrdered()->map(fn (Category $c) => [
                'id' => $c->id,
                'name' => $c->name,
                'icon' => $c->icon,
                'digital' => $c->is_digital,
                'type' => $c->type->value,
            ])->values()->all(),
            'products' => $this->products->allForPublic()->map(fn (Product $p) => [
                'id' => $p->id,
                'code' => $p->code,
                'name' => $p->name,
                'desc' => (string) $p->description,
                'category' => $p->category_id,
                'brand' => $p->brand,
                'origin' => (string) $p->origin,
                'status' => $p->status->value,
                'priceFactoryCash' => $p->price_factory_cash,
                'priceBaloochiCash' => $p->price_baloochi_cash,
                'priceFactoryInstallment' => $p->price_factory_installment,
                'factoryInstallmentTerms' => (string) $p->factory_installment_terms,
                'priceBaloochiInstallment' => $p->price_baloochi_installment,
                'baloochiInstallmentTerms' => (string) $p->baloochi_installment_terms,
                'imageUrl' => (string) $p->image_src,
                'pdfUrl' => (string) $p->pdf_src,
                'extraInfo' => (string) $p->extra_info,
            ])->values()->all(),
        ];
    }
}
