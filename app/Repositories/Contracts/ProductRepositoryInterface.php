<?php

namespace App\Repositories\Contracts;

use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface ProductRepositoryInterface
{
    public function paginateForAdmin(?string $search, ?int $categoryId, int $perPage = 25): LengthAwarePaginator;

    /** @return Collection<int, Product> */
    public function allForPublic(): Collection;

    public function create(array $attributes): Product;

    public function update(Product $product, array $attributes): Product;

    public function delete(Product $product): void;
}
