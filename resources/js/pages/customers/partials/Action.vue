<script setup lang="ts">
import { ref, nextTick, onMounted, onBeforeUnmount } from 'vue';
import { router } from '@inertiajs/vue3';
import Modal from '@/components/shared/Modal.vue';
import {
  MoreVertical,
  Edit,
  Trash2,
} from 'lucide-vue-next';

interface Category {
  id: number;
  is_active: boolean;
  name?: string;
}

const props = defineProps<{ item: Category }>();

/* Dropdown */
const open = ref(false);
const buttonRef = ref<HTMLElement | null>(null);
const position = ref({ top: 0, left: 0 });

/* Delete modal */
const showDeleteModal = ref(false);


/* Dropdown position calculation */
const openMenu = async () => {
  open.value = !open.value;

  if (open.value) {
    await nextTick();
    const rect = buttonRef.value!.getBoundingClientRect();
    position.value = {
      top: rect.bottom + window.scrollY + 6,
      left: rect.right + window.scrollX - 180,
    };
  }
};

/* Actions */
const edit = () => router.visit(`/customers/${props.item.id}/edit`);


/* Confirm delete */
const confirmDelete = () => {
  router.delete(`/customers/${props.item.id}`);
  showDeleteModal.value = false;
};

/* Close dropdown on outside click */
const handleClickOutside = (e: MouseEvent) => {
  if (!buttonRef.value?.contains(e.target as Node)) {
    open.value = false;
  }
};

onMounted(() => document.addEventListener('click', handleClickOutside));
onBeforeUnmount(() => document.removeEventListener('click', handleClickOutside));
</script>

<template>
  <!-- Trigger -->
  <button
    ref="buttonRef"
    @click.stop="openMenu"
    class="p-2 rounded-lg text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800"
  >
    <MoreVertical class="h-5 w-5" />
  </button>

  <!-- Teleported Dropdown -->
  <Teleport to="body">
    <div
      v-if="open"
      class="fixed z-9999 w-44 bg-white dark:bg-gray-900
             border border-gray-200 dark:border-gray-700
             rounded-lg shadow-xl"
      :style="{ top: `${position.top}px`, left: `${position.left}px` }"
    >
      <!-- Edit -->
      <button
        @click="edit"
        class="flex w-full items-center gap-2 px-4 py-2 text-sm
               text-blue-600 dark:text-blue-400 hover:bg-gray-100 dark:hover:bg-gray-800"
      >
        <Edit class="h-4 w-4" />
        Edit
      </button>

      <!-- Delete -->
      <button
        @click="showDeleteModal = true"
        class="flex w-full items-center gap-2 px-4 py-2 text-sm
               text-red-600 hover:bg-gray-100 dark:hover:bg-gray-800"
      >
        <Trash2 class="h-4 w-4" />
        Delete
      </button>
    </div>
  </Teleport>

  <!-- Delete Modal -->
  <Modal
    :show="showDeleteModal"
    title="Confirm Delete"
    :message="`Delete ${props.item.name ?? 'this item'}?`"
    confirmText="Delete"
    cancelText="Cancel"
    @confirm="confirmDelete"
    @cancel="showDeleteModal = false"
  />

</template>
