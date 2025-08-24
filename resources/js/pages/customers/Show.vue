<script setup lang="ts">
import PlaceholderPattern from '@/components/PlaceholderPattern.vue';
import Button from '@/components/ui/button/Button.vue';
import Card from '@/components/ui/card/Card.vue';
import CardContent from '@/components/ui/card/CardContent.vue';
import CardHeader from '@/components/ui/card/CardHeader.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { BreadcrumbItem } from '@/types';
import { Customer } from '@/types/customer';
import { Head, router } from '@inertiajs/vue3';
import { ArrowLeft, Edit, MapPin, User } from 'lucide-vue-next';
import { Divider } from 'primevue';

const props = defineProps<{
    customer: Customer;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Customers',
        href: '/customers',
    },
    {
        title: 'Detail',
        href: `/customers/${props.customer.id}`,
    },
];

const goBack = () => {
    router.visit(route('customers.index'));
};

const editCustomer = () => {
    router.visit(route('customers.edit', { customer: props.customer.id }));
};
</script>

<template>
    <Head :title="`Customer - ${customer.name}`" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-2 sm:p-4">
            <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 p-3 sm:p-5 md:min-h-min dark:border-sidebar-border">
                <PlaceholderPattern class="pointer-events-none" />

                <!-- Header Section -->
                <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center gap-3">
                        <Button variant="outline" size="sm" @click="goBack" class="cursor-pointer">
                            <ArrowLeft :size="16" />
                            Back
                        </Button>
                        <div>
                            <h1 class="text-2xl font-bold text-foreground">Customer Details</h1>
                            <p class="text-muted-foreground">View customer information</p>
                        </div>
                    </div>
                    <Button variant="default" size="lg" @click="editCustomer" class="cursor-pointer">
                        <Edit :size="16" />
                        Edit Customer
                    </Button>
                </div>

                <!-- Customer Information Cards -->
                <div class="grid gap-6 md:grid-cols-2">
                    <!-- Basic Information Card -->
                    <Card>
                        <CardHeader class="pb-3">
                            <div class="flex items-center gap-2">
                                <User :size="20" class="text-primary" />
                                <h3 class="text-lg font-semibold">Basic Information</h3>
                            </div>
                        </CardHeader>
                        <Divider />
                        <CardContent class="pt-4">
                            <div class="space-y-4">
                                <div>
                                    <label class="text-sm font-medium text-muted-foreground">Customer Code</label>
                                    <p class="mt-1 font-mono text-base text-foreground">{{ customer.customer_code }}</p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-muted-foreground">Customer Name</label>
                                    <p class="mt-1 text-base text-foreground">{{ customer.name }}</p>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Location Information Card -->
                    <Card v-if="customer.location">
                        <CardHeader class="pb-3">
                            <div class="flex items-center gap-2">
                                <MapPin :size="20" class="text-primary" />
                                <h3 class="text-lg font-semibold">Location Information</h3>
                            </div>
                        </CardHeader>
                        <Divider />
                        <CardContent class="pt-4">
                            <div class="space-y-4">
                                <div>
                                    <label class="text-sm font-medium text-muted-foreground">Address</label>
                                    <p class="mt-1 text-base text-foreground">{{ customer.location.address || 'N/A' }}</p>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="text-sm font-medium text-muted-foreground">Province</label>
                                        <p class="mt-1 text-sm text-foreground">{{ customer.location.province || 'N/A' }}</p>
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-muted-foreground">City</label>
                                        <p class="mt-1 text-sm text-foreground">{{ customer.location.cities || 'N/A' }}</p>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="text-sm font-medium text-muted-foreground">District</label>
                                        <p class="mt-1 text-sm text-foreground">{{ customer.location.district || 'N/A' }}</p>
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-muted-foreground">Sub District</label>
                                        <p class="mt-1 text-sm text-foreground">{{ customer.location.sub_district || 'N/A' }}</p>
                                    </div>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-muted-foreground">Postal Code</label>
                                    <p class="mt-1 font-mono text-base text-foreground">{{ customer.location.postal_code || 'N/A' }}</p>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <!-- Full Address Summary (Optional) -->
                <Card v-if="customer.location" class="mt-6">
                    <CardHeader class="pb-3">
                        <h3 class="text-lg font-semibold">Complete Address</h3>
                    </CardHeader>
                    <Divider />
                    <CardContent class="pt-4">
                        <p class="leading-relaxed text-foreground">
                            {{ customer.location.address }}
                            <template v-if="customer.location.sub_district">, {{ customer.location.sub_district }}</template>
                            <template v-if="customer.location.district">, {{ customer.location.district }}</template>
                            <template v-if="customer.location.cities">, {{ customer.location.cities }}</template>
                            <template v-if="customer.location.province">, {{ customer.location.province }}</template>
                            <template v-if="customer.location.postal_code">,  {{ customer.location.postal_code }}</template>
                        </p>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped></style>
