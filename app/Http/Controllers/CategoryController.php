<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use App\Support\BusinessContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = Category::query()
            ->with('parent')
            ->orderBy('name')
            ->paginate(15);

        return view('categories.index', compact('categories'));
    }

    public function create(): View
    {
        $parents = Category::query()->orderBy('name')->get();

        return view('categories.create', compact('parents'));
    }

    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $slug = $this->makeUniqueSlug($request->string('name'));

        Category::query()->create([
            'business_id' => BusinessContext::id(),
            'parent_id' => $request->input('parent_id'),
            'name' => $request->string('name'),
            'slug' => $slug,
            'description' => $request->input('description'),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('categories.index')->with('status', 'Kategori berhasil dibuat.');
    }

    public function show(Category $category): View
    {
        $category->load(['parent', 'children', 'products']);

        return view('categories.show', compact('category'));
    }

    public function edit(Category $category): View
    {
        $parents = Category::query()
            ->whereKeyNot($category->id)
            ->orderBy('name')
            ->get();

        return view('categories.edit', compact('category', 'parents'));
    }

    public function update(UpdateCategoryRequest $request, Category $category): RedirectResponse
    {
        $slug = $this->makeUniqueSlug($request->string('name'), $category->id);

        $category->update([
            'parent_id' => $request->input('parent_id'),
            'name' => $request->string('name'),
            'slug' => $slug,
            'description' => $request->input('description'),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('categories.show', $category)->with('status', 'Kategori diperbarui.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->products()->exists()) {
            return back()->with('error', 'Kategori masih memiliki produk.');
        }

        if ($category->children()->exists()) {
            return back()->with('error', 'Kategori masih memiliki sub-kategori.');
        }

        $category->delete();

        return redirect()->route('categories.index')->with('status', 'Kategori dihapus.');
    }

    private function makeUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'kategori';
        $slug = $base;
        $i = 1;

        while (Category::query()
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->where('slug', $slug)
            ->exists()
        ) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }
}
