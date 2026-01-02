<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import Button from '@/components/ui/button/Button.vue';
import DataTable from '@/components/shared/DataTable.vue';
import Pagination from '@/components/shared/Pagination.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { Search, Filter, RefreshCcw } from 'lucide-vue-next';
import SupplierActions from './partials/Action.vue';

interface Supplier {
    id: number;
    name: string;
    contact_person?: string;
    phone?: string;
    email?: string;
    address?: string;
    terms_days?: string;
    opening_balance?: string | number;
    created_at: string;
}

const props = defineProps<{
    suppliers: {
        data: Supplier[];
        meta?: {
            current_page: number;
            last_page: number;
            links: { url: string | null; label: string; active: boolean }[];
            total: number;
        };
    };
}>();

/* Breadcrumbs */
const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Suppliers', href: '/suppliers' },
];

/* Table columns */
const columns = [
    { key: 'name', label: 'Name', width: '200px' },
    { key: 'contact_person', label: 'Contact Person', width: '150px' },
    { key: 'phone', label: 'Phone', width: '150px' },
    { key: 'email', label: 'Email', width: '200px' },
    { key: 'address', label: 'Address', width: '250px' },
    { key: 'terms_days', label: 'Terms (Days)', width: '120px' },
    { key: 'opening_balance', label: 'Opening Balance', width: '150px' },
    { key: 'created_at', label: 'Created', width: '150px' },
];

/* Search & Filter */
const searchQuery = ref('');
const startDate = ref('');
const endDate = ref('');
const showFilterDropdown = ref(false);

const hasDateFilter = computed(() => !!startDate.value || !!endDate.value);

const fetchSuppliers = () => {
    router.get(
        '/suppliers',
        {
            search: searchQuery.value,
            start_date: startDate.value,
            end_date: endDate.value,
        },
        { preserveState: true, replace: true }
    );
    showFilterDropdown.value = false;
};

const resetDateFilter = () => {
    startDate.value = '';
    endDate.value = '';
    fetchSuppliers();
};

watch(searchQuery, (value) => {
    if (value === '') fetchSuppliers();
});

const paginationMeta = computed(() => ({
    current_page: props.suppliers.meta?.current_page ?? 1,
    last_page: props.suppliers.meta?.last_page ?? 1,
    links: props.suppliers.meta?.links ?? [],
}));

const formatCurrency = (value: number | string | undefined) => {
    if (!value) return '0.00';
    return new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP',
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
        useGrouping: true, 
    }).format(Number(value));
};
</script>

<template>
  <Head title="Suppliers" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex flex-col gap-4 p-4">
      <!-- Header -->
      <div class="flex justify-between items-center">
        <div>
          <h1 class="text-2xl font-bold">Suppliers</h1>
          <p class="text-sm text-gray-500">Manage your suppliers</p>
        </div>

        <Link href="/suppliers/create">
          <Button class="bg-blue-600 hover:bg-blue-500 text-white">
            Create Supplier
          </Button>
        </Link>
      </div>

      <!-- Search & Filter -->
      <div class="flex justify-between items-center gap-3">
        <!-- Search -->
        <div class="relative max-w-sm w-full">
          <Search class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" />
          <input
            v-model="searchQuery"
            @keyup.enter="fetchSuppliers"
            placeholder="Search suppliers..."
            class="w-full pl-10 pr-4 py-2 border rounded-lg"
          />
        </div>

        <!-- Filter -->
        <div class="relative">
          <button
            @click="showFilterDropdown = !showFilterDropdown"
            class="flex items-center gap-2 px-4 py-2 border rounded-lg"
          >
            <Filter class="h-4 w-4" />
            Filter
          </button>

          <div
            v-if="showFilterDropdown"
            class="absolute right-0 mt-2 w-72 bg-gray-100 border-gray-500 rounded-lg shadow-lg p-4 z-50"
          >
            <label class="text-sm text-gray-500">Start Date</label>
            <input
              type="date"
              v-model="startDate"
              class="w-full border rounded-lg px-2 py-1 mb-2 text-gray-500"
            />

            <label class="text-sm text-gray-500">End Date</label>
            <input
              type="date"
              v-model="endDate"
              class="w-full border rounded-lg px-2 py-1 mb-3 text-gray-500"
            />

            <div class="flex gap-2">
              <button
                @click="fetchSuppliers"
                class="flex-1 bg-blue-600 hover:bg-blue-500 text-white rounded-lg py-2"
              >
                Apply
              </button>

              <button v-if="hasDateFilter" @click="resetDateFilter" class="p-2 border rounded-lg">
                <RefreshCcw class="h-4 w-4" />
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- DataTable -->
      <DataTable :columns="columns" :data="props.suppliers.data">
        <template #row-actions="{ item }">
          <SupplierActions :item="item" />
        </template>

        <template #cell-created_at="{ item }">
          {{ new Date(item.created_at).toLocaleDateString() }}
        </template>

        <template #cell-opening_balance="{ item }">
          {{ formatCurrency(item.opening_balance) }}
        </template>

        <template #footer>
          <div class="flex justify-between w-full">
            <p class="text-sm">
              Showing {{ props.suppliers.data.length }} of {{ props.suppliers.meta?.total ?? 0 }}
            </p>
            <Pagination :meta="paginationMeta" />
          </div>
        </template>
      </DataTable>
    </div>
  </AppLayout>
</template>
