<?php

namespace App\Repositories\Eloquent;

use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ProductRepository implements ProductRepositoryInterface
{
    public function paginateForAdmin(?string $search, ?int $categoryId, int $perPage = 25): LengthAwarePaginator
    {
        return Product::query()
            ->with('category')
            ->search($search)
            ->when($categoryId, fn ($q) => $q->where('category_id', $categoryId))
            ->ordered()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function allForPublic(): Collection
    {
        return Product::query()->ordered()->get();
    }

    public function create(array $attributes): Product
    {
        return Product::create($attributes);
    }

    public function update(Product $product, array $attributes): Product
    {
        $product->update($attributes);

        return $product->refresh();
    }

    public function delete(Product $product): void
    {
        $product->delete();
    }
}
