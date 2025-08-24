<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import PlaceholderPattern from '@/components/PlaceholderPattern.vue';
import Label from '@/components/ui/label/Label.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { BreadcrumbItem, Session } from '@/types';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { InputNumber, InputText, useToast } from 'primevue';
import { computed, ComputedRef } from 'vue';

const toast = useToast();
const page = usePage();
const session: ComputedRef<Session> = computed(() => page.props.session || {});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Products',
        href: '/products',
    },
    {
        title: 'Create',
        href: '/products/create',
    },
];

const form = useForm({
    product_code: '',
    name: '',
    price: 0,
    stock: 0,
});

const submit = () => {
    form.post(route('products.store'), {
        onError: () => {
            toast.add({
                severity: 'error',
                summary: 'Error',
                detail: session.value.error || 'Failed to create product',
                life: 3000,
            });
        },
    });
};
</script>

<template>
    <Head title="Create Product" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-2 sm:p-4">
            <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 p-3 sm:p-5 md:min-h-min dark:border-sidebar-border">
                <PlaceholderPattern class="pointer-events-none" />

                <form @submit.prevent="submit" class="grid gap-6 rounded-md p-4">
                    <div class="mb-6">
                        <h2 class="text-xl font-semibold text-foreground">Create Product</h2>
                        <p class="text-muted-foreground">Create new product information</p>
                    </div>

                    <div class="grid gap-2">
                        <Label for="product-code">Product Code</Label>
                        <InputText
                            id="product-code"
                            type="text"
                            required
                            autofocus
                            :tabindex="1"
                            v-model="form.product_code"
                            placeholder="Product Code"
                        />
                        <InputError :message="form.errors.product_code" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="product-name">Product Name</Label>
                        <InputText id="product-name" type="text" required :tabindex="2" v-model="form.name" placeholder="Product Name" />
                        <InputError :message="form.errors.name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="price">Price (IDR)</Label>
                        <InputNumber
                            id="price"
                            v-model="form.price"
                            :min="0"
                            mode="currency"
                            currency="IDR"
                            locale="id-ID"
                            :minFractionDigits="0"
                            :maxFractionDigits="0"
                            placeholder="Price"
                            :tabindex="3"
                        />
                        <InputError :message="form.errors.price" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="stock">Stock</Label>
                        <InputNumber id="stock" v-model="form.stock" :min="0" :useGrouping="false" placeholder="Stock Quantity" :tabindex="4" />
                        <InputError :message="form.errors.stock" />
                    </div>

                    <div class="flex justify-end gap-4">
                        <button
                            type="button"
                            @click="$inertia.visit(route('products.index'))"
                            class="rounded-md bg-gray-200 px-4 py-2 text-gray-700 transition-colors hover:bg-gray-300"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="rounded-md bg-green-600 px-4 py-2 text-white transition-colors hover:bg-green-700 disabled:opacity-50"
                        >
                            {{ form.processing ? 'Creating...' : 'Create Product' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped></style>
