<script setup lang="ts">
import PlaceholderPattern from '@/components/PlaceholderPattern.vue';
import Button from '@/components/ui/button/Button.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { BreadcrumbItem } from '@/types';
import { Transaction } from '@/types/transaction';
import { Head } from '@inertiajs/vue3';
import { Column, DataTable } from 'primevue';

const props = defineProps<{
    transaction: Transaction;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Transactions',
        href: '/transactions',
    },
    {
        title: 'Detail',
        href: `/transactions/${props.transaction.id}`,
    },
];

const formatCurrency = (value: number): string => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(value);
};

const formatDate = (dateString: string): string => {
    return new Date(dateString).toLocaleDateString('id-ID', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
};

const printInvoice = () => {
    window.print();
};
</script>

<template>
    <Head title="Transaction Detail" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-2 sm:p-4">
            <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 p-3 sm:p-5 md:min-h-min dark:border-sidebar-border">
                <PlaceholderPattern class="pointer-events-none" />

                <div class="grid gap-6 rounded-md p-4">
                    <div class="mb-6">
                        <h2 class="text-xl font-semibold text-foreground">Transaction Detail</h2>
                        <p class="text-muted-foreground">Invoice #{{ transaction.invoice_number }}</p>
                    </div>

                    <!-- Transaction Header Info -->
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <!-- Customer Information -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold">Customer Information</h3>
                            <div class="space-y-2 rounded-md border p-4">
                                <div>
                                    <span class="font-medium">Code:</span>
                                    <span class="ml-2">{{ transaction.customer?.customer_code || 'N/A' }}</span>
                                </div>
                                <div>
                                    <span class="font-medium">Name:</span>
                                    <span class="ml-2">{{ transaction.customer?.name || 'N/A' }}</span>
                                </div>
                                <div v-if="transaction.customer?.location">
                                    <span class="font-medium">Address:</span>
                                    <span class="ml-2">
                                        {{ transaction.customer.location.address }}, {{ transaction.customer.location.cities }},
                                        {{ transaction.customer.location.province }}
                                        {{ transaction.customer.location.postal_code }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Transaction Information -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold">Transaction Information</h3>
                            <div class="space-y-2 rounded-md border p-4">
                                <div>
                                    <span class="font-medium">Invoice Number:</span>
                                    <span class="ml-2">{{ transaction.invoice_number }}</span>
                                </div>
                                <div>
                                    <span class="font-medium">Invoice Date:</span>
                                    <span class="ml-2">{{ formatDate(transaction.invoice_date) }}</span>
                                </div>
                                <div>
                                    <span class="font-medium">Total Amount:</span>
                                    <span class="ml-2 text-lg font-semibold text-green-600">{{ formatCurrency(transaction.total) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Transaction Items -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold">Transaction Items</h3>

                        <DataTable
                            :value="transaction.detail_transactions"
                            tableStyle="min-width: 50rem"
                            showGridlines
                            dataKey="id"
                            class="mt-4"
                            scrollable
                            scrollDirection="horizontal"
                        >
                            <template #empty> No transaction items found. </template>
                            <Column header="No.">
                                <template #body="slotProps">
                                    {{ slotProps.index + 1 }}
                                </template>
                            </Column>
                            <Column field="product_code" header="Product Code">
                                <template #body="{ data }">
                                    {{ data.product_code }}
                                </template>
                            </Column>
                            <Column field="product_name" header="Product Name">
                                <template #body="{ data }">
                                    {{ data.product_name }}
                                </template>
                            </Column>
                            <Column field="quantity" header="Qty">
                                <template #body="{ data }">
                                    {{ data.quantity }}
                                </template>
                            </Column>
                            <Column field="price_at_time" header="Price">
                                <template #body="{ data }">
                                    {{ formatCurrency(data.price_at_time) }}
                                </template>
                            </Column>
                            <Column field="disc1" header="Disc1">
                                <template #body="{ data }"> {{ data.disc1 }}% </template>
                            </Column>
                            <Column field="disc2" header="Disc2">
                                <template #body="{ data }"> {{ data.disc2 }}% </template>
                            </Column>
                            <Column field="disc3" header="Disc3">
                                <template #body="{ data }"> {{ data.disc3 }}% </template>
                            </Column>
                            <Column field="net_price" header="Net Price">
                                <template #body="{ data }">
                                    {{ formatCurrency(data.net_price) }}
                                </template>
                            </Column>
                            <Column field="amount" header="Amount">
                                <template #body="{ data }">
                                    <span class="font-medium">
                                        {{ formatCurrency(data.amount) }}
                                    </span>
                                </template>
                            </Column>
                        </DataTable>

                        <!-- Total Row -->
                        <div class="mt-4 flex justify-end">
                            <div class="rounded-md border bg-gray-50 p-4">
                                <div class="flex items-center gap-4">
                                    <span class="text-right font-medium text-gray-900">Total:</span>
                                    <span class="text-lg font-bold text-green-600">
                                        {{ formatCurrency(transaction.total) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-between gap-4 border-t pt-6">
                        <Button variant="outline" @click="$inertia.visit(route('transactions.index'))"> Back to List </Button>

                        <div class="flex gap-2">
                            <Button variant="secondary" @click="$inertia.visit(route('transactions.edit', { transaction: transaction.id }))">
                                Edit Transaction
                            </Button>
                            <Button variant="default" @click="printInvoice"> Print Invoice </Button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
@media print {
    .no-print {
        display: none !important;
    }
}

table {
    font-size: 0.875rem;
}

@media (max-width: 768px) {
    table {
        font-size: 0.75rem;
    }
}
</style>
