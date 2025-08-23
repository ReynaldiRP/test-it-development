<?php

namespace App\Http\Controllers;

use App\Http\Requests\Customers\CustomerStoreRequest;
use App\Http\Requests\Customers\CustomerUpdateRequest;
use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class CustomerController extends Controller
{
    /**
     * Display a listing of the customers.
     * @return Response
     */
    public function index(): Response
    {
        $customers = Customer::with('location')->paginate(5);
        return Inertia::render('Customers/Index', [
            'customers' => $customers
        ]);
    }

    /**
     * Show the form for creating a new customer.
     * @return Response
     */
    public function create(): Response
    {
        return Inertia::render('Customers/Create');
    }

    /**
     * Store a newly created customer in storage.
     * @param CustomerStoreRequest $request
     * @return RedirectResponse
     */
    public function store(CustomerStoreRequest $request): RedirectResponse
    {
        try {
            $customer = Customer::create($request->validated());
            return redirect()->route('customers.index', $customer);
        } catch (\Exception $th) {
            return redirect()->back()->withErrors(['error' => 'Failed to create customer']);
        }
    }

    /**
     * Display the specified customer.
     * @param Customer $customer
     * @return Response
     */
    public function show(Customer $customer): Response
    {
        return Inertia::render('Customers/Show', [
            'customer' => $customer
        ]);
    }

    /**
     * Show the form for editing the specified customer.
     * @param Customer $customer
     * @return Response
     */
    public function edit(Customer $customer): Response
    {
        return Inertia::render('Customers/Edit', [
            'customer' => $customer
        ]);
    }

    /**
     * Update the specified customer in storage.
     * @param CustomerUpdateRequest $request
     * @param Customer $customer
     * @return RedirectResponse
     */
    public function update(CustomerUpdateRequest $request, Customer $customer): RedirectResponse
    {
        try {
            $customer->update($request->validated());
            return redirect()->route('customers.index', $customer);
        } catch (\Exception $th) {
            return redirect()->back()->withErrors(['error' => 'Failed to update customer']);
        }
    }

    /**
     * Remove the specified customer from storage.
     * @param Customer $customer
     * @return RedirectResponse
     */
    public function destroy(Customer $customer): RedirectResponse
    {
        try {
            $customer->delete();
            return redirect()->route('customers.index');
        } catch (\Exception $th) {
            return redirect()->back()->withErrors(['error' => 'Failed to delete customer']);
        }
    }
}
