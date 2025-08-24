<?php

namespace App\Http\Controllers;

use App\Http\Requests\Products\ProductStoreRequest;
use App\Http\Requests\Products\ProductUpdateRequest;
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
        $products = Product::all();
        return Inertia::render('products/Index', [
            'products' => $products
        ]);
    }

    /**
     * Show the form for creating a new product.
     * @return Response
     */
    public function create(): Response
    {
        return Inertia::render('products/Create');
    }

    /**
     * Store a newly created product in storage.
     * @param ProductStoreRequest $request
     * @return RedirectResponse
     */
    public function store(ProductStoreRequest $request): RedirectResponse
    {
        try {
            Product::create($request->validated());
            return redirect()->route('products.index')->with('success', 'Product created successfully');
        } catch (\Exception $th) {
            return redirect()->back()->withErrors(['error' => 'Failed to create product: ' . $th->getMessage()]);
        }
    }


    /**
     * Display the specified product.
     * @param Product $product
     * @return Response
     */
    public function show(Product $product): Response
    {
        return Inertia::render('products/Show', [
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
        return Inertia::render('products/Edit', [
            'product' => $product
        ]);
    }

    /**
     * Update the specified product in storage.
     * @param ProductUpdateRequest $request
     * @param Product $product
     * @return RedirectResponse
     */
    public function update(ProductUpdateRequest $request, Product $product): RedirectResponse
    {
        try {
            $product->update($request->validated());
            return redirect()->route('products.index')->with('success', 'Product updated successfully');
        } catch (\Exception $th) {
            return redirect()->back()->withErrors(['error' => 'Failed to update product: ' . $th->getMessage()]);
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
            // Check if product has been used in transactions
            if ($product->detailTransactions()->exists()) {
                return redirect()->back()->withErrors([
                    'error' => 'Cannot delete product. This product has been used in transactions.'
                ]);
            }

            $product->delete();
            return redirect()->route('products.index')->with('success', 'Product deleted successfully');
        } catch (\Exception $th) {
            return redirect()->back()->withErrors(['error' => 'Failed to delete product: ' . $th->getMessage()]);
        }
    }
}
