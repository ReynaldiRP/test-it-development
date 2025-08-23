<?php

namespace App\Http\Controllers;

use App\Http\Requests\Products\ProductStoreRequest;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    /**
     * Display a listing of the products.
     * @return Response
     */
    public function index(): Response
    {
        $products = Product::paginate(5);
        return Inertia::render('Products/Index', [
            'products' => $products
        ]);
    }

    /**
     * Show the form for creating a new product.
     * @return Response
     */
    public function create(): Response
    {
        return Inertia::render('Products/Create');
    }

    /**
     * Store a newly created product in storage.
     * @param ProductStoreRequest $request
     * @return RedirectResponse
     */
    public function store(ProductStoreRequest $request): RedirectResponse
    {
        try {
            $product = Product::create($request->validated());
            return redirect()->route('products.index', $product);
        } catch (\Exception $th) {
            return redirect()->back()->withErrors(['error' => 'Failed to create product']);
        }
    }


    /**
     * Display the specified product.
     * @param Product $product
     * @return Response
     */
    public function show(Product $product): Response
    {
        return Inertia::render('Products/Show', [
            'product' => $product
        ]);
    }

    /**
     * Show the form for editing the specified product.
     * @param Product $product
     * @return Response
     */
    public function edit(Product $product): Response
    {
        return Inertia::render('Products/Edit', [
            'product' => $product
        ]);
    }

    /**
     * Update the specified product in storage.
     * @param ProductStoreRequest $request
     * @param Product $product
     * @return RedirectResponse
     */
    public function update(ProductStoreRequest $request, Product $product): RedirectResponse
    {
        try {
            $product->update($request->validated());
            return redirect()->route('products.index', $product);
        } catch (\Exception $th) {
            return redirect()->back()->withErrors(['error' => 'Failed to update product']);
        }
    }

    /**
     * Remove the specified product from storage.
     * @param Product $product
     * @return RedirectResponse
     */
    public function destroy(Product $product): RedirectResponse
    {
        try {
            $product->delete();
            return redirect()->route('products.index');
        } catch (\Exception $th) {
            return redirect()->back()->withErrors(['error' => 'Failed to delete product']);
        }
    }
}
