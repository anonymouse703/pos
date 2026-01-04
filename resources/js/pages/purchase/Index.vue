<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import Button from '@/components/ui/button/Button.vue';
import DataTable from '@/components/shared/DataTable.vue';
import Pagination from '@/components/shared/Pagination.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { Search, Filter, RefreshCcw } from 'lucide-vue-next';
import ProductActions from './partials/Action.vue';

interface Product {
  id: number;
  supplier: string;
  invoice_number: string;
  purchase_date: string;
  total: number;
  status: string;
}

const props = defineProps<{
  purchases: {
    data: Product[];
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
  { title: 'Purchases', href: '/purchases' },
];

/* Table columns */
const columns = [
  { key: 'supplier', label: 'Supplier', width: '300px' },
  { key: 'invoice_number', label: 'Invoice Number', width: '200px' },
  { key: 'purchase_date', label: 'Purchase Date', width: '200px' },
  { key: 'total', label: 'Total Amount', width: '200px' },
  { key: 'status', label: 'Status', width: '100px' },
];

/* Search & Filter */
const searchQuery = ref('');
const startDate = ref('');
const endDate = ref('');
const showFilterDropdown = ref(false);

const hasDateFilter = computed(() => !!startDate.value || !!endDate.value);

/* Fetch */
const fetchPurchases = () => {
  router.get(
    '/purchases',
    {
      search: searchQuery.value,
      start_date: startDate.value,
      end_date: endDate.value,
    },
    { preserveState: true, replace: true }
  );
  showFilterDropdown.value = false;
};

/* Reset filter */
const resetDateFilter = () => {
  startDate.value = '';
  endDate.value = '';
  fetchPurchases();
};

/* Auto fetch when search cleared */
watch(searchQuery, (value) => {
  if (value === '') fetchPurchases();
});

/* Pagination */
const paginationMeta = computed(() => ({
  current_page: props.purchases.meta?.current_page ?? 1,
  last_page: props.purchases.meta?.last_page ?? 1,
  links: props.purchases.meta?.links ?? [],
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
  <Head title="Purchases" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex flex-col gap-4 p-4">

      <!-- Header -->
      <div class="flex justify-between items-center">
        <div>
          <h1 class="text-2xl font-bold">Purchases</h1>
          <p class="text-sm text-gray-500">Manage your purchases</p>
        </div>

        <Link href="/purchases/create">
          <Button class="bg-blue-600 hover:bg-blue-500 text-white">
            Create Purchase
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
            @keyup.enter="fetchPurchases"
            placeholder="Search purchases..."
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
            <input type="date" v-model="startDate" class="w-full border rounded-lg px-2 py-1 mb-2 text-gray-500" />

            <label class="text-sm text-gray-500">End Date</label>
            <input type="date" v-model="endDate" class="w-full border rounded-lg px-2 py-1 mb-3 text-gray-500" />

            <div class="flex gap-2">
              <button
                @click="fetchPurchases"
                class="flex-1 bg-blue-600 hover:bg-blue-500 text-white rounded-lg py-2"
              >
                Apply
              </button>

              <button
                v-if="hasDateFilter"
                @click="resetDateFilter"
                class="p-2 border rounded-lg"
              >
                <RefreshCcw class="h-4 w-4" />
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- DataTable -->
      <DataTable :columns="columns" :data="props.purchases.data">
        <template #row-actions="{ item }">
          <ProductActions :item="item" />
        </template>

        <template #cell-supplier="{ item }">
          {{ (item.supplier.name) ?? 'No Supplier' }}
        </template>

        <template #cell-purchase_date="{ item }">
          {{ new Date(item.purchase_date).toLocaleDateString() }}
        </template>

         <template #cell-total="{ item }">
          {{ formatCurrency(item.total) }}
        </template>

        <template #footer>
          <div class="flex justify-between w-full">
            <p class="text-sm">
              Showing {{ props.purchases.data.length }} of {{ props.purchases.meta?.total ?? 0 }}
            </p>
            <Pagination :meta="paginationMeta" />
          </div>
        </template>
      </DataTable>

    </div>
  </AppLayout>
</template>
