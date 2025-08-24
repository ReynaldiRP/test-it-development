<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import PlaceholderPattern from '@/components/PlaceholderPattern.vue';
import Label from '@/components/ui/label/Label.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { BreadcrumbItem, Session } from '@/types';
import { Customer } from '@/types/customer';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import axios from 'axios';
import { InputText, Select, useToast } from 'primevue';
import { computed, ComputedRef, onMounted, ref, watch } from 'vue';

// Define the select option interface
interface SelectOption {
    label: string;
    value: string;
    id: number;
}

const props = defineProps<{
    customer: Customer;
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
        title: 'Customers',
        href: '/customers',
    },
    {
        title: 'Edit',
        href: `/customers/${props.customer.id}/edit`,
    },
];

const form = useForm({
    customer_code: props.customer.customer_code || '',
    name: props.customer.name || '',
    location: {
        address: props.customer.location?.address || '',
        province: '',
        cities: '',
        district: '',
        subdistrict: '',
        postal_code: props.customer.location?.postal_code || '',
    },
});

const initializeLocationData = async () => {
    try {
        // Find province code
        const province = props.provinces.find((p) => p.label === props.customer.location?.province);
        if (province) {
            form.location.province = province.value;
            await fetchCities(province.value);

            // Find city code
            const city = cities.value.find((c) => c.label === props.customer.location?.cities);
            if (city) {
                form.location.cities = city.value;
                await fetchDistricts(city.value);

                // Find district code
                const district = districts.value.find((d) => d.label === props.customer.location?.district);
                if (district) {
                    form.location.district = district.value;
                    await fetchSubdistricts(district.value);

                    // Find subdistrict code
                    const subdistrict = subdistricts.value.find((s) => s.label === props.customer.location?.sub_district);
                    if (subdistrict) {
                        form.location.subdistrict = subdistrict.value;
                    }
                }
            }
        }
    } catch (error) {
        console.error('Error initializing location data:', error);
    }
};

// Initialize on component mount
onMounted(() => {
    initializeLocationData();
});

// Watch for province changes to fetch cities
watch(
    () => form.location.province,
    async (newProvince, oldProvince) => {
        if (newProvince && newProvince !== oldProvince) {
            if (oldProvince !== '') {
                form.location.cities = '';
                form.location.district = '';
                form.location.subdistrict = '';
                districts.value = [];
                subdistricts.value = [];
            }
            cities.value = [];

            // Fetch cities
            await fetchCities(newProvince);
        } else if (!newProvince) {
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
            if (oldCity !== '') {
                form.location.district = '';
                form.location.subdistrict = '';
                subdistricts.value = [];
            }
            districts.value = [];

            // Fetch districts
            await fetchDistricts(newCity);
        } else if (!newCity) {
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
            if (oldDistrict !== '') {
                form.location.subdistrict = '';
            }
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
    form.put(route('customers.update', { customer: props.customer.id }), {
        onError: () => {
            toast.add({
                severity: 'error',
                summary: 'Error',
                detail: session.value.error || 'Failed to update customer',
                life: 3000,
            });
        },
    });
};
</script>

<template>
    <Head title="Edit Customer" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-2 sm:p-4">
            <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 p-3 sm:p-5 md:min-h-min dark:border-sidebar-border">
                <PlaceholderPattern class="pointer-events-none" />

                <form @submit.prevent="submit" class="grid gap-6 rounded-md p-4">
                    <div class="mb-6">
                        <h2 class="text-xl font-semibold text-foreground">Edit Customer</h2>
                        <p class="text-muted-foreground">Update customer information</p>
                    </div>

                    <div class="grid gap-2">
                        <Label for="customer-code">Customer Code</Label>
                        <InputText
                            id="customer-code"
                            type="text"
                            required
                            autofocus
                            :tabindex="1"
                            v-model="form.customer_code"
                            placeholder="Customer Code"
                        />
                        <InputError :message="form.errors.customer_code" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="customer-name">Customer Name</Label>
                        <InputText id="customer-name" type="text" required :tabindex="2" v-model="form.name" placeholder="Customer Name" />
                        <InputError :message="form.errors.name" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="customer-address">Customer Address</Label>
                        <InputText
                            id="customer-address"
                            type="text"
                            required
                            :tabindex="3"
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
                            {{ form.processing ? 'Updating...' : 'Update Customer' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped></style>
