<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import PlaceholderPattern from '@/components/PlaceholderPattern.vue';
import Button from '@/components/ui/button/Button.vue';
import Label from '@/components/ui/label/Label.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { BreadcrumbItem } from '@/types';
import { Customer } from '@/types/customer';
import { Product } from '@/types/product';
import { CustomerDetails, ProductDetails, TransactionItem } from '@/types/transaction';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import axios from 'axios';
import { Trash2 } from 'lucide-vue-next';
import { DatePicker, InputNumber, InputText, Select, useToast } from 'primevue';
import { computed, ref, watch } from 'vue';

interface SelectOption {
    label: string;
    value: number;
}

const props = defineProps<{
    customers: Customer[];
    products: Product[];
}>();

const toast = useToast();
const page = usePage();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Transactions',
        href: '/transactions',
    },
    {
        title: 'Create',
        href: '/transactions/create',
    },
];

// Form data
const form = useForm({
    customer_id: null,
    invoice_date: new Date(),
    total: 0,
    items: [] as TransactionItem[],
});

// Customer and product options
const customerOptions = computed<SelectOption[]>(() =>
    props.customers.map((customer) => ({
        label: `${customer.customer_code} - ${customer.name}`,
        value: customer.id,
    })),
);

const productOptions = computed<SelectOption[]>(() =>
    props.products.map((product) => {
        const maxNameLength = 40;
        const truncatedName = product.name.length > maxNameLength ? product.name.substring(0, maxNameLength) + '...' : product.name;

        return {
            label: `${product.product_code} - ${truncatedName} (Stock: ${product.stock})`,
            value: product.id,
        };
    }),
);

// Selected customer and product details
const selectedCustomer = ref<CustomerDetails | null>(null);
const selectedProduct = ref<ProductDetails | null>(null);

// Current item being added
const currentItem = ref<TransactionItem>({
    product_id: 0,
    quantity: 1,
    price_at_time: 0,
    disc1: 0,
    disc2: 0,
    disc3: 0,
    net_price: 0,
    amount: 0,
});

// Watch for customer selection
watch(
    () => form.customer_id,
    async (newCustomerId) => {
        if (newCustomerId) {
            try {
                const response = await axios.get(`/api/customers/${newCustomerId}`);
                selectedCustomer.value = response.data;
            } catch (error) {
                console.error('Error fetching customer details:', error);
                toast.add({
                    severity: 'error',
                    summary: 'Error',
                    detail: 'Failed to fetch customer details',
                    life: 3000,
                });
            }
        } else {
            selectedCustomer.value = null;
        }
    },
);

// Watch for product selection in current item
watch(
    () => currentItem.value.product_id,
    async (newProductId) => {
        if (newProductId) {
            try {
                const response = await axios.get(`/api/products/${newProductId}`);
                selectedProduct.value = response.data;
                currentItem.value.price_at_time = response.data.price;
                calculateNetPriceAndAmount();
            } catch (error) {
                console.error('Error fetching product details:', error);
                toast.add({
                    severity: 'error',
                    summary: 'Error',
                    detail: 'Failed to fetch product details',
                    life: 3000,
                });
            }
        } else {
            selectedProduct.value = null;
            currentItem.value.price_at_time = 0;
            calculateNetPriceAndAmount();
        }
    },
);

// Calculate net price and amount for current item
const calculateNetPriceAndAmount = () => {
    let netPrice = currentItem.value.price_at_time;

    // Apply discounts in sequence
    netPrice -= netPrice * (currentItem.value.disc1 / 100);
    netPrice -= netPrice * (currentItem.value.disc2 / 100);
    netPrice -= netPrice * (currentItem.value.disc3 / 100);

    currentItem.value.net_price = Math.round(netPrice);
    currentItem.value.amount = currentItem.value.net_price * currentItem.value.quantity;
};

// Watch for changes in pricing to recalculate
watch(
    [
        () => currentItem.value.quantity,
        () => currentItem.value.price_at_time,
        () => currentItem.value.disc1,
        () => currentItem.value.disc2,
        () => currentItem.value.disc3,
    ],
    calculateNetPriceAndAmount,
);

// Add item to transaction
const addItem = () => {
    if (!currentItem.value.product_id) {
        toast.add({
            severity: 'error',
            summary: 'Error',
            detail: 'Please select a product',
            life: 3000,
        });
        return;
    }

    if (!selectedProduct.value) {
        toast.add({
            severity: 'error',
            summary: 'Error',
            detail: 'Product details not loaded',
            life: 3000,
        });
        return;
    }

    if (currentItem.value.quantity <= 0) {
        toast.add({
            severity: 'error',
            summary: 'Error',
            detail: 'Quantity must be greater than 0',
            life: 3000,
        });
        return;
    }

    if (currentItem.value.quantity > selectedProduct.value.stock) {
        toast.add({
            severity: 'error',
            summary: 'Error',
            detail: `Insufficient stock. Available: ${selectedProduct.value.stock}`,
            life: 3000,
        });
        return;
    }

    // Check if product already exists in items
    const existingItemIndex = form.items.findIndex((item) => item.product_id === currentItem.value.product_id);

    if (existingItemIndex >= 0) {
        const totalQuantity = form.items[existingItemIndex].quantity + currentItem.value.quantity;
        if (totalQuantity > selectedProduct.value.stock) {
            toast.add({
                severity: 'error',
                summary: 'Error',
                detail: `Total quantity would exceed stock. Available: ${selectedProduct.value.stock}`,
                life: 3000,
            });
            return;
        }

        // Update existing item
        form.items[existingItemIndex] = { ...currentItem.value };
    } else {
        // Add new item
        form.items.push({ ...currentItem.value });
    }

    // Reset current item
    resetCurrentItem();

    // Recalculate total
    calculateTotal();

    toast.add({
        severity: 'success',
        summary: 'Success',
        detail: 'Item added to transaction',
        life: 2000,
    });
};

// Remove item from transaction
const removeItem = (index: number) => {
    form.items.splice(index, 1);
    calculateTotal();
};

// Reset current item
const resetCurrentItem = () => {
    currentItem.value = {
        product_id: 0,
        quantity: 1,
        price_at_time: 0,
        disc1: 0,
        disc2: 0,
        disc3: 0,
        net_price: 0,
        amount: 0,
    };
    selectedProduct.value = null;
};

// Calculate transaction total
const calculateTotal = () => {
    form.total = form.items.reduce((sum, item) => sum + item.amount, 0);
};

// Get product details for display in items table
const getProductDetails = (productId: number) => {
    return props.products.find((p) => p.id === productId);
};

// Format currency
const formatCurrency = (value: number): string => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(value);
};

// Submit form
const submit = () => {
    if (!form.customer_id) {
        toast.add({
            severity: 'error',
            summary: 'Error',
            detail: 'Please select a customer',
            life: 3000,
        });
        return;
    }

    if (form.items.length === 0) {
        toast.add({
            severity: 'error',
            summary: 'Error',
            detail: 'Please add at least one item',
            life: 3000,
        });
        return;
    }

    // Format the date properly for submission
    const formattedData = {
        ...form.data(),
        invoice_date: form.invoice_date.toISOString().split('T')[0], // Convert to YYYY-MM-DD format
    };

    // Debug log to check form data
    console.log('Form data being submitted:', formattedData);

    form.transform(() => formattedData).post(route('transactions.store'), {
        onSuccess: () => {
            toast.add({
                severity: 'success',
                summary: 'Success',
                detail: 'Transaction created successfully',
                life: 3000,
            });
        },
        onError: (errors) => {
            console.log('Form errors:', errors);
            console.log('Page props:', page.props);

            const sessionData = page.props.session as any;
            const errorMessage = sessionData?.error || 'Failed to create transaction. Please check the form.';

            toast.add({
                severity: 'error',
                summary: 'Error',
                detail: errorMessage,
                life: 5000,
            });
        },
    });
};
</script>

<template>
    <Head title="Create Transaction" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-2 sm:p-4">
            <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 p-3 sm:p-5 md:min-h-min dark:border-sidebar-border">
                <PlaceholderPattern class="pointer-events-none" />

                <form @submit.prevent="submit" class="grid gap-6 rounded-md p-4">
                    <div class="mb-6">
                        <h2 class="text-xl font-semibold text-foreground">Create Transaction</h2>
                        <p class="text-muted-foreground">Create new transaction</p>
                    </div>

                    <!-- Customer Selection -->
                    <div class="grid gap-2">
                        <Label for="customer">Customer</Label>
                        <Select
                            id="customer"
                            v-model="form.customer_id"
                            :options="customerOptions"
                            optionLabel="label"
                            optionValue="value"
                            placeholder="Select Customer"
                            class="w-full"
                            filter
                        />
                        <InputError :message="form.errors.customer_id" />
                    </div>

                    <!-- Customer Details Display -->
                    <div v-if="selectedCustomer" class="grid gap-2">
                        <Label>Customer Details</Label>
                        <div class="rounded-md border p-3">
                            <p><strong>Code:</strong> {{ selectedCustomer.customer_code }}</p>
                            <p><strong>Name:</strong> {{ selectedCustomer.name }}</p>
                            <p><strong>Address:</strong> {{ selectedCustomer.address }}</p>
                        </div>
                    </div>

                    <!-- Invoice Date -->
                    <div class="grid gap-2">
                        <Label for="invoice-date">Invoice Date</Label>
                        <DatePicker id="invoice-date" v-model="form.invoice_date" dateFormat="dd/mm/yy" placeholder="Select date" />
                        <InputError :message="form.errors.invoice_date" />
                    </div>

                    <!-- Add Item Section -->
                    <div class="border-t pt-6">
                        <h3 class="mb-4 text-lg font-semibold">Add Items</h3>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                            <!-- Product Selection -->
                            <div class="grid gap-2">
                                <Label for="product">Product</Label>
                                <div class="relative">
                                    <Select
                                        id="product"
                                        v-model="currentItem.product_id"
                                        :options="productOptions"
                                        optionLabel="label"
                                        optionValue="value"
                                        placeholder="Select Product"
                                        class="w-full"
                                        filter
                                        :filterPlaceholder="'Search products...'"
                                    />
                                </div>
                            </div>

                            <!-- Quantity -->
                            <div class="grid gap-2">
                                <Label for="quantity">Quantity</Label>
                                <InputNumber
                                    id="quantity"
                                    v-model="currentItem.quantity"
                                    :min="1"
                                    :max="selectedProduct?.stock || 999"
                                    :useGrouping="false"
                                    placeholder="Quantity"
                                />
                                <small v-if="selectedProduct" class="text-muted-foreground"> Available stock: {{ selectedProduct.stock }} </small>
                            </div>

                            <!-- Price -->
                            <div class="grid gap-2">
                                <Label for="price">Price</Label>
                                <InputNumber
                                    id="price"
                                    v-model="currentItem.price_at_time"
                                    :min="0"
                                    mode="currency"
                                    currency="IDR"
                                    locale="id-ID"
                                    :minFractionDigits="0"
                                    :maxFractionDigits="0"
                                    placeholder="Price"
                                />
                            </div>

                            <!-- Discount 1 -->
                            <div class="grid gap-2">
                                <Label for="disc1">Discount 1 (%)</Label>
                                <InputNumber
                                    id="disc1"
                                    v-model="currentItem.disc1"
                                    :min="0"
                                    :max="100"
                                    :minFractionDigits="0"
                                    :maxFractionDigits="2"
                                    placeholder="Discount 1"
                                    suffix="%"
                                />
                            </div>

                            <!-- Discount 2 -->
                            <div class="grid gap-2">
                                <Label for="disc2">Discount 2 (%)</Label>
                                <InputNumber
                                    id="disc2"
                                    v-model="currentItem.disc2"
                                    :min="0"
                                    :max="100"
                                    :minFractionDigits="0"
                                    :maxFractionDigits="2"
                                    placeholder="Discount 2"
                                    suffix="%"
                                />
                            </div>

                            <!-- Discount 3 -->
                            <div class="grid gap-2">
                                <Label for="disc3">Discount 3 (%)</Label>
                                <InputNumber
                                    id="disc3"
                                    v-model="currentItem.disc3"
                                    :min="0"
                                    :max="100"
                                    :minFractionDigits="0"
                                    :maxFractionDigits="2"
                                    placeholder="Discount 3"
                                    suffix="%"
                                />
                            </div>
                        </div>

                        <!-- Net Price and Amount Display -->
                        <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div class="grid gap-2">
                                <Label>Net Price</Label>
                                <InputText :value="formatCurrency(currentItem.net_price)" readonly class="bg-muted" />
                            </div>
                            <div class="grid gap-2">
                                <Label>Amount</Label>
                                <InputText :value="formatCurrency(currentItem.amount)" readonly class="bg-muted" />
                            </div>
                        </div>

                        <!-- Add Item Button -->
                        <div class="mt-4">
                            <Button type="button" @click="addItem" variant="outline" class="w-full sm:w-auto"> Add Item </Button>
                        </div>
                    </div>

                    <!-- Items Table -->
                    <div v-if="form.items.length > 0" class="border-t pt-6">
                        <h3 class="mb-4 text-lg font-semibold">Transaction Items</h3>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Product</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Qty</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Price</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Disc1</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Disc2</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Disc3</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Net Price</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Amount</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white">
                                    <tr v-for="(item, index) in form.items" :key="index">
                                        <td class="px-6 py-4 text-sm whitespace-nowrap text-gray-900">
                                            {{ getProductDetails(item.product_id)?.product_code }} -
                                            {{ getProductDetails(item.product_id)?.name }}
                                        </td>
                                        <td class="px-6 py-4 text-sm whitespace-nowrap text-gray-900">{{ item.quantity }}</td>
                                        <td class="px-6 py-4 text-sm whitespace-nowrap text-gray-900">{{ formatCurrency(item.price_at_time) }}</td>
                                        <td class="px-6 py-4 text-sm whitespace-nowrap text-gray-900">{{ item.disc1 }}%</td>
                                        <td class="px-6 py-4 text-sm whitespace-nowrap text-gray-900">{{ item.disc2 }}%</td>
                                        <td class="px-6 py-4 text-sm whitespace-nowrap text-gray-900">{{ item.disc3 }}%</td>
                                        <td class="px-6 py-4 text-sm whitespace-nowrap text-gray-900">{{ formatCurrency(item.net_price) }}</td>
                                        <td class="px-6 py-4 text-sm whitespace-nowrap text-gray-900">{{ formatCurrency(item.amount) }}</td>
                                        <td class="px-6 py-4 text-sm whitespace-nowrap text-gray-900">
                                            <Button type="button" @click="removeItem(index)" variant="destructive" size="sm">
                                                <Trash2 :size="16" />
                                            </Button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Total -->
                        <div class="mt-4 text-right">
                            <div class="text-xl font-semibold">Total: {{ formatCurrency(form.total) }}</div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-end gap-4 border-t pt-6">
                        <button
                            type="button"
                            @click="$inertia.visit(route('transactions.index'))"
                            class="rounded-md bg-gray-200 px-4 py-2 text-gray-700 transition-colors hover:bg-gray-300"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            :disabled="form.processing || form.items.length === 0"
                            class="rounded-md bg-green-600 px-4 py-2 text-white transition-colors hover:bg-green-700 disabled:opacity-50"
                        >
                            {{ form.processing ? 'Creating...' : 'Create Transaction' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
table {
    font-size: 0.875rem;
}

th,
td {
    padding: 0.5rem;
}

@media (max-width: 768px) {
    table {
        font-size: 0.75rem;
    }

    th,
    td {
        padding: 0.25rem;
    }
}

/* Fix for Select dropdown responsiveness */
:deep(.p-select) {
    width: 100%;
    position: relative;
}

:deep(.p-select .p-inputtext) {
    width: 100%;
    box-sizing: border-box;
    min-height: 2.5rem;
    line-height: 1.5;
}

:deep(.p-select-label) {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    max-width: calc(100% - 2rem);
    display: block;
}

:deep(.p-select-overlay) {
    max-width: 90vw;
    width: auto;
    min-width: 300px;
    z-index: 9999;
    position: absolute;
}

:deep(.p-select-option) {
    padding: 0.5rem 0.75rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    box-sizing: border-box;
}

:deep(.p-select-option:hover) {
    background-color: #f3f4f6;
}

/* Prevent layout shift */
:deep(.p-select .p-select-dropdown) {
    position: relative;
    flex-shrink: 0;
}

/* Stabilize grid container */
.grid.gap-2 {
    position: relative;
}

.grid.gap-2 .relative {
    position: relative;
    width: 100%;
}

/* Mobile responsiveness */
@media (max-width: 640px) {
    :deep(.p-select-overlay) {
        max-width: 95vw;
        min-width: 250px;
    }

    :deep(.p-select-option) {
        font-size: 0.875rem;
        padding: 0.75rem 0.5rem;
    }
}
</style>
