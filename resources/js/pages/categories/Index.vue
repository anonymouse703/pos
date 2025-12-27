<script setup lang="ts">
import { computed, ref } from 'vue';
import Button from '@/components/ui/button/Button.vue';
import DataTable from '@/components/shared/DataTable.vue';
import Pagination from '@/components/shared/Pagination.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import Modal from '@/components/shared/Modal.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { Edit, Trash2 } from 'lucide-vue-next';

const props = defineProps<{
    categories: {
        data: Category[];
        meta?: {
            current_page: number;
            last_page: number;
            links: {
                url: string | null;
                label: string;
                active: boolean;
            }[];
            total: number;
        };
    };
}>();

interface Category {
    id: number;
    name: string;
    slug: string;
    description?: string;
    created_at: string;
    is_active: boolean;
}

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Categories', href: dashboard().url },
];

const columns = [
    { key: 'name', label: 'Name', sortable: true },
    { key: 'slug', label: 'Slug', sortable: true },
    { key: 'description', label: 'Description', sortable: false },
    { key: 'is_active', label: 'Status', sortable: true, width: '120px' },
    { key: 'created_at', label: 'Created', sortable: true, width: '150px' },
];

const handleEdit = (category: Category) => router.visit(`/categories/${category.id}/edit`);
const handleRowClick = (category: Category) => console.log('Row clicked:', category);

const paginationMeta = computed(() => ({
    current_page: props.categories.meta?.current_page ?? 1,
    last_page: props.categories.meta?.last_page ?? 1,
    links: Array.isArray(props.categories.meta?.links) ? props.categories.meta.links : [],
}));

// Modal state
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

</script>


<template>

    <Head title="Categories" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <!-- Page Header -->
            <div class="flex justify-between items-center mb-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Categories</h1>
                    <p class="text-sm text-gray-500  dark:text-white mt-1">
                        Manage your product categories
                    </p>
                </div>
                <Link href="/categories/create">
                    <Button class="bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Create New Category
                    </Button>
                </Link>
            </div>

            <!-- Data Table Component -->
            <DataTable :columns="columns" :data="props.categories.data" :searchable="true" :filterable="true"
                search-placeholder="Search categories..." empty-message="No categories found"
                @row-click="handleRowClick">
                <!-- Custom cell for is_active with colored badge -->
                <template #cell-is_active="{ item }">
                    <span :class="[
                        'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                        item.is_active
                            ? 'bg-green-100 text-green-800'
                            : 'bg-gray-100 text-gray-800'
                    ]">
                        {{ item.is_active ? 'Active' : 'Inactive' }}
                    </span>
                </template>

                <!-- Custom cell for created date -->
                <template #cell-created_at="{ item }">
                    <span class="text-gray-600">
                        {{ new Date(item.created_at).toLocaleDateString() }}
                    </span>
                </template>

                <!-- Custom cell for description with truncation -->
                <template #cell-description="{ item }">
                    <span class="text-gray-600 line-clamp-2">
                        {{ item.description || '-' }}
                    </span>
                </template>

                <!-- Row actions (Edit & Delete buttons) -->
                <template #row-actions="{ item }">
                    <div class="flex items-center justify-end gap-3">
                        <button @click="handleEdit(item)"
                            class="text-blue-600 hover:text-blue-800 transition-colors p-1" title="Edit">
                            <Edit class="h-4 w-4" />
                        </button>
                        <button @click="openDeleteModal(item)"
                            class="text-red-600 hover:text-red-800 transition-colors p-1" title="Delete">
                            <Trash2 class="h-4 w-4" />
                        </button>
                    </div>
                </template>

                <!-- Additional toolbar actions -->
                <template #actions>
                    <button
                        class="px-4 py-2 text-sm text-gray-700 dark:text-white border border-gray-200 rounded-lg  hover:bg-gray-50/15 hover:text-gray-100 transition-colors">
                        Export
                    </button>
                </template>

                <!-- Footer with record count -->
                <template #footer>
                    <div class="flex justify-between items-center w-full">
                        <p class="text-sm text-gray-600 dark:text-white">
                            Showing {{ props.categories.data.length }} of {{ props.categories.meta?.total ?? 0 }}
                            {{ (props.categories.meta?.total ?? 0) === 1 ? 'category' : 'categories' }}
                        </p>

                        <!-- Pagination Component -->
                        <Pagination :meta="paginationMeta" />
                    </div>
                </template>

            </DataTable>


            <Modal :show="showDeleteModal" title="Confirm Delete"
                :message="`Are you sure you want to delete ${selectedCategory?.name}? This action cannot be undone.`"
                confirmText="Delete" cancelText="Cancel" @confirm="confirmDelete" @cancel="cancelDelete" />
        </div>
    </AppLayout>
</template>