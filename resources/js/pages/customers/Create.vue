<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import PlaceholderPattern from '@/components/PlaceholderPattern.vue';
import Label from '@/components/ui/label/Label.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { BreadcrumbItem, Session } from '@/types';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import axios from 'axios';
import { InputText, Select, useToast } from 'primevue';
import { computed, ComputedRef, ref, watch } from 'vue';

// Define the select option interface
interface SelectOption {
    label: string;
    value: string;
    id: number;
}

const props = defineProps<{
    provinces: SelectOption[];
}>();

const toast = useToast();
const page = usePage();
const session: ComputedRef<Session> = computed(() => page.props.session || {});

// Reactive data for cascading dropdowns
const cities = ref<SelectOption[]>([]);
const districts = ref<SelectOption[]>([]);
const subdistricts = ref<SelectOption[]>([]);
const loadingCities = ref(false);
const loadingDistricts = ref(false);
const loadingSubdistricts = ref(false);

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Customer',
        href: '/customer',
    },
    {
        title: 'Create',
        href: '/customer/create',
    },
];

const form = useForm({
    code: '',
    name: '',
    location: {
        address: '',
        province: '',
        cities: '',
        district: '',
        subdistrict: '',
        postal_code: '',
    },
});

// Watch for province changes to fetch cities
watch(
    () => form.location.province,
    async (newProvince, oldProvince) => {
        if (newProvince && newProvince !== oldProvince) {
            // Reset dependent fields
            form.location.cities = '';
            form.location.district = '';
            form.location.subdistrict = '';
            cities.value = [];
            districts.value = [];
            subdistricts.value = [];

            // Fetch cities
            await fetchCities(newProvince);
        } else if (!newProvince) {
            // Clear all dependent data if province is cleared
            form.location.cities = '';
            form.location.district = '';
            form.location.subdistrict = '';
            cities.value = [];
            districts.value = [];
            subdistricts.value = [];
        }
    },
);

// Watch for city changes to fetch districts
watch(
    () => form.location.cities,
    async (newCity, oldCity) => {
        if (newCity && newCity !== oldCity) {
            // Reset dependent fields
            form.location.district = '';
            form.location.subdistrict = '';
            districts.value = [];
            subdistricts.value = [];

            // Fetch districts
            await fetchDistricts(newCity);
        } else if (!newCity) {
            // Clear dependent data if city is cleared
            form.location.district = '';
            form.location.subdistrict = '';
            districts.value = [];
            subdistricts.value = [];
        }
    },
);

// Watch for district changes to fetch subdistricts
watch(
    () => form.location.district,
    async (newDistrict, oldDistrict) => {
        if (newDistrict && newDistrict !== oldDistrict) {
            form.location.subdistrict = '';
            subdistricts.value = [];

            // Fetch subdistricts
            await fetchSubdistricts(newDistrict);
        } else if (!newDistrict) {
            form.location.subdistrict = '';
            subdistricts.value = [];
        }
    },
);

// Fetch cities based on province
const fetchCities = async (provinceCode: string) => {
    try {
        loadingCities.value = true;
        const response = await axios.get(`/api/cities/${provinceCode}`);
        cities.value = response.data;
    } catch (error) {
        console.error('Error fetching cities:', error);
        toast.add({
            severity: 'error',
            summary: 'Error',
            detail: 'Failed to fetch cities',
            life: 3000,
        });
        cities.value = [];
    } finally {
        loadingCities.value = false;
    }
};

// Fetch districts based on city
const fetchDistricts = async (cityCode: string) => {
    try {
        loadingDistricts.value = true;
        const response = await axios.get(`/api/districts/${cityCode}`);
        districts.value = response.data;
    } catch (error) {
        console.error('Error fetching districts:', error);
        toast.add({
            severity: 'error',
            summary: 'Error',
            detail: 'Failed to fetch districts',
            life: 3000,
        });
        districts.value = [];
    } finally {
        loadingDistricts.value = false;
    }
};

const fetchSubdistricts = async (districtCode: string) => {
    try {
        loadingSubdistricts.value = true;
        const response = await axios.get(`/api/subdistricts/${districtCode}`);
        subdistricts.value = response.data;
    } catch (error) {
        console.error('Error fetching subdistricts:', error);
        toast.add({
            severity: 'error',
            summary: 'Error',
            detail: 'Failed to fetch subdistricts',
            life: 3000,
        });
        subdistricts.value = [];
    } finally {
        loadingSubdistricts.value = false;
    }
};

const submit = () => {
    form.post(route('customers.store'), {
        onError: () => {
            toast.add({
                severity: 'error',
                summary: 'Error',
                detail: session.value.error || 'Failed to create customer',
                life: 3000,
            });
        },
    });
};
</script>

<template>
    <Head title="Create Customer" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-2 sm:p-4">
            <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 p-3 sm:p-5 md:min-h-min dark:border-sidebar-border">
                <PlaceholderPattern class="pointer-events-none" />

                <form @submit.prevent="submit" class="grid gap-6 rounded-md p-4">
                    <div class="mb-6">
                        <h2 class="text-xl font-semibold text-foreground">Create Customer</h2>
                        <p class="text-muted-foreground">Create new customer information</p>
                    </div>

                    <div class="grid gap-2">
                        <Label for="customer-code">Customer Code</Label>
                        <InputText id="customer-code" type="text" required autofocus :tabindex="1" v-model="form.code" placeholder="Customer Code" />
                        <InputError :message="form.errors.code" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="customer-name">Customer Name</Label>
                        <InputText id="customer-name" type="text" required autofocus :tabindex="1" v-model="form.name" placeholder="Customer Name" />
                        <InputError :message="form.errors.name" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="customer-address">Customer Address</Label>
                        <InputText
                            id="customer-address"
                            type="text"
                            required
                            autofocus
                            :tabindex="1"
                            v-model="form.location.address"
                            placeholder="Customer Address"
                        />
                        <InputError :message="form.errors['location.address']" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="province">Province</Label>
                        <Select
                            id="province"
                            v-model="form.location.province"
                            :options="props.provinces"
                            optionLabel="label"
                            optionValue="value"
                            placeholder="Select Province"
                            class="w-full"
                        />
                        <InputError :message="form.errors['location.province']" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="city">City</Label>
                        <Select
                            id="city"
                            v-model="form.location.cities"
                            :options="cities"
                            optionLabel="label"
                            optionValue="value"
                            placeholder="Select City"
                            :disabled="!form.location.province || loadingCities"
                            :loading="loadingCities"
                            class="w-full"
                        />
                        <InputError :message="form.errors['location.cities']" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="district">District</Label>
                        <Select
                            id="district"
                            v-model="form.location.district"
                            :options="districts"
                            optionLabel="label"
                            optionValue="value"
                            placeholder="Select District"
                            :disabled="!form.location.cities || loadingDistricts"
                            :loading="loadingDistricts"
                            class="w-full"
                        />
                        <InputError :message="form.errors['location.district']" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="subdistrict">Sub District</Label>
                        <Select
                            id="subdistrict"
                            v-model="form.location.subdistrict"
                            :options="subdistricts"
                            optionLabel="label"
                            optionValue="value"
                            placeholder="Select Sub District"
                            :disabled="!form.location.district || loadingSubdistricts"
                            :loading="loadingSubdistricts"
                            class="w-full"
                        />
                        <InputError :message="form.errors['location.subdistrict']" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="postal-code">Postal Code</Label>
                        <InputText id="postal-code" type="text" v-model="form.location.postal_code" placeholder="Postal Code" class="w-full" />
                        <InputError :message="form.errors['location.postal_code']" />
                    </div>
                    <div class="flex justify-end gap-4">
                        <button
                            type="button"
                            @click="$inertia.visit(route('customers.index'))"
                            class="rounded-md bg-gray-200 px-4 py-2 text-gray-700 transition-colors hover:bg-gray-300"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="rounded-md bg-green-600 px-4 py-2 text-white transition-colors hover:bg-green-700 disabled:opacity-50"
                        >
                            {{ form.processing ? 'Creating...' : 'Create Customer' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped></style>
