<?php

namespace App\Repositories\Eloquent;

use App\Models\Category;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use Illuminate\Support\Collection;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function allOrdered(?string $search = null): Collection
    {
        $search = trim((string) $search);

        return Category::query()
            ->withCount('products')
            ->when($search !== '', fn ($q) => $q->where('name', 'like', '%'.addcslashes($search, '\\%_').'%'))
            ->ordered()
            ->get();
    }

    public function create(array $attributes): Category
    {
        return Category::create($attributes);
    }

    public function update(Category $category, array $attributes): Category
    {
        $category->update($attributes);

        return $category->refresh();
    }

    public function delete(Category $category): void
    {
        $category->delete();
    }

    public function hasProducts(Category $category): bool
    {
        return $category->products()->exists();
    }
}
