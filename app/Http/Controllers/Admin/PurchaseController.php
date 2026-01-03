<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\PurchaseResource;
use App\Repositories\PurchaseItemRepository;
use App\Repositories\Contracts\PurchaseRepositoryInterface;

class PurchaseController extends Controller
{
    public function __construct(protected PurchaseRepositoryInterface $purchaseRepository)
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

        $purchases = $this->purchaseRepository
            ->with(['supplier'])
            ->filterByKeyword($search)
            ->filterByDate($startDate, $endDate)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
            // dd($purchases);

        return Inertia::render('purchase/Index', [
            'purchases' => PurchaseResource::collection($purchases),
        ]);
    }

    // public function create()
    // {
    //     $categories = Category::select('id', 'name')
    //         ->orderBy('name', 'asc')
    //         ->get();
            
    //     return Inertia::render('products/Create', [
    //         'categories' => $categories,
    //         'units' => Unit::collection(),
    //     ]);
    // }

    // public function store(StoreRequest $request, FileUploadService $fileUploadService)
    // {
    //     DB::beginTransaction();

    //     try {
    //         $payload = $request->validated();

    //         $product = new Product();
    //         $product->forceFill($payload);

    //         $this->purchaseRepository->save($product);

    //         if ($request->hasFile('product_photo')) {
    //             $file = $fileUploadService->upload(
    //                 $request->file('product_photo'),
    //                 Auth::user()?->id,
    //                 'uploads/products/product-images'
    //             );

    //             $product->product_image_id = $file->id;
    //             $product->save();
    //         }

    //         DB::commit();

    //         return redirect()
    //             ->route('products.index')
    //             ->with('flash', [
    //                 'type' => 'success',
    //                 'message' => __('Product successfully created.'),
    //             ]);

    //     } catch (\Throwable $exception) {
    //         DB::rollBack();
    //         report($exception);

    //         return back()->withErrors([
    //             'error' => __('Something went wrong. Please try again.')
    //         ]);
    //     }
    // }

    // public function edit(Product $product)
    // {
    //     $categories = Category::select('id', 'name')
    //         ->orderBy('name', 'asc')
    //         ->get();
            
    //     $product->load(['productPhoto','category']);
        
    //     return Inertia::render('products/Edit', [
    //         'product' => new ProductResource($product),
    //         'categories' => $categories,
    //         'units' => Unit::collection(),
    //     ]);
    // }

    // public function update(UpdateRequest $request, Product $product)
    // {
    //     $payload = $request->validated();

    //     $product->forceFill($payload);
    //     try {
    //         $this->purchaseRepository->save($product);
    //     } catch (Exception $exception) {
    //         report($exception);
    //     }

    //     return redirect()
    //         ->route('products.index')
    //         ->with('flash', [
    //             'type' => 'success',
    //             'message' => __('Product successfully updated.'),
    //         ]);
    // }

    // public function destroy(Product $product)
    // {
    //     try {
    //         $this->purchaseRepository->delete($product);
    //     } catch (Exception $exception) {
    //         report($exception);
    //     }

    //     return redirect()
    //         ->route('products.index')
    //         ->with('flash', [
    //             'type' => 'danger',
    //             'message' => __('Product successfully deleted.'),
    //         ]);
    // }

    // public function toggleStatus(Product $product)
    // {
    //     $product->update([
    //         'is_active' => ! $product->is_active,
    //     ]);

    //      return redirect()
    //         ->route('products.index')
    //         ->with('flash', [
    //             'type' => 'success', 
    //             'message' => __('Product status updated.'), 
    //         ]);
    // }
}
