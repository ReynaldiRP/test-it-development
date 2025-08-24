<script setup lang="ts">
import PlaceholderPattern from '@/components/PlaceholderPattern.vue';
import Button from '@/components/ui/button/Button.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { BreadcrumbItem, Session } from '@/types';
import { Transaction } from '@/types/transaction';
import { Head, router, usePage } from '@inertiajs/vue3';
import { FilterMatchMode } from '@primevue/core/api';
import { ExternalLink, FilterX, Search } from 'lucide-vue-next';
import { Column, DataTable, Dialog, IconField, InputIcon, InputText, useToast } from 'primevue';
import { computed, ComputedRef, onMounted, ref } from 'vue';

const props = defineProps<{
    transactions: Transaction[];
}>();

const loading = ref(false);
const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});
const dt = ref<typeof DataTable | null>(null);
const showModals = ref(false);
const toast = useToast();
const page = usePage();
const selectedTransactionId = ref<number | null>(null);

const session: ComputedRef<Session> = computed(() => page.props.session || {});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Transactions',
        href: '/transactions',
    },
];

const transactions = ref<Transaction[]>(props.transactions);

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

const editHandler = (transactionId: number) => {
    router.visit(route('transactions.edit', { transaction: transactionId }));
};

const openDeleteModal = (transactionId: number) => {
    selectedTransactionId.value = transactionId;
    showModals.value = true;
};

const closeDeleteModal = () => {
    showModals.value = false;
    selectedTransactionId.value = null;
};

const deleteHandler = async (transactionId: number) => {
    console.log('Deleting transaction with ID:', transactionId);

    router.delete(route('transactions.destroy', { transaction: transactionId }), {
        preserveScroll: true,
        onSuccess: () => {
            toast.add({
                severity: 'success',
                summary: 'Success',
                detail: session.value.success || 'Transaction deleted successfully.',
                life: 3000,
            });

            transactions.value = transactions.value.filter((transaction) => transaction.id !== transactionId);
        },
        onError: (errors) => {
            console.log('Session data:', session.value);
            console.error('Error deleting transaction:', errors);
            toast.add({
                severity: 'error',
                summary: 'Error',
                detail: errors.error || 'Failed to delete transaction.',
                life: 3000,
            });
        },
        onFinish: () => {
            showModals.value = false;
        },
    });
};

const viewHandler = (transactionId: number) => {
    router.visit(route('transactions.show', { transaction: transactionId }));
};

const exportCSV = () => {
    if (dt.value) {
        (dt.value as any).exportCSV();
    }
};

const formatCurrency = (value: number): string => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(value);
};

const formatDate = (dateString: string): string => {
    return new Date(dateString).toLocaleDateString('id-ID');
};
</script>

<template>
    <Head title="Transactions" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-2 sm:p-4">
            <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 p-3 sm:p-5 md:min-h-min dark:border-sidebar-border">
                <PlaceholderPattern class="pointer-events-none" />
                <Button
                    variant="default"
                    size="lg"
                    @click="() => $inertia.visit(route('transactions.create'))"
                    class="mb-4 w-full cursor-pointer sm:w-auto"
                >
                    Create Transaction
                </Button>

                <DataTable
                    v-model:filters="filters"
                    :value="transactions"
                    tableStyle="min-width: 50rem"
                    showGridlines
                    dataKey="id"
                    :paginator="true"
                    :rows="5"
                    :loading="loading"
                    :global-filter-fields="['invoice_number', 'customer.name', 'customer.customer_code']"
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
                                        placeholder="Search transactions..."
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
                    <template #empty> No Transactions found. </template>
                    <template #loading> Loading Transactions data. Please wait. </template>
                    <Column header="No.">
                        <template #body="slotProps">
                            {{ slotProps.index + 1 }}
                        </template>
                    </Column>
                    <Column field="invoice_number" sortable header="Invoice Number">
                        <template #body="{ data }">
                            {{ data.invoice_number }}
                        </template>
                    </Column>
                    <Column field="invoice_date" sortable header="Invoice Date">
                        <template #body="{ data }">
                            {{ formatDate(data.invoice_date) }}
                        </template>
                    </Column>
                    <Column field="customer.customer_code" sortable header="Customer Code">
                        <template #body="{ data }">
                            {{ data.customer?.customer_code || 'N/A' }}
                        </template>
                    </Column>
                    <Column field="customer.name" sortable header="Customer Name">
                        <template #body="{ data }">
                            {{ data.customer?.name || 'N/A' }}
                        </template>
                    </Column>
                    <Column field="total" sortable header="Total">
                        <template #body="{ data }">
                            {{ formatCurrency(data.total) }}
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
            <Dialog v-model:visible="showModals" modal header="Delete Transaction">
                <div class="flex flex-col gap-4">
                    <p>Are you sure you want to delete this transaction?</p>
                    <p class="text-sm text-muted-foreground">This action cannot be undone and will affect product stock.</p>
                    <Button variant="destructive" size="lg" @click="() => deleteHandler(selectedTransactionId!)" class="cursor-pointer"
                        >Confirm</Button
                    >
                    <Button variant="secondary" size="lg" @click="closeDeleteModal">Cancel</Button>
                </div>
            </Dialog>
        </div>
    </AppLayout>
</template>

<style scoped></style>
