<?php

namespace App\Http\Controllers;

use App\Helpers\GenerateInvoiceHelper;
use App\Http\Requests\Transactions\TransactionStoreRequest;
use App\Http\Requests\Transactions\TransactionUpdateRequest;
use App\Models\Product;
use App\Models\Transaction;
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
        $transactions = Transaction::paginate(5);
        return Inertia::render('Transactions/Index', [
            'transactions' => $transactions
        ]);
    }

    /**
     * Show the form for creating a new transaction.
     * @return Response
     */
    public function create(): Response
    {
        return Inertia::render('Transactions/Create');
    }

    /**
     * Store a newly created transactions and detail transactions in storage.
     * @param TransactionStoreRequest $request
     * @return RedirectResponse
     */
    public function store(TransactionStoreRequest $request): RedirectResponse
    {
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

            $this->storeDetailTransactions($request, $transaction->id);

            $this->updateProductStock($request->input('items', []));

            DB::commit();

            return redirect()->route('transactions.index')->with('success', 'Transaction created successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with(['error' => 'Failed to create transaction']);
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
            $netPrice = $this->calculatedNetPrice([$item]);

            $detailData[] = [
                'transaction_id' => $transactionId,
                'product_id' => $item['product_id'],
                'invoice_number' => $transaction->invoice_number,
                'product_code' => $product->code,
                'product_name' => $product->name,
                'quantity' => $item['quantity'] ?? 0,
                'price_at_time' => $product->price,
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
     * Calculate the net price for the given items.
     * @param array $items
     * @return float
     */
    private function calculatedNetPrice(array $items): float
    {
        $totalNetPrice = 0;

        foreach ($items as $item) {
            $price = $item['price'] ?? 0;
            $disc1 = $item['disc1'] ?? 0;
            $disc2 = $item['disc2'] ?? 0;
            $disc3 = $item['disc3'] ?? 0;

            $netPrice = $price;
            $netPrice -= ($netPrice * ($disc1 / 100));
            $netPrice -= ($netPrice * ($disc2 / 100));
            $netPrice -= ($netPrice * ($disc3 / 100));

            $totalNetPrice += round($netPrice, 2);
        }

        return $totalNetPrice;
    }

    
    /**
     * Display the specified transaction.
     * @param Transaction $transaction
     * @return Response
     */
    public function show(Transaction $transaction): Response
    {
        return Inertia::render('Transactions/Show', [
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
        return Inertia::render('Transactions/Edit', [
            'transaction' => $transaction
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
            $transaction->update($request->validated());
            return redirect()->route('transactions.index')->with('success', 'Transaction updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with(['error' => 'Failed to update transaction']);
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
    }
}
