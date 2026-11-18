<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Repositories\Contracts\SettingRepositoryInterface;
use App\Services\SettingService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * داده‌های اولیه (تنظیمات، دسته‌ها و محصولات) از فایل HTML اصلی سایت.
 */
class CatalogSeeder extends Seeder
{
    public function run(SettingRepositoryInterface $settings, SettingService $settingService): void
    {
        $data = json_decode(file_get_contents(database_path('seeders/data/catalog.json')), true, flags: JSON_THROW_ON_ERROR);

        DB::transaction(function () use ($data, $settings) {
            $settings->putMany(array_intersect_key($data['settings'], SettingService::EDITABLE));

            $categoryIds = [];
            foreach ($data['categories'] as $i => $c) {
                $category = Category::updateOrCreate(
                    ['name' => $c['name']],
                    [
                        'icon' => $c['icon'],
                        'type' => $c['type'] ?? 'equipment',
                        'is_digital' => (bool) ($c['digital'] ?? false),
                        'sort_order' => $i,
                    ],
                );
                $categoryIds[$c['id']] = $category->id;
            }

            foreach ($data['products'] as $i => $p) {
                if (! isset($categoryIds[$p['category']])) {
                    continue;
                }

                Product::updateOrCreate(
                    ['code' => $p['code']],
                    [
                        'category_id' => $categoryIds[$p['category']],
                        'name' => $p['name'],
                        'description' => $p['desc'] ?: null,
                        'brand' => $p['brand'],
                        'origin' => $p['origin'] ?: null,
                        'status' => $p['status'] ?? 'in',
                        'price_factory_cash' => (int) $p['priceFactoryCash'],
                        'price_factory_installment' => (int) $p['priceFactoryInstallment'],
                        'factory_installment_terms' => $p['factoryInstallmentTerms'] ?: null,
                        'price_baloochi_cash' => (int) $p['priceBaloochiCash'],
                        'price_baloochi_installment' => (int) $p['priceBaloochiInstallment'],
                        'baloochi_installment_terms' => $p['baloochiInstallmentTerms'] ?: null,
                        'image_url' => $p['imageUrl'] ?: null,
                        'pdf_url' => $p['pdfUrl'] ?: null,
                        'extra_info' => $p['extraInfo'] ?: null,
                        'sort_order' => $i,
                    ],
                );
            }
        });

        $settingService->flush();
    }
}
