<script setup lang="ts">
interface Column {
  key: string;
  label: string;
  width?: string;
}

interface Props {
  columns: Column[];
  data: any[];
  emptyMessage?: string;
}

const props = withDefaults(defineProps<Props>(), {
  emptyMessage: 'No data found',
});

const emit = defineEmits<{
  rowClick: [item: any];
}>();

const handleRowClick = (item: any) => emit('rowClick', item);
</script>

<template>
  <div class="w-full space-y-4">
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
                {{ column.label }}
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
              v-for="(item, index) in props.data"
              :key="item.id || index"
              class="hover:bg-gray-50 transition-colors cursor-pointer"
              @click="handleRowClick(item)"
            >
              <td
                v-for="column in columns"
                :key="column.key"
                class="px-6 py-4 text-sm text-gray-900"
              >
                <slot :name="`cell-${column.key}`" :item="item" :value="item[column.key]">
                  {{ item[column.key] }}
                </slot>
              </td>

              <!-- Row actions slot -->
              <td v-if="$slots['row-actions']" class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium" @click.stop>
                <slot name="row-actions" :item="item"></slot>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Empty State -->
      <div v-if="props.data.length === 0" class="text-center py-12">
        <slot name="empty-state">
          <div class="text-gray-500">
            <p class="text-sm">{{ emptyMessage }}</p>
          </div>
        </slot>
      </div>
    </div>

    <!-- Footer slot -->
    <div v-if="$slots.footer" class="flex justify-between items-center">
      <slot name="footer" :total="props.data.length" />
    </div>
  </div>
</template>
