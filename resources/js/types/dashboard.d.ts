export interface Stats {
    totalCustomers: number;
    totalProducts: number;
    totalTransactions: number;
    lowStockProducts: number;
    monthlyRevenue: number;
    todayTransactions: number;
}

export interface RecentTransaction {
    id: number;
    invoice_number: string;
    customer_name: string;
    total: number;
    invoice_date: string;
    items_count: number;
}

export interface TopProduct {
    product_name: string;
    total_sold: number;
    total_revenue: number;
}

export interface Props {
    stats: Stats;
    recentTransactions: RecentTransaction[];
    topProducts: TopProduct[];
}
