<?php

namespace App\Http\Controllers;

use App\Helpers\GenerateInvoiceHelper;
use App\Http\Requests\Transactions\TransactionStoreRequest;
use App\Http\Requests\Transactions\TransactionUpdateRequest;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Transaction;
use DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;


class TransactionController extends Controller
{
    /**
     * Display a listing of the transactions.
     * @return Response
     */
    public function index(): Response
    {
        $transactions = Transaction::with(['customer', 'detailTransactions'])->get();

        // Transform the data to match frontend expectations
        $transactions->each(function ($transaction) {
            $transaction->detail_transactions = $transaction->detailTransactions;
        });

        return Inertia::render('transactions/Index', [
            'transactions' => $transactions
        ]);
    }

    /**
     * Show the form for creating a new transaction.
     * @return Response
     */
    public function create(): Response
    {
        $customers = Customer::all(['id', 'customer_code', 'name']);
        $products = Product::where('stock', '>', 0)->get(['id', 'product_code', 'name', 'price', 'stock']);

        return Inertia::render('transactions/Create', [
            'customers' => $customers,
            'products' => $products,
        ]);
    }

    /**
     * Store a newly created transactions and detail transactions in storage.
     * @param TransactionStoreRequest $request
     * @return RedirectResponse`
     */
    public function store(TransactionStoreRequest $request): RedirectResponse
    {
        \Log::info('Transaction store request data:', $request->all());

        $invoiceNumber = GenerateInvoiceHelper::generateInvoiceNumber();

        DB::beginTransaction();

        try {
            $stockValidation = $this->validateStockAvailability($request->input('items', []));

            if (!$stockValidation['valid']) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', $stockValidation['message']);
            }


            $transaction = Transaction::create([
                'customer_id' => $request->input('customer_id'),
                'invoice_number' => $invoiceNumber,
                'invoice_date' => $request->input('invoice_date'),
                'total' => $request->input('total'),
            ]);

            $this->storeDetailTransactions($request->input('items', []), $transaction->id);

            $this->updateProductStock($request->input('items', []));

            DB::commit();

            return redirect()->route('transactions.index')->with('success', 'Transaction created successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Transaction creation failed:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with(['error' => 'Failed to create transaction: ' . $e->getMessage()]);
        }
    }

    /**
     * Store detail transactions for a specific transaction.
     * @param Request $request
     * @param int $transactionId
     * @return void
     */
    private function storeDetailTransactions(array $items, int $transactionId): void
    {
        $detailData = [];

        foreach ($items as $item) {
            $product = Product::find($item['product_id']);
            $transaction = Transaction::find($transactionId);
            $netPrice = $this->calculateItemNetPrice($item);

            $detailData[] = [
                'transaction_id' => $transactionId,
                'product_id' => $item['product_id'],
                'invoice_number' => $transaction->invoice_number,
                'product_code' => $product->product_code,
                'product_name' => $product->name,
                'quantity' => $item['quantity'] ?? 0,
                'price_at_time' => $item['price_at_time'] ?? $product->price,
                'disc1' => $item['disc1'] ?? 0,
                'disc2' => $item['disc2'] ?? 0,
                'disc3' => $item['disc3'] ?? 0,
                'net_price' => $netPrice,
                'amount' => $netPrice * $item['quantity'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('detail_transactions')->insert($detailData);
    }

    /**
     * Validate stock availability for all items
     * @param array $items
     * @return array
     */
    private function validateStockAvailability(array $items): array
    {
        if (empty($items)) {
            return [
                'valid' => false,
                'message' => 'No items provided for transaction.'
            ];
        }

        foreach ($items as $item) {
            $product = Product::find($item['product_id'] ?? null);

            if (!$product) {
                return [
                    'valid' => false,
                    'message' => 'One or more products not found.'
                ];
            }

            $requestedQuantity = (int)($item['quantity'] ?? 0);

            if ($requestedQuantity <= 0) {
                return [
                    'valid' => false,
                    'message' => "Invalid quantity for product: {$product->name}"
                ];
            }

            if ($product->stock < $requestedQuantity) {
                return [
                    'valid' => false,
                    'message' => "Insufficient stock for product: {$product->name}. Available: {$product->stock}, Requested: {$requestedQuantity}"
                ];
            }
        }

        return ['valid' => true, 'message' => ''];
    }

    /**
     * Validate stock availability for transaction update
     * @param array $items
     * @return void
     * @throws \Exception
     */
    private function validateStockAvailabilityForUpdate(array $items): void
    {
        foreach ($items as $item) {
            $product = Product::find($item['product_id']);

            if (!$product) {
                throw new \Exception('One or more products not found.');
            }

            $requestedQuantity = (int)($item['quantity'] ?? 0);

            if ($requestedQuantity <= 0) {
                throw new \Exception("Invalid quantity for product: {$product->name}");
            }

            if ($product->stock < $requestedQuantity) {
                throw new \Exception("Insufficient stock for product: {$product->name}. Available: {$product->stock}, Requested: {$requestedQuantity}");
            }
        }
    }

    /**
     * Update product stock after successful transaction
     * @param array $items
     * @return void
     */
    private function updateProductStock(array $items): void
    {
        foreach ($items as $item) {
            Product::where('id', $item['product_id'])
                ->decrement('stock', $item['quantity']);
        }
    }

    /**
     * Calculate the net price for a single item.
     * @param array $item
     * @return float
     */
    private function calculateItemNetPrice(array $item): float
    {
        $price = $item['price_at_time'] ?? 0;
        $disc1 = $item['disc1'] ?? 0;
        $disc2 = $item['disc2'] ?? 0;
        $disc3 = $item['disc3'] ?? 0;

        $netPrice = $price;
        $netPrice -= ($netPrice * ($disc1 / 100));
        $netPrice -= ($netPrice * ($disc2 / 100));
        $netPrice -= ($netPrice * ($disc3 / 100));

        return round($netPrice, 2);
    }


    /**
     * Display the specified transaction.
     * @param Transaction $transaction
     * @return Response
     */
    public function show(Transaction $transaction): Response
    {
        $transaction->load(['customer.location', 'detailTransactions']);

        // Transform the data to match frontend expectations
        $transaction->detail_transactions = $transaction->detailTransactions;

        return Inertia::render('transactions/Show', [
            'transaction' => $transaction
        ]);
    }

    /**
     * Show the form for editing the specified transaction.
     * @param Transaction $transaction
     * @return Response
     */
    public function edit(Transaction $transaction): Response
    {
        // Load relationships
        $transaction->load(['customer.location', 'detailTransactions']);

        // Get customers and products for dropdowns
        $customers = Customer::all(['id', 'customer_code', 'name']);
        $products = Product::all(['id', 'product_code', 'name', 'price', 'stock']);

        // Transform detail transactions to match frontend expectations
        $transaction->detail_transactions = $transaction->detailTransactions->map(function ($detail) {
            return [
                'id' => $detail->id,
                'product_id' => $detail->product_id,
                'quantity' => $detail->quantity,
                'price_at_time' => $detail->price_at_time,
                'disc1' => $detail->disc1,
                'disc2' => $detail->disc2,
                'disc3' => $detail->disc3,
                'net_price' => $detail->net_price,
                'amount' => $detail->amount,
            ];
        });

        return Inertia::render('transactions/Edit', [
            'transaction' => $transaction,
            'customers' => $customers,
            'products' => $products,
        ]);
    }

    /**
     * Update the specified transaction in storage.
     * @param TransactionUpdateRequest $request
     * @param Transaction $transaction
     * @return RedirectResponse
     */
    public function update(TransactionUpdateRequest $request, Transaction $transaction): RedirectResponse
    {
        try {
            DB::beginTransaction();

            $validated = $request->validated();

            // Restore stock for existing detail transactions
            foreach ($transaction->detailTransactions as $oldDetail) {
                $product = Product::find($oldDetail->product_id);
                if ($product) {
                    $product->increment('stock', $oldDetail->quantity);
                }
            }

            // Delete existing detail transactions
            $transaction->detailTransactions()->delete();

            // Validate stock availability for new items
            $this->validateStockAvailabilityForUpdate($validated['items']);

            // Update transaction header
            $transaction->update([
                'customer_id' => $validated['customer_id'],
                'invoice_date' => $validated['invoice_date'],
                'total' => $validated['total'],
            ]);

            // Create new detail transactions and update stock
            foreach ($validated['items'] as $item) {
                // Create detail transaction
                $transaction->detailTransactions()->create([
                    'invoice_number' => $transaction->invoice_number,
                    'product_id' => $item['product_id'],
                    'product_code' => Product::find($item['product_id'])->product_code,
                    'product_name' => Product::find($item['product_id'])->name,
                    'quantity' => $item['quantity'],
                    'price_at_time' => $item['price_at_time'],
                    'disc1' => $item['disc1'],
                    'disc2' => $item['disc2'],
                    'disc3' => $item['disc3'],
                    'net_price' => $this->calculateItemNetPrice($item),
                    'amount' => $this->calculateItemNetPrice($item) * $item['quantity'],
                ]);

                // Update product stock
                Product::find($item['product_id'])->decrement('stock', $item['quantity']);
            }

            DB::commit();

            return redirect()->route('transactions.index')->with('success', 'Transaction updated successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with(['error' => 'Failed to update transaction: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified transaction from storage.
     * @param Transaction $transaction
     * @return RedirectResponse
     */
    public function destroy(Transaction $transaction): RedirectResponse
    {
        try {
            $transaction->delete();
            return redirect()->route('transactions.index')->with('success', 'Transaction deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with(['error' => 'Failed to delete transaction']);
        }
    }

    /**
     * Get customer details by ID for AJAX request.
     * @param int $customerId
     * @return JsonResponse
     */
    public function getCustomerDetails(int $customerId): JsonResponse
    {
        try {
            $customer = Customer::with('location')->find($customerId);

            if (!$customer) {
                return response()->json(['error' => 'Customer not found'], 404);
            }

            return response()->json([
                'id' => $customer->id,
                'customer_code' => $customer->customer_code,
                'name' => $customer->name,
                'address' => $customer->location ?
                    $customer->location->address . ', ' .
                    $customer->location->cities . ', ' .
                    $customer->location->province . ' ' .
                    $customer->location->postal_code
                    : 'Address not available'
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch customer details'], 500);
        }
    }

    /**
     * Get product details by ID for AJAX request.
     * @param int $productId
     * @return JsonResponse
     */
    public function getProductDetails(int $productId): JsonResponse
    {
        try {
            $product = Product::find($productId);

            if (!$product) {
                return response()->json(['error' => 'Product not found'], 404);
            }

            return response()->json([
                'id' => $product->id,
                'product_code' => $product->product_code,
                'name' => $product->name,
                'price' => $product->price,
                'stock' => $product->stock
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch product details'], 500);
        }
    }
}
