<script setup lang="ts">
import { computed } from 'vue';
import { router } from '@inertiajs/vue3';
import { ChevronLeft, ChevronRight } from 'lucide-vue-next';

interface Link {
  url: string | null;
  label: string;
  active: boolean;
}

interface PaginationMeta {
  current_page: number;
  last_page: number;
  links: Link[];
}

const props = defineProps({
  meta: {
    type: Object as () => PaginationMeta,
    required: true
  },
  query: {
    type: Object,
    default: () => ({})
  }
});

const pagesList = computed(() =>
  props.meta?.links?.filter(
    link =>
      !link.label.includes('Previous') &&
      !link.label.includes('Next') &&
      !link.label.includes('«') &&
      !link.label.includes('»')
  ) ?? []
);

const prevPage = computed(() =>
  props.meta?.links?.find(
    link => link.label.includes('Previous') || link.label.includes('«')
  ) ?? null
)

const nextPage = computed(() =>
  props.meta?.links?.find(
    link => link.label.includes('Next') || link.label.includes('»')
  ) ?? null
)

const currentPage = computed(() => props.meta?.current_page ?? 1)
const numPages = computed(() => props.meta?.last_page ?? 1)

const redirectToUrl = (url: string | null) => {
  if (url) {
    router.visit(url, { data: props.query, preserveState: true })
  }
}
</script>

<template>
  <div class="flex flex-col items-center mt-6 space-y-2">
    <div class="flex space-x-2">
      <button
        v-if="prevPage"
        @click="redirectToUrl(prevPage.url)"
        class="px-3 py-1 rounded border bg-white text-gray-700 hover:bg-gray-100"
      >
        <ChevronLeft class="w-4 h-4 inline" />
      </button>

      <button
        v-for="(page, index) in pagesList"
        :key="index"
        @click="redirectToUrl(page.url)"
        :class="[
          'px-3 py-1 rounded border',
          page.active
            ? 'bg-teal-600 text-white border-teal-600'
            : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-100'
        ]"
      >
        {{ page.label }}
      </button>

      <button
        v-if="nextPage"
        @click="redirectToUrl(nextPage.url)"
        class="px-3 py-1 rounded border bg-white text-gray-700 hover:bg-gray-100"
      >
        <ChevronRight class="w-4 h-4 inline" />
      </button>
    </div>

    <small>Page {{ currentPage }} of {{ numPages }}</small>
  </div>
</template>

<style scoped>
  
</style>
