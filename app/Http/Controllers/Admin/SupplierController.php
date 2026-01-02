<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Carbon\Carbon;
use Inertia\Inertia;
use App\Models\Supplier;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Supplier\StoreRequest;
use App\Http\Requests\Supplier\UpdateRequest;
use App\Http\Resources\Admin\SupplierResource;
use App\Repositories\Contracts\SupplierRepositoryInterface;

class SupplierController extends Controller
{
    public function __construct(protected SupplierRepositoryInterface $supplierRepository)
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

        $suppliers = $this->supplierRepository
            ->filterByKeyword($search)
            ->filterByDate($startDate, $endDate)
            ->orderBy('published_at', 'desc')
            ->paginate(10);

        return Inertia::render('suppliers/Index', [
            'suppliers' => SupplierResource::collection($suppliers),
        ]);
    }

    public function create()
    {
        return Inertia::render('suppliers/Create', []);
    }

    public function store(StoreRequest $request)
    {
        $payload = $request->validated();

        $supplier = new Supplier();
        $supplier->forceFill($payload);

        try {
            $this->supplierRepository->save($supplier);
        } catch (Exception $exception) {
            report($exception);
        }

        return redirect()
            ->route('suppliers.index')
            ->with('flash', [
                'type' => 'success',
                'message' => __('Supplier successfully created.'),
            ]);
    }

    public function edit(Supplier $supplier)
    {
        return Inertia::render('suppliers/Edit', [
            'supplier' => new SupplierResource($supplier),
        ]);
    }

    public function update(UpdateRequest $request, Supplier $supplier)
    {
        $payload = $request->validated();

        $supplier->forceFill($payload);
        try {
            $this->supplierRepository->save($supplier);
        } catch (Exception $exception) {
            report($exception);
        }

        return redirect()
            ->route('suppliers.index')
            ->with('flash', [
                'type' => 'success',
                'message' => __('Supplier successfully updated.'),
            ]);
    }

    public function destroy(Supplier $supplier)
    {
        try {
            $this->supplierRepository->delete($supplier);
        } catch (Exception $exception) {
            report($exception);
        }

        return redirect()
            ->route('suppliers.index')
            ->with('flash', [
                'type' => 'danger',
                'message' => __('Supplier successfully deleted.'),
            ]);
    }

}
