<script setup lang="ts">
import PlaceholderPattern from '@/components/PlaceholderPattern.vue';
import Button from '@/components/ui/button/Button.vue';
import Label from '@/components/ui/label/Label.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { BreadcrumbItem } from '@/types';
import { Product } from '@/types/product';
import { Head } from '@inertiajs/vue3';

const props = defineProps<{
    product: Product;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Products',
        href: '/products',
    },
    {
        title: 'Detail',
        href: `/products/${props.product.id}`,
    },
];

const formatCurrency = (value: number): string => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(value);
};

const formatDateTime = (dateString: string): string => {
    return new Intl.DateTimeFormat('id-ID', {
        dateStyle: 'long',
        timeStyle: 'short',
    }).format(new Date(dateString));
};
</script>

<template>
    <Head title="Product Detail" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-2 sm:p-4">
            <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 p-3 sm:p-5 md:min-h-min dark:border-sidebar-border">
                <PlaceholderPattern class="pointer-events-none" />

                <div class="grid gap-6 rounded-md p-4">
                    <div class="mb-6">
                        <h2 class="text-xl font-semibold text-foreground">Product Detail</h2>
                        <p class="text-muted-foreground">View product information</p>
                    </div>

                    <div class="grid gap-6 md:grid-cols-2">
                        <div class="grid gap-2">
                            <Label>Product Code</Label>
                            <div class="rounded-md border border-input bg-background px-3 py-2 text-sm">
                                {{ product.product_code }}
                            </div>
                        </div>

                        <div class="grid gap-2">
                            <Label>Product Name</Label>
                            <div class="rounded-md border border-input bg-background px-3 py-2 text-sm">
                                {{ product.name }}
                            </div>
                        </div>

                        <div class="grid gap-2">
                            <Label>Price</Label>
                            <div class="rounded-md border border-input bg-background px-3 py-2 text-sm font-semibold text-green-600">
                                {{ formatCurrency(product.price) }}
                            </div>
                        </div>

                        <div class="grid gap-2">
                            <Label>Stock</Label>
                            <div class="rounded-md border border-input bg-background px-3 py-2 text-sm">
                                <span :class="product.stock <= 10 ? 'font-semibold text-red-600' : ''">
                                    {{ product.stock }}
                                    <span v-if="product.stock <= 10" class="ml-2 text-xs text-red-500">(Low Stock)</span>
                                </span>
                            </div>
                        </div>

                        <div v-if="product.created_at" class="grid gap-2">
                            <Label>Created At</Label>
                            <div class="rounded-md border border-input bg-background px-3 py-2 text-sm text-muted-foreground">
                                {{ formatDateTime(product.created_at) }}
                            </div>
                        </div>

                        <div v-if="product.updated_at" class="grid gap-2">
                            <Label>Last Updated</Label>
                            <div class="rounded-md border border-input bg-background px-3 py-2 text-sm text-muted-foreground">
                                {{ formatDateTime(product.updated_at) }}
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-4">
                        <Button variant="outline" @click="$inertia.visit(route('products.index'))" class="cursor-pointer"> Back to Products </Button>
                        <Button variant="default" @click="$inertia.visit(route('products.edit', { product: product.id }))" class="cursor-pointer">
                            Edit Product
                        </Button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped></style>
