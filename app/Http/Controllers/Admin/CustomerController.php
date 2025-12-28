<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Carbon\Carbon;
use Inertia\Inertia;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\StoreRequest;
use App\Http\Requests\Customer\UpdateRequest;
use App\Http\Resources\Admin\CustomerResource;
use App\Repositories\Contracts\CustomerRepositoryInterface;

class CustomerController extends Controller
{
    public function __construct(protected CustomerRepositoryInterface $customerRepository)
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

        $customers = $this->customerRepository
            ->filterByKeyword($search)
            ->filterByDate($startDate, $endDate)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return Inertia::render('customers/Index', [
            'customers' => CustomerResource::collection($customers),
        ]);
    }

    public function create()
    {
        return Inertia::render('customers/Create', []);
    }

    public function store(StoreRequest $request)
    {
        $payload = $request->validated();

        $customer = new Customer();
        $customer->forceFill($payload);

        try {
            $this->customerRepository->save($customer);
        } catch (Exception $exception) {
            report($exception);
        }

        return redirect()
            ->route('customers.index')
            ->with('flash', [
                'type' => 'success',
                'message' => __('Category successfully created.'),
            ]);
    }

    public function edit(Customer $customer)
    {
        return Inertia::render('customers/Edit', [
            'customer' => new CustomerResource($customer),
        ]);
    }

    public function update(UpdateRequest $request, Customer $customer)
    {
        $payload = $request->validated();

        $customer->forceFill($payload);

        try {
            $this->customerRepository->save($customer);
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

    public function destroy(Customer $customer)
    {
        try {
            $this->customerRepository->delete($customer);
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

    public function toggleStatus(Customer $customer)
    {
        $customer->update([
            'is_active' => ! $customer->is_active,
        ]);

         return redirect()
            ->route('customers.index')
            ->with('flash', [
                'type' => 'success', 
                'message' => __('Customer status updated.'), 
            ]);
    }
}
