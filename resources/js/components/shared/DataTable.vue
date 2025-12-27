<script setup lang="ts">
import { ref, computed } from 'vue';
import { Search, Filter, ChevronDown, ChevronUp, MoreVertical } from 'lucide-vue-next';

interface Column {
    key: string;
    label: string;
    sortable?: boolean;
    width?: string;
}

interface Props {
    columns: Column[];
    data: any[];
    searchable?: boolean;
    filterable?: boolean;
    searchPlaceholder?: string;
    emptyMessage?: string;
}

const props = withDefaults(defineProps<Props>(), {
    searchable: true,
    filterable: true,
    searchPlaceholder: 'Search...',
    emptyMessage: 'No data found',
});

const emit = defineEmits<{
    rowClick: [item: any];
}>();

const searchQuery = ref('');
const sortKey = ref<string>('');
const sortOrder = ref<'asc' | 'desc'>('asc');

// Computed filtered and sorted data
const processedData = computed(() => {
    let result = [...props.data];

    // Apply search
    if (searchQuery.value) {
        result = result.filter(item =>
            Object.values(item).some(val =>
                String(val).toLowerCase().includes(searchQuery.value.toLowerCase())
            )
        );
    }

    // Apply sorting
    if (sortKey.value) {
        result.sort((a, b) => {
            const aVal = a[sortKey.value];
            const bVal = b[sortKey.value];
            
            if (aVal === null || aVal === undefined) return 1;
            if (bVal === null || bVal === undefined) return -1;
            
            if (aVal < bVal) return sortOrder.value === 'asc' ? -1 : 1;
            if (aVal > bVal) return sortOrder.value === 'asc' ? 1 : -1;
            return 0;
        });
    }

    return result;
});

const handleSort = (key: string) => {
    if (sortKey.value === key) {
        sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc';
    } else {
        sortKey.value = key;
        sortOrder.value = 'asc';
    }
};

const handleRowClick = (item: any) => {
    emit('rowClick', item);
};
</script>

<template>
    <div class="w-full space-y-4">
        <!-- Search and Filter Bar -->
        <div class="flex flex-col sm:flex-row gap-3 items-start sm:items-center justify-between">
            <!-- Search Input -->
            <div v-if="searchable" class="relative flex-1 max-w-sm">
                <Search class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" />
                <input
                    v-model="searchQuery"
                    type="text"
                    :placeholder="searchPlaceholder"
                    class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                />
            </div>

            <!-- Filter and Custom Actions Slot -->
            <div class="flex gap-2 items-center">
                <button
                    v-if="filterable"
                    class="flex items-center gap-2 px-4 py-2 border border-gray-200 rounded-lg hover:bg-gray-50/25 text-white transition-colors"
                >
                    <Filter class="h-4 w-4" />
                    <span class="hidden sm:inline">Filter</span>
                </button>
                
                <!-- Custom actions slot (e.g., Export, Create buttons) -->
                <slot name="actions" />
            </div>
        </div>

        <!-- Table -->
        <div class="border border-gray-200 rounded-lg overflow-hidden bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th
                                v-for="column in columns"
                                :key="column.key"
                                :style="column.width ? { width: column.width } : {}"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                            >
                                <button
                                    v-if="column.sortable"
                                    @click="handleSort(column.key)"
                                    class="flex items-center gap-2 hover:text-gray-700 transition-colors group"
                                >
                                    {{ column.label }}
                                    <div class="flex flex-col">
                                        <ChevronUp
                                            :class="[
                                                'h-3 w-3 -mb-1 transition-colors',
                                                sortKey === column.key && sortOrder === 'asc'
                                                    ? 'text-blue-600'
                                                    : 'text-gray-300 group-hover:text-gray-400'
                                            ]"
                                        />
                                        <ChevronDown
                                            :class="[
                                                'h-3 w-3 transition-colors',
                                                sortKey === column.key && sortOrder === 'desc'
                                                    ? 'text-blue-600'
                                                    : 'text-gray-300 group-hover:text-gray-400'
                                            ]"
                                        />
                                    </div>
                                </button>
                                <span v-else>{{ column.label }}</span>
                            </th>
                            
                            <!-- Actions column (only show if slot is provided) -->
                            <th
                                v-if="$slots['row-actions']"
                                class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider"
                            >
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr
                            v-for="(item, index) in processedData"
                            :key="item.id || index"
                            class="hover:bg-gray-50 transition-colors cursor-pointer"
                            @click="handleRowClick(item)"
                        >
                            <td
                                v-for="column in columns"
                                :key="column.key"
                                class="px-6 py-4 text-sm text-gray-900"
                                :class="column.sortable ? 'whitespace-nowrap' : ''"
                            >
                                <!-- Custom cell slot with fallback to default rendering -->
                                <slot :name="`cell-${column.key}`" :item="item" :value="item[column.key]">
                                    {{ item[column.key] }}
                                </slot>
                            </td>
                            
                            <!-- Row actions slot -->
                            <td
                                v-if="$slots['row-actions']"
                                class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium"
                                @click.stop
                            >
                                <slot name="row-actions" :item="item">
                                    <button class="text-gray-400 hover:text-gray-600 transition-colors">
                                        <MoreVertical class="h-5 w-5" />
                                    </button>
                                </slot>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Empty State -->
            <div
                v-if="processedData.length === 0"
                class="text-center py-12"
            >
                <slot name="empty-state">
                    <div class="text-gray-500">
                        <p class="text-sm">{{ emptyMessage }}</p>
                    </div>
                </slot>
            </div>
        </div>

        <!-- Footer slot for pagination, stats, etc. -->
        <div v-if="$slots.footer" class="flex justify-between items-center">
            <slot name="footer" :total="processedData.length" />
        </div>
    </div>
</template>