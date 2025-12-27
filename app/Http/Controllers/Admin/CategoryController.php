<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Inertia\Inertia;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Category\StoreRequest;
use App\Http\Resources\Admin\CategoryResource;
use App\Repositories\Contracts\CategoryRepositoryInterface;

class CategoryController extends Controller
{
    public function __construct(protected CategoryRepositoryInterface $categoryRepository)
    {}

    public function index()
    { 
        $categories = $this->categoryRepository->paginate();

        return Inertia::render('categories/Index', [
            'categories' => CategoryResource::collection($categories),
        ]);
    }

    public function create()
    {
        return Inertia::render('categories/Create', []);
    }

    public function store(StoreRequest $request)
    {
        $payload = $request->validated();

        $category = new Category();
        $category->forceFill($payload);

        try {
            $this->categoryRepository->save($category);
        } catch (Exception $exception) {
            report($exception);
        }

        return redirect()
            ->route('categories.index')
            ->with('flash', [
                'type' => 'success',
                'message' => __('Category successfully created.'),
            ]);
    }

    public function edit(Category $category)
    {
        return Inertia::render('categories/Edit', [
            'category' => new CategoryResource($category),
        ]);
    }

    public function update(StoreRequest $request, Category $category)
    {
        $payload = $request->validated();

        $category->forceFill($payload);

        try {
            $this->categoryRepository->save($category);
        } catch (Exception $exception) {
            report($exception);
        }

        return redirect()
            ->route('categories.index')
            ->with('flash', [
                'type' => 'success',
                'message' => __('Category successfully updated.'),
            ]);
    }

    public function destroy(Category $category)
    {
        try {
            $this->categoryRepository->delete($category);
        } catch (Exception $exception) {
            report($exception);
        }

        return redirect()
            ->route('categories.index')
            ->with('flash', [
                'type' => 'danger',
                'message' => __('Category successfully deleted.'),
            ]);
    }
}
