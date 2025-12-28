<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import Button from '@/components/ui/button/Button.vue';
import DataTable from '@/components/shared/DataTable.vue';
import Pagination from '@/components/shared/Pagination.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { Search, Filter, RefreshCcw } from 'lucide-vue-next';
import CustomerActions from './partials/Action.vue';

interface Customer {
  id: number;
  name: string;
  phone: string;
  email: string;
  address: string;
  credit_limit: string;
  opening_balance: string;
  created_at: string;
}

const props = defineProps<{
  customers: {
    data: Customer[];
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
  { title: 'Customers', href: dashboard().url },
];

/* Table columns */
const columns = [
  { key: 'name', label: 'Name', width: '200px' },
  { key: 'phone', label: 'Phone', width: '150px' },
  { key: 'email', label: 'Email', width: '200px' },
  { key: 'address', label: 'Address', width: '250px' },
  { key: 'credit_limit', label: 'Credit Limit', width: '120px' },
  { key: 'opening_balance', label: 'Opening Balance', width: '130px' },
  { key: 'created_at', label: 'Created', width: '150px' },
];

/* Search & Filter */
const searchQuery = ref('');
const startDate = ref('');
const endDate = ref('');
const showFilterDropdown = ref(false);

const hasDateFilter = computed(() => !!startDate.value || !!endDate.value);

/* Fetch */
const fetchCustomers = () => {
  router.get(
    '/customers',
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
  fetchCustomers();
};

/* Auto fetch when search cleared */
watch(searchQuery, (value) => {
  if (value === '') fetchCustomers();
});

/* Pagination */
const paginationMeta = computed(() => ({
  current_page: props.customers.meta?.current_page ?? 1,
  last_page: props.customers.meta?.last_page ?? 1,
  links: props.customers.meta?.links ?? [],
}));
</script>

<template>
  <Head title="Customers" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex flex-col gap-4 p-4">

      <!-- Header -->
      <div class="flex justify-between items-center">
        <div>
          <h1 class="text-2xl font-bold">Customers</h1>
          <p class="text-sm text-gray-500">Manage your customers</p>
        </div>

        <Link href="/customers/create">
          <Button class="bg-blue-600 hover:bg-blue-500 text-white">
            Create Customer
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
            @keyup.enter="fetchCustomers"
            placeholder="Search customers..."
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
                @click="fetchCustomers"
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
      <DataTable :columns="columns" :data="props.customers.data">
        <template #row-actions="{ item }">
          <CustomerActions :item="item" />
        </template>

        <template #cell-created_at="{ item }">
          {{ new Date(item.created_at).toLocaleDateString() }}
        </template>

        <template #footer>
          <div class="flex justify-between w-full">
            <p class="text-sm">
              Showing {{ props.customers.data.length }} of {{ props.customers.meta?.total ?? 0 }}
            </p>
            <Pagination :meta="paginationMeta" />
          </div>
        </template>
      </DataTable>

    </div>
  </AppLayout>
</template>
