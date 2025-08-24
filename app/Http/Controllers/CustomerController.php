<?php

namespace App\Http\Controllers;

use App\Http\Requests\Customers\CustomerStoreRequest;
use App\Http\Requests\Customers\CustomerUpdateRequest;
use App\Models\Customer;
use App\Models\Location;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Indonesia;
use Inertia\Inertia;
use Inertia\Response;
use Request;

class CustomerController extends Controller
{
    /**
     * Display a listing of the customers.
     * @return Response
     */
    public function index(): Response
    {
        $customers = Customer::with('location')->get();
        return Inertia::render('customers/Index', [
            'customers' => $customers
        ]);
    }

    /**
     * Show the form for creating a new customer.
     * @param Request $request
     * @return Response
     */
    public function create(Request $request): Response
    {
        $provinces = Indonesia::allProvinces()->map(function ($province) {
            return [
                'label' => $province->name,
                'value' => $province->code,
                'id' => $province->id
            ];
        });

        return Inertia::render('customers/Create', [
            'provinces' => $provinces
        ]);
    }

    /**
     * Store a newly created customer in storage.
     * @param CustomerStoreRequest $request
     * @return RedirectResponse
     */
    public function store(CustomerStoreRequest $request): RedirectResponse
    {
        try {
            $validated = $request->validated();

            $provinceName = DB::table('indonesia_provinces')->where('code', $validated['location']['province'])->value('name') ?? $validated['location']['province'];
            $cityName = DB::table('indonesia_cities')->where('code', $validated['location']['cities'])->value('name') ?? $validated['location']['cities'];
            $districtName = DB::table('indonesia_districts')->where('code', $validated['location']['district'])->value('name') ?? $validated['location']['district'];
            $subdistrictName = DB::table('indonesia_villages')->where('code', $validated['location']['subdistrict'])->value('name') ?? $validated['location']['subdistrict'];

            $location = Location::create([
                'address' => $validated['location']['address'],
                'province' => $provinceName,
                'cities' => $cityName,
                'district' => $districtName,
                'sub_district' => $subdistrictName,
                'postal_code' => $validated['location']['postal_code'],
            ]);

            $customer = Customer::create([
                'customer_code' => $validated['code'],
                'name' => $validated['name'],
                'location_id' => $location->id,
            ]);

            return redirect()->route('customers.index')->with('success', 'Customer created successfully');
        } catch (\Exception $th) {
            return redirect()->back()->withErrors(['error' => 'Failed to create customer: ' . $th->getMessage()]);
        }
    }

    /**
     * Display the specified customer.
     * @param Customer $customer
     * @return Response
     */
    public function show(Customer $customer): Response
    {
        $customer->load('location');

        return Inertia::render('customers/Show', [
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
        $customer->load('location');

        $provinces = Indonesia::allProvinces()->map(function ($province) {
            return [
                'label' => $province->name,
                'value' => $province->code,
                'id' => $province->id
            ];
        });

        return Inertia::render('customers/Edit', [
            'customer' => $customer,
            'provinces' => $provinces
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
            $validated = $request->validated();

            // Get location names from codes
            $provinceName = DB::table('indonesia_provinces')->where('code', $validated['location']['province'])->value('name') ?? $validated['location']['province'];
            $cityName = DB::table('indonesia_cities')->where('code', $validated['location']['cities'])->value('name') ?? $validated['location']['cities'];
            $districtName = DB::table('indonesia_districts')->where('code', $validated['location']['district'])->value('name') ?? $validated['location']['district'];
            $subdistrictName = DB::table('indonesia_villages')->where('code', $validated['location']['subdistrict'])->value('name') ?? $validated['location']['subdistrict'];

            // Update customer basic info
            $customer->update([
                'customer_code' => $validated['customer_code'],
                'name' => $validated['name'],
            ]);

            // Update location
            $customer->location()->update([
                'address' => $validated['location']['address'],
                'province' => $provinceName,
                'cities' => $cityName,
                'district' => $districtName,
                'sub_district' => $subdistrictName,
                'postal_code' => $validated['location']['postal_code'],
            ]);

            return redirect()->route('customers.index')->with('success', 'Customer updated successfully');
        } catch (\Exception $th) {
            return redirect()->back()->withErrors(['error' => 'Failed to update customer: ' . $th->getMessage()]);
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

    /**
     * Get cities by province code.
     * @param string $provinceCode
     * @return JsonResponse
     */
    public function getCities(string $provinceCode): JsonResponse
    {
        try {
            $cities = DB::table('indonesia_cities')
                ->where('province_code', $provinceCode)
                ->orderBy('name')
                ->get();

            if ($cities->isEmpty()) {
                return response()->json(['error' => 'No cities found for this province'], 404);
            }

            $cityOptions = $cities->map(function ($city) {
                return [
                    'label' => $city->name,
                    'value' => $city->code,
                    'id' => $city->id
                ];
            })->values();

            return response()->json($cityOptions);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch cities: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get districts by city code.
     * @param string $cityCode
     * @return JsonResponse
     */
    public function getDistricts(string $cityCode): JsonResponse
    {
        try {
            $districts = DB::table('indonesia_districts')
                ->where('city_code', $cityCode)
                ->orderBy('name')
                ->get();

            if ($districts->isEmpty()) {
                return response()->json(['error' => 'No districts found for this city'], 404);
            }

            $districtOptions = $districts->map(function ($district) {
                return [
                    'label' => $district->name,
                    'value' => $district->code,
                    'id' => $district->id
                ];
            })->values();

            return response()->json($districtOptions);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch districts: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get subdistricts (villages) by district code.
     * @param string $districtCode
     * @return JsonResponse
     */
    public function getSubdistricts(string $districtCode): JsonResponse
    {
        try {
            $villages = DB::table('indonesia_villages')
                ->where('district_code', $districtCode)
                ->orderBy('name')
                ->get();

            if ($villages->isEmpty()) {
                return response()->json(['error' => 'No villages found for this district'], 404);
            }

            $villageOptions = $villages->map(function ($village) {
                return [
                    'label' => $village->name,
                    'value' => $village->code,
                    'id' => $village->id
                ];
            })->values();

            return response()->json($villageOptions);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch subdistricts: ' . $e->getMessage()], 500);
        }
    }
}
