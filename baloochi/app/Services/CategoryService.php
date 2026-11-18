<?php

namespace App\Services;

use App\Models\Category;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use DomainException;

class CategoryService
{
    public function __construct(private readonly CategoryRepositoryInterface $categories)
    {
    }

    public function create(array $data): Category
    {
        return $this->categories->create($this->attributes($data));
    }

    public function update(Category $category, array $data): Category
    {
        return $this->categories->update($category, $this->attributes($data));
    }

    /**
     * @throws DomainException وقتی دسته هنوز محصول دارد
     */
    public function delete(Category $category): void
    {
        if ($this->categories->hasProducts($category)) {
            throw new DomainException('این دسته دارای محصول است. ابتدا محصولات آن را حذف یا به دسته دیگری منتقل کنید.');
        }

        $this->categories->delete($category);
    }

    private function attributes(array $data): array
    {
        return [
            'name' => $data['name'],
            'icon' => $data['icon'],
            'type' => $data['type'],
            'is_digital' => (bool) ($data['is_digital'] ?? false),
            'sort_order' => (int) ($data['sort_order'] ?? 0),
        ];
    }
}
