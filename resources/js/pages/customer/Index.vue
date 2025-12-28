<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import Button from '@/components/ui/button/Button.vue';
import DataTable from '@/components/shared/DataTable.vue';
import Pagination from '@/components/shared/Pagination.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import Modal from '@/components/shared/Modal.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { Edit, Trash2, Search, Filter, RefreshCcw } from 'lucide-vue-next';

interface Category {
  id: number;
  name: string;
  slug: string;
  description?: string;
  created_at: string;
  is_active: boolean;
}

const props = defineProps<{
  categories: {
    data: Category[];
    meta?: {
      current_page: number;
      last_page: number;
      links: { url: string | null; label: string; active: boolean }[];
      total: number;
    };
  };
}>();

// --- Breadcrumbs & Columns ---
const breadcrumbs: BreadcrumbItem[] = [{ title: 'Categories', href: dashboard().url }];

const columns = [
  { key: 'name', label: 'Name', width: '200px' },
  { key: 'slug', label: 'Slug', width: '150px' },
  { key: 'description', label: 'Description' },
  { key: 'is_active', label: 'Status', width: '120px' },
  { key: 'created_at', label: 'Created', width: '150px' },
];

// --- Search & Date Filter ---
const searchQuery = ref('');
const startDate = ref('');
const endDate = ref('');
const showFilterDropdown = ref(false);

const hasDateFilter = computed(() => !!startDate.value || !!endDate.value);

// --- Fetch from server ---
const fetchCategories = () => {
  router.get(
    '/categories',
    {
      search: searchQuery.value,
      start_date: startDate.value,
      end_date: endDate.value,
    },
    { preserveState: true, replace: true }
  );
  showFilterDropdown.value = false;
};

// --- Reset Date Filter ---
const resetDateFilter = () => {
  startDate.value = '';
  endDate.value = '';
  fetchCategories();
};

// --- Auto fetch when search is cleared ---
watch(searchQuery, (value) => {
  if (value === '') {
    fetchCategories();
  }
});

// --- Row actions ---
const handleEdit = (category: Category) => router.visit(`/categories/${category.id}/edit`);
const handleRowClick = (category: Category) => console.log('Row clicked:', category);

// --- Delete Modal ---
const showDeleteModal = ref(false);
const selectedCategory = ref<Category | null>(null);

const openDeleteModal = (category: Category) => {
  selectedCategory.value = category;
  showDeleteModal.value = true;
};

const confirmDelete = () => {
  if (selectedCategory.value) {
    router.delete(`/categories/${selectedCategory.value.id}`);
    showDeleteModal.value = false;
    selectedCategory.value = null;
  }
};

const cancelDelete = () => {
  showDeleteModal.value = false;
  selectedCategory.value = null;
};

// --- Pagination ---
const paginationMeta = computed(() => ({
  current_page: props.categories.meta?.current_page ?? 1,
  last_page: props.categories.meta?.last_page ?? 1,
  links: Array.isArray(props.categories.meta?.links) ? props.categories.meta.links : [],
}));
</script>

<template>
  <Head title="Categories" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">

      <!-- Header -->
      <div class="flex justify-between items-center mb-4">
        <div>
          <h1 class="text-2xl font-bold">Categories</h1>
          <p class="text-sm text-gray-500">Manage your product categories</p>
        </div>
        <Link href="/categories/create">
          <Button class="bg-blue-600 hover:bg-blue-500 text-white">Create New Category</Button>
        </Link>
      </div>

      <!-- Search & Filter -->
      <div class="flex gap-3 items-center justify-between mb-2">

        <!-- Search -->
        <div class="relative flex-1 max-w-sm">
          <Search class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" />
          <input
            v-model="searchQuery"
            @keyup.enter="fetchCategories"
            type="text"
            placeholder="Search categories..."
            class="w-full pl-10 pr-4 py-2 border rounded-lg"
          />
        </div>

        <!-- Filter Dropdown -->
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
            class="absolute right-0 mt-2 w-72 bg-white border rounded-lg shadow-lg p-4 z-50"
          >
            <label class="text-sm">Start Date</label>
            <input type="date" v-model="startDate" class="w-full border rounded-lg px-2 py-1 mb-2 text-gray-500" />

            <label class="text-sm">End Date</label>
            <input type="date" v-model="endDate" class="w-full border rounded-lg px-2 py-1 mb-3 text-gray-500" />

            <div class="flex gap-2">
              <button
                @click="fetchCategories"
                class="flex-1 bg-blue-600 hover:bg-blue-500 text-white rounded-lg py-2"
              >
                Apply
              </button>

              <button
                v-if="hasDateFilter"
                @click="resetDateFilter"
                class="p-2 border rounded-lg hover:bg-gray-600/50 text-gray-500"
                title="Reset filter"
              >
                <RefreshCcw class="h-4 w-4" />
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- DataTable -->
      <DataTable :columns="columns" :data="props.categories.data" @row-click="handleRowClick">

        <template #row-actions="{ item }">
          <div class="flex gap-3 justify-end">
            <button @click.stop="handleEdit(item)" class="text-blue-600">
              <Edit class="h-4 w-4" />
            </button>
            <button @click.stop="openDeleteModal(item)" class="text-red-600">
              <Trash2 class="h-4 w-4" />
            </button>
          </div>
        </template>

        <template #cell-is_active="{ item }">
          <span class="px-2 py-1 rounded text-xs"
            :class="item.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'">
            {{ item.is_active ? 'Active' : 'Inactive' }}
          </span>
        </template>

        <template #cell-created_at="{ item }">
          {{ new Date(item.created_at).toLocaleDateString() }}
        </template>

        <template #footer>
          <div class="flex justify-between w-full">
            <p class="text-sm">
              Showing {{ props.categories.data.length }} of {{ props.categories.meta?.total ?? 0 }}
            </p>
            <Pagination :meta="paginationMeta" />
          </div>
        </template>
      </DataTable>

      <!-- Delete Modal -->
      <Modal
        :show="showDeleteModal"
        title="Confirm Delete"
        :message="`Delete ${selectedCategory?.name}?`"
        confirmText="Delete"
        cancelText="Cancel"
        @confirm="confirmDelete"
        @cancel="cancelDelete"
      />
    </div>
  </AppLayout>
</template>
