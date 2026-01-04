<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use Inertia\Inertia;
use App\Models\Product;
use App\Models\Supplier;
use Faker\Provider\Payment;
use Illuminate\Http\Request;
use App\Enums\Purchase\Status;
use App\Http\Controllers\Controller;
use App\Enums\Purchase\PaymentMethod;
use App\Services\Purchase\PurchaseService;
use App\Http\Requests\Purchase\StoreRequest;
use App\Repositories\PurchaseItemRepository;
use App\Http\Resources\Admin\PurchaseResource;
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

    public function create()
    {
        $suppliers = Supplier::select('id', 'name')
            ->orderBy('name', 'asc')
            ->get();
        
        $products = Product::select('id', 'name')
            ->orderBy('name', 'asc')
            ->get();
            
        return Inertia::render('purchase/Create', [
            'suppliers' => $suppliers,
            'products' => $products,
            'payment_methods' => PaymentMethod::collection(),
            'payment_statuses' => Status::collection(),
            
        ]);
    }

    public function store(StoreRequest $request, PurchaseService $service)
    {
        $service->store($request->validated());

        return redirect()
            ->route('purchases.index')
            ->with('success', 'Purchase created successfully');
    }

    public function show($id)
    {
        $purchase = $this->purchaseRepository
            ->with(['supplier', 'items.product'])
            ->find($id);

        return Inertia::render('purchase/Show', [
            'purchase' => $purchase,
        ]);
    }

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
