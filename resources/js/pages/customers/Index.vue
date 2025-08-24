<script setup lang="ts">
import PlaceholderPattern from '@/components/PlaceholderPattern.vue';
import Button from '@/components/ui/button/Button.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { BreadcrumbItem, Session } from '@/types';
import { Customer } from '@/types/customer';
import { Head, router, usePage } from '@inertiajs/vue3';
import { FilterMatchMode } from '@primevue/core/api';
import { ExternalLink, FilterX, Search } from 'lucide-vue-next';
import { Column, DataTable, Dialog, IconField, InputIcon, InputText, useToast } from 'primevue';
import { computed, ComputedRef, onMounted, ref } from 'vue';

const props = defineProps<{
    customers: Customer[];
}>();

const loading = ref(false);
const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});
const dt = ref<typeof DataTable | null>(null);
const showModals = ref(false);
const toast = useToast();
const page = usePage();
const selectedCustomerId = ref<number | null>(null);

const session: ComputedRef<Session> = computed(() => page.props.session || {});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Customers',
        href: '/customers',
    },
];

const customer = ref<Customer[]>(props.customers);

onMounted(() => {
    if (session.value.success) {
        toast.add({
            severity: 'success',
            summary: 'Success',
            detail: session.value.success,
            life: 3000,
        });
    }
});

const clearFilter = () => {
    filters.value.global.value = null;
};

const editHandler = (customerId: number) => {
    router.visit(route('customers.edit', { customer: customerId }));
};

const openDeleteModal = (customerId: number) => {
    selectedCustomerId.value = customerId;
    showModals.value = true;
};

const closeDeleteModal = () => {
    showModals.value = false;
    selectedCustomerId.value = null;
};

const deleteHandler = async (customerId: number) => {
    console.log('Deleting customer with ID:', customerId);

    router.delete(route('dashboard.customers.destroy', { customer: customerId }), {
        preserveScroll: true,
        onSuccess: () => {
            toast.add({
                severity: 'success',
                summary: 'Success',
                detail: session.value.success || 'Customer deleted successfully.',
                life: 3000,
            });

            customer.value = customer.value.filter((customer) => customer.id !== customerId);
        },
        onError: (errors) => {
            console.log('Session data:', session.value);
            console.error('Error deleting customer:', errors);
            toast.add({
                severity: 'error',
                summary: 'Error',
                detail: session.value.error || 'Failed to delete customer.',
                life: 3000,
            });
        },
        onFinish: () => {
            showModals.value = false;
        },
    });
};

const viewHandler = (customerId: number) => {
    router.visit(route('customers.show', { customer: customerId }));
};

const exportCSV = () => {
    if (dt.value) {
        (dt.value as any).exportCSV();
    }
};
</script>

<template>
    <Head title="Customers" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-2 sm:p-4">
            <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 p-3 sm:p-5 md:min-h-min dark:border-sidebar-border">
                <PlaceholderPattern class="pointer-events-none" />
                <Button
                    variant="default"
                    size="lg"
                    @click="() => $inertia.visit(route('customers.create'))"
                    class="mb-4 w-full cursor-pointer sm:w-auto"
                >
                    Add Customers
                </Button>

                <DataTable
                    v-model:filters="filters"
                    :value="customer"
                    tableStyle="min-width: 50rem"
                    showGridlines
                    dataKey="id"
                    :paginator="true"
                    :rows="5"
                    :loading="loading"
                    :global-filter-fields="['customer_code', 'name']"
                    class="mt-4"
                    ref="dt"
                    scrollable
                    scrollDirection="horizontal"
                    :scrollHeight="'400px'"
                    removableSort
                >
                    <template #header>
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                            <Button variant="outline" size="lg" class="w-full cursor-pointer sm:w-auto" @click="clearFilter">
                                <FilterX />
                                Clear
                            </Button>
                            <div class="flex flex-col gap-2 sm:flex-row sm:gap-2">
                                <IconField class="w-full sm:w-auto">
                                    <InputIcon>
                                        <Search :size="18" />
                                    </InputIcon>
                                    <InputText
                                        v-model="filters['global'].value"
                                        placeholder="Keyword Search"
                                        class="w-full min-w-[200px] sm:w-auto"
                                    />
                                </IconField>
                                <Button variant="secondary" size="lg" class="w-full cursor-pointer sm:w-auto" @click="exportCSV">
                                    <ExternalLink />
                                    Export
                                </Button>
                            </div>
                        </div>
                    </template>
                    <template #empty> No Customers found. </template>
                    <template #loading> Loading Customers data. Please wait. </template>
                    <Column header="No.">
                        <template #body="slotProps">
                            {{ slotProps.index + 1 }}
                        </template>
                    </Column>
                    <Column field="customer_code" sortable header="Customer Code">
                        <template #body="{ data }">
                            {{ data.customer_code }}
                        </template>
                    </Column>
                    <Column field="name" sortable header="Customer Name">
                        <template #body="{ data }">
                            {{ data.name || 'N/A' }}
                        </template>
                    </Column>
                    <Column header="Actions">
                        <template #body="{ data }">
                            <div class="flex flex-col gap-2 sm:flex-row sm:gap-2">
                                <Button variant="secondary" size="sm" @click="() => editHandler(data.id)" class="w-full cursor-pointer sm:w-auto">
                                    Edit
                                </Button>
                                <Button
                                    variant="destructive"
                                    size="sm"
                                    class="w-full cursor-pointer sm:w-auto"
                                    @click="() => openDeleteModal(data.id)"
                                >
                                    Delete
                                </Button>
                                <Button variant="default" size="sm" class="w-full cursor-pointer sm:w-auto" @click="() => viewHandler(data.id)">
                                    Detail
                                </Button>
                            </div>
                        </template>
                    </Column>
                </DataTable>
            </div>
            <Dialog v-model:visible="showModals" modal header="Delete Customer">
                <div class="flex flex-col gap-4">
                    <p>Are you sure you want to delete this customer?</p>
                    <Button variant="destructive" size="lg" @click="() => deleteHandler(selectedCustomerId!)" class="cursor-pointer">Confirm</Button>
                    <Button variant="secondary" size="lg" @click="closeDeleteModal">Cancel</Button>
                </div>
            </Dialog>
        </div>
    </AppLayout>
</template>

<style scoped></style>
