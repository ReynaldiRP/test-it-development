<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $totalCustomers = Customer::count();
        $totalProducts = Product::count();
        $totalTransactions = Transaction::count();
        $lowStockProducts = Product::where('stock', '<=', 10)->count();

        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $monthlyRevenue = Transaction::whereYear('invoice_date', $currentYear)
            ->whereMonth('invoice_date', $currentMonth)
            ->sum('total');

        $todayTransactions = Transaction::whereDate('invoice_date', Carbon::today())->count();

        $recentTransactions = Transaction::with(['customer', 'detailTransactions'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($transaction) {
                return [
                    'id' => $transaction->id,
                    'invoice_number' => $transaction->invoice_number,
                    'customer_name' => $transaction->customer->name,
                    'total' => $transaction->total,
                    'invoice_date' => $transaction->invoice_date,
                    'items_count' => $transaction->detailTransactions->count(),
                ];
            });

        $topProducts = DB::table('detail_transactions')
            ->select('product_name', DB::raw('SUM(quantity) as total_sold'), DB::raw('SUM(amount) as total_revenue'))
            ->groupBy('product_name')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        $lowStockItems = Product::where('stock', '<=', 10)
            ->orderBy('stock', 'asc')
            ->limit(5)
            ->select('id', 'product_code', 'name', 'stock', 'price')
            ->get();

        return Inertia::render('Dashboard', [
            'stats' => [
                'totalCustomers' => $totalCustomers,
                'totalProducts' => $totalProducts,
                'totalTransactions' => $totalTransactions,
                'lowStockProducts' => $lowStockProducts,
                'monthlyRevenue' => $monthlyRevenue,
                'todayTransactions' => $todayTransactions,
            ],
            'recentTransactions' => $recentTransactions,
            'topProducts' => $topProducts,
            'lowStockItems' => $lowStockItems,
        ]);
    }
}
