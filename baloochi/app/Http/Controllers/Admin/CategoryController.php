<?php

namespace App\Http\Controllers\Admin;

use App\Enums\CategoryType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CategoryRequest;
use App\Models\Category;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Services\CategoryService;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function __construct(
        private readonly CategoryRepositoryInterface $categories,
        private readonly CategoryService $service,
    ) {
    }

    public function index(Request $request): View
    {
        $search = $request->string('q')->trim()->limit(100, '')->toString();

        return view('admin.categories.index', [
            'categories' => $this->categories->allOrdered($search),
            'search' => $search,
        ]);
    }

    public function create(): View
    {
        return view('admin.categories.form', [
            'category' => new Category(['type' => CategoryType::Equipment, 'icon' => 'fa-tooth', 'is_digital' => false]),
        ]);
    }

    public function store(CategoryRequest $request): RedirectResponse
    {
        $category = $this->service->create($request->validated());

        return redirect()->route('admin.categories.index')
            ->with('status', "دسته «{$category->name}» افزوده شد.");
    }

    public function edit(Category $category): View
    {
        return view('admin.categories.form', ['category' => $category]);
    }

    public function update(CategoryRequest $request, Category $category): RedirectResponse
    {
        $category = $this->service->update($category, $request->validated());

        return redirect()->route('admin.categories.index')
            ->with('status', "دسته «{$category->name}» ویرایش شد.");
    }

    public function destroy(Category $category): RedirectResponse
    {
        try {
            $this->service->delete($category);
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('status', "دسته «{$category->name}» حذف شد.");
    }
}
