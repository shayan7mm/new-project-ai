<?php

namespace App\Repositories\Contracts;

use App\Models\Category;
use Illuminate\Support\Collection;

interface CategoryRepositoryInterface
{
    /** @return Collection<int, Category> */
    public function allOrdered(?string $search = null): Collection;

    public function create(array $attributes): Category;

    public function update(Category $category, array $attributes): Category;

    public function delete(Category $category): void;

    public function hasProducts(Category $category): bool;
}
