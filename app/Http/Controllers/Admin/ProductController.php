<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Carbon\Carbon;
use Inertia\Inertia;
use App\Models\Product;
use App\Models\Category;
use App\Enums\Product\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Product\StoreRequest;
use App\Http\Requests\Product\UpdateRequest;
use App\Http\Resources\Admin\ProductResource;
use App\Services\FileUpload\FileUploadService;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\Contracts\CategoryRepositoryInterface;

class ProductController extends Controller
{
    public function __construct(protected ProductRepositoryInterface $productRepository, protected CategoryRepositoryInterface $categoryRepository)
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

        $products = $this->productRepository
            ->with(['productPhoto', 'category'])
            ->filterByKeyword($search)
            ->filterByDate($startDate, $endDate)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
            // dd($products);

        return Inertia::render('products/Index', [
            'products' => ProductResource::collection($products),
        ]);
    }

    public function create()
    {
        $categories = Category::select('id', 'name')
            ->orderBy('name', 'asc')
            ->get();
            
        return Inertia::render('products/Create', [
            'categories' => $categories,
            'units' => Unit::collection(),
        ]);
    }

    public function store(StoreRequest $request, FileUploadService $fileUploadService)
    {
        DB::beginTransaction();

        try {
            $payload = $request->validated();

            $product = new Product();
            $product->forceFill($payload);

            $this->productRepository->save($product);

            if ($request->hasFile('product_photo')) {
                $file = $fileUploadService->upload(
                    $request->file('product_photo'),
                    Auth::user()?->id,
                    'uploads/products/product-images'
                );

                $product->product_image_id = $file->id;
                $product->save();
            }

            DB::commit();

            return redirect()
                ->route('products.index')
                ->with('flash', [
                    'type' => 'success',
                    'message' => __('Product successfully created.'),
                ]);

        } catch (\Throwable $exception) {
            DB::rollBack();
            report($exception);

            return back()->withErrors([
                'error' => __('Something went wrong. Please try again.')
            ]);
        }
    }

    public function edit(Product $product)
    {
        $categories = Category::select('id', 'name')
            ->orderBy('name', 'asc')
            ->get();
            
        $product->load(['productPhoto','category']);
        
        return Inertia::render('products/Edit', [
            'product' => new ProductResource($product),
            'categories' => $categories,
            'units' => Unit::collection(),
        ]);
    }

    public function update(UpdateRequest $request, Product $product)
    {
        $payload = $request->validated();

        $product->forceFill($payload);
        try {
            $this->productRepository->save($product);
        } catch (Exception $exception) {
            report($exception);
        }

        return redirect()
            ->route('products.index')
            ->with('flash', [
                'type' => 'success',
                'message' => __('Product successfully updated.'),
            ]);
    }

    public function destroy(Product $product)
    {
        try {
            $this->productRepository->delete($product);
        } catch (Exception $exception) {
            report($exception);
        }

        return redirect()
            ->route('products.index')
            ->with('flash', [
                'type' => 'danger',
                'message' => __('Product successfully deleted.'),
            ]);
    }

    public function toggleStatus(Product $product)
    {
        $product->update([
            'is_active' => ! $product->is_active,
        ]);

         return redirect()
            ->route('products.index')
            ->with('flash', [
                'type' => 'success', 
                'message' => __('Product status updated.'), 
            ]);
    }
}
