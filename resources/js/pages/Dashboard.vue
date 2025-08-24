<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { BreadcrumbItem } from '@/types';
import { Props } from '@/types/dashboard';
import { Head, Link } from '@inertiajs/vue3';
import { BarChart3, Package, ShoppingCart, TrendingDown, TrendingUp, Users } from 'lucide-vue-next';

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

const formatCurrency = (value: number): string => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(value);
};

const formatDate = (date: string): string => {
    return new Date(date).toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
};
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-4">
            <!-- Welcome Section -->
            <div class="rounded-xl border border-sidebar-border/70 bg-card p-6 dark:border-sidebar-border">
                <h1 class="text-2xl font-bold text-foreground">Welcome to Transaction Management</h1>
                <p class="mt-2 text-muted-foreground">Monitor your business performance and manage your inventory</p>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <!-- Total Customers -->
                <div class="rounded-xl border border-sidebar-border/70 bg-card p-6 shadow-sm dark:border-sidebar-border">
                    <div class="flex items-center">
                        <div class="rounded-lg bg-blue-100 p-3 dark:bg-blue-900/50">
                            <Users :size="24" class="text-blue-600 dark:text-blue-400" />
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-muted-foreground">Total Customers</p>
                            <p class="text-2xl font-bold text-foreground">{{ props.stats.totalCustomers }}</p>
                        </div>
                    </div>
                </div>

                <!-- Total Products -->
                <div class="rounded-xl border border-sidebar-border/70 bg-card p-6 shadow-sm dark:border-sidebar-border">
                    <div class="flex items-center">
                        <div class="rounded-lg bg-green-100 p-3 dark:bg-green-900/50">
                            <Package :size="24" class="text-green-600 dark:text-green-400" />
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-muted-foreground">Total Products</p>
                            <p class="text-2xl font-bold text-foreground">{{ props.stats.totalProducts }}</p>
                        </div>
                    </div>
                </div>

                <!-- Total Transactions -->
                <div class="rounded-xl border border-sidebar-border/70 bg-card p-6 shadow-sm dark:border-sidebar-border">
                    <div class="flex items-center">
                        <div class="rounded-lg bg-purple-100 p-3 dark:bg-purple-900/50">
                            <ShoppingCart :size="24" class="text-purple-600 dark:text-purple-400" />
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-muted-foreground">Total Transactions</p>
                            <p class="text-2xl font-bold text-foreground">{{ props.stats.totalTransactions }}</p>
                        </div>
                    </div>
                </div>

                <!-- Monthly Revenue -->
                <div class="rounded-xl border border-sidebar-border/70 bg-card p-6 shadow-sm dark:border-sidebar-border">
                    <div class="flex items-center">
                        <div class="rounded-lg bg-emerald-100 p-3 dark:bg-emerald-900/50">
                            <TrendingUp :size="24" class="text-emerald-600 dark:text-emerald-400" />
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-muted-foreground">Monthly Revenue</p>
                            <p class="text-2xl font-bold text-foreground">{{ formatCurrency(props.stats.monthlyRevenue) }}</p>
                        </div>
                    </div>
                </div>

                <!-- Today's Transactions -->
                <div class="rounded-xl border border-sidebar-border/70 bg-card p-6 shadow-sm dark:border-sidebar-border">
                    <div class="flex items-center">
                        <div class="rounded-lg bg-orange-100 p-3 dark:bg-orange-900/50">
                            <BarChart3 :size="24" class="text-orange-600 dark:text-orange-400" />
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-muted-foreground">Today's Transactions</p>
                            <p class="text-2xl font-bold text-foreground">{{ props.stats.todayTransactions }}</p>
                        </div>
                    </div>
                </div>

                <!-- Low Stock Alert -->
                <div class="rounded-xl border border-sidebar-border/70 bg-card p-6 shadow-sm dark:border-sidebar-border">
                    <div class="flex items-center">
                        <div class="rounded-lg bg-red-100 p-3 dark:bg-red-900/50">
                            <TrendingDown :size="24" class="text-red-600 dark:text-red-400" />
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-muted-foreground">Low Stock Items</p>
                            <p class="text-2xl font-bold text-foreground">{{ props.stats.lowStockProducts }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <!-- Recent Transactions -->
                <div class="rounded-xl border border-sidebar-border/70 bg-card p-6 shadow-sm dark:border-sidebar-border">
                    <div class="mb-4 flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-foreground">Recent Transactions</h3>
                        <Link
                            :href="route('transactions.index')"
                            class="text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300"
                        >
                            View All
                        </Link>
                    </div>
                    <div class="space-y-3">
                        <div
                            v-for="transaction in props.recentTransactions"
                            :key="transaction.id"
                            class="flex items-center justify-between rounded-lg border border-sidebar-border/70 p-3 dark:border-sidebar-border"
                        >
                            <div>
                                <p class="font-medium text-foreground">{{ transaction.invoice_number }}</p>
                                <p class="text-sm text-muted-foreground">{{ transaction.customer_name }}</p>
                                <p class="text-xs text-muted-foreground">{{ formatDate(transaction.invoice_date) }}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-medium text-foreground">{{ formatCurrency(transaction.total) }}</p>
                                <p class="text-xs text-muted-foreground">{{ transaction.items_count }} items</p>
                            </div>
                        </div>
                    </div>
                    <div v-if="props.recentTransactions.length === 0" class="py-8 text-center">
                        <p class="text-muted-foreground">No transactions yet</p>
                    </div>
                </div>

                <!-- Top Products -->
                <div class="rounded-xl border border-sidebar-border/70 bg-card p-6 shadow-sm dark:border-sidebar-border">
                    <div class="mb-4 flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-foreground">Top Selling Products</h3>
                        <Link
                            :href="route('products.index')"
                            class="text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300"
                        >
                            View All
                        </Link>
                    </div>
                    <div class="space-y-3">
                        <div
                            v-for="(product, index) in props.topProducts"
                            :key="product.product_name"
                            class="flex items-center justify-between rounded-lg border border-sidebar-border/70 p-3 dark:border-sidebar-border"
                        >
                            <div class="flex items-center">
                                <span
                                    class="mr-3 flex h-6 w-6 items-center justify-center rounded-full bg-muted text-xs font-medium text-muted-foreground"
                                >
                                    {{ index + 1 }}
                                </span>
                                <div>
                                    <p class="font-medium text-foreground">{{ product.product_name }}</p>
                                    <p class="text-sm text-muted-foreground">{{ product.total_sold }} sold</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="font-medium text-foreground">{{ formatCurrency(product.total_revenue) }}</p>
                            </div>
                        </div>
                    </div>
                    <div v-if="props.topProducts.length === 0" class="py-8 text-center">
                        <p class="text-muted-foreground">No sales data yet</p>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="rounded-xl border border-sidebar-border/70 bg-card p-6 shadow-sm dark:border-sidebar-border">
                <h3 class="mb-4 text-lg font-semibold text-foreground">Quick Actions</h3>
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                    <Link
                        :href="route('transactions.create')"
                        class="flex items-center justify-center rounded-lg border border-green-200 bg-green-50 p-4 text-green-800 transition-colors hover:bg-green-100 dark:border-green-800 dark:bg-green-900/20 dark:text-green-300 dark:hover:bg-green-900/30"
                    >
                        <ShoppingCart :size="20" class="mr-2" />
                        New Transaction
                    </Link>
                    <Link
                        :href="route('products.create')"
                        class="flex items-center justify-center rounded-lg border border-blue-200 bg-blue-50 p-4 text-blue-800 transition-colors hover:bg-blue-100 dark:border-blue-800 dark:bg-blue-900/20 dark:text-blue-300 dark:hover:bg-blue-900/30"
                    >
                        <Package :size="20" class="mr-2" />
                        Add Product
                    </Link>
                    <Link
                        :href="route('customers.create')"
                        class="flex items-center justify-center rounded-lg border border-purple-200 bg-purple-50 p-4 text-purple-800 transition-colors hover:bg-purple-100 dark:border-purple-800 dark:bg-purple-900/20 dark:text-purple-300 dark:hover:bg-purple-900/30"
                    >
                        <Users :size="20" class="mr-2" />
                        Add Customer
                    </Link>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
