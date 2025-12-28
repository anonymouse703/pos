<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Carbon\Carbon;
use Inertia\Inertia;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Category\StoreRequest;
use App\Http\Requests\Category\UpdateRequest;
use App\Http\Resources\Admin\CategoryResource;
use App\Repositories\Contracts\CategoryRepositoryInterface;

class CategoryController extends Controller
{
    public function __construct(protected CategoryRepositoryInterface $categoryRepository)
    {}

    public function index(Request $request)
    { 
         $search = $request->input('search');
        
        $startDate = $request->input('start_date');
        $endDate   = $request->input('end_date');

        if ($startDate) {
            $startDate = Carbon::parse($startDate)->startOfDay(); 
        }

        if ($endDate) {
            $endDate = Carbon::parse($endDate)->endOfDay(); 
        }

        $categories = $this->categoryRepository
            ->filterByKeyword($search)
            ->filterByDate($startDate, $endDate)
            ->orderBy('published_at', 'desc')
            ->paginate(10);

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

    public function update(UpdateRequest $request, Category $category)
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

    public function toggleStatus(Category $category)
    {
        $category->update([
            'is_active' => ! $category->is_active,
        ]);

         return redirect()
            ->route('categories.index')
            ->with('flash', [
                'type' => 'success', 
                'message' => __('Category status updated.'), 
            ]);
    }

}
