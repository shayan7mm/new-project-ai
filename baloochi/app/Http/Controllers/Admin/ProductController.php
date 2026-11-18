<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ProductStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductRequest;
use App\Models\Product;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Services\ProductService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct(
        private readonly ProductRepositoryInterface $products,
        private readonly CategoryRepositoryInterface $categories,
        private readonly ProductService $service,
    ) {
    }

    public function index(Request $request): View
    {
        $search = $request->string('q')->trim()->limit(100, '')->toString();
        $categoryId = $request->integer('category') ?: null;

        return view('admin.products.index', [
            'products' => $this->products->paginateForAdmin($search, $categoryId),
            'categories' => $this->categories->allOrdered(),
            'search' => $search,
            'categoryId' => $categoryId,
        ]);
    }

    public function create(): View
    {
        return view('admin.products.form', [
            'product' => new Product(['status' => ProductStatus::InStock]),
            'categories' => $this->categories->allOrdered(),
        ]);
    }

    public function store(ProductRequest $request): RedirectResponse
    {
        $product = $this->service->create(
            $request->validated(),
            $request->file('image'),
            $request->file('pdf'),
        );

        return redirect()->route('admin.products.index')
            ->with('status', "محصول «{$product->name}» افزوده شد.");
    }

    public function edit(Product $product): View
    {
        return view('admin.products.form', [
            'product' => $product,
            'categories' => $this->categories->allOrdered(),
        ]);
    }

    public function update(ProductRequest $request, Product $product): RedirectResponse
    {
        $product = $this->service->update(
            $product,
            $request->validated(),
            $request->file('image'),
            $request->file('pdf'),
        );

        return redirect()->route('admin.products.index')
            ->with('status', "محصول «{$product->name}» ویرایش شد.");
    }

    public function destroy(Product $product): RedirectResponse
    {
        $name = $product->name;
        $this->service->delete($product);

        return back()->with('status', "محصول «{$name}» حذف شد.");
    }
}
