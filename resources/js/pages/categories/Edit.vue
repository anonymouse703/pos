<script setup lang="ts">
import Input from '@/components/ui/input/Input.vue';
import Label from '@/components/ui/label/Label.vue';
import Button from '@/components/ui/button/Button.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { ArrowLeft, Loader2 } from 'lucide-vue-next';

const props = defineProps<{
  category: {
    data: {
      id: number;
      name: string;
      slug: string;
      description?: string;
      is_active: number;
    }
  }
}>();

// Breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Categories', href: '/categories' },
  { title: 'Edit', href: dashboard().url },
];

// Form typing
interface CategoryForm {
  name: string;
  slug: string;
  description: string;
  is_active: boolean;
}

// Use Inertia form with pre-filled data
const form = useForm<CategoryForm>({
  name: props.category.data.name,
  slug: props.category.data.slug,
  description: props.category.data.description || '',
  is_active: props.category.data.is_active === 1,
});

// Auto-generate slug from name
const generateSlug = () => {
  form.slug = form.name
    .toLowerCase()
    .replace(/[^a-z0-9]+/g, '-')
    .replace(/(^-|-$)/g, '');
};

// Submit handler (PATCH)
const submit = () => {
  form.transform((data) => ({
    ...data,
    is_active: data.is_active ? 1 : 0,
  })).patch(`/categories/${props.category.data.id}`, {
    onSuccess: () => console.log('Updated!'),
    onError: (errors) => console.log(errors),
  });
};
</script>

<template>

  <Head title="Edit Category" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-4">
      <!-- Header -->
      <div class="flex items-center gap-4">
        <Link href="/categories" class="text-gray-600 hover:text-gray-900 transition-colors">
          <ArrowLeft class="h-5 w-5" />
        </Link>
        <div>
          <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Category</h1>
          <p class="text-sm text-gray-500 mt-1 dark:text-white">
            Update the category details below
          </p>
        </div>
      </div>

      <!-- Form Card -->
      <div class="bg-white rounded-lg border border-gray-200 shadow-sm w-full max-w-3xl mx-auto">
        <form @submit.prevent="submit" class="p-6 space-y-6">
          <!-- Category Name -->
          <div class="space-y-2">
            <Label for="name" class="text-sm font-medium text-gray-900">
              Category Name <span class="text-red-500">*</span>
            </Label>
            <Input v-model="form.name" @input="generateSlug" type="text" id="name"
              placeholder="e.g., Electronics, Clothing, Home & Garden"
              class="w-full bg-white dark:bg-gray-200 text-gray-900 dark:text-gray-500 border-gray-300 dark:border-gray-600"
              :class="{ 'border-red-500': form.errors.name }" required />
            <p v-if="form.errors.name" class="text-red-600 text-sm">{{ form.errors.name }}</p>
          </div>

          <!-- Slug (Auto-generated) -->
          <div class="space-y-2">
            <Label for="slug" class="text-sm font-medium text-gray-900">
              Slug <span class="text-gray-400 text-xs">(Auto-generated)</span>
            </Label>
            <Input v-model="form.slug" type="text" id="slug" placeholder="category-slug"
              class="w-full bg-white dark:bg-gray-200 text-gray-900 dark:text-gray-500 border-gray-300 dark:border-gray-600"
              :class="{ 'border-red-500': form.errors.slug }" />
            <p class="text-xs text-gray-500">
              URL-friendly version of the name. You can edit this if needed.
            </p>
            <p v-if="form.errors.slug" class="text-red-600 text-sm">{{ form.errors.slug }}</p>
          </div>

          <!-- Description -->
          <div class="space-y-2">
            <Label for="description" class="text-sm font-medium text-gray-900">Description</Label>
            <textarea v-model="form.description" id="description" rows="4"
              class="w-full px-3 py-2 border dark:text-gray-500 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all resize-none"
              :class="{ 'border-red-500': form.errors.description }"
              placeholder="Brief description of this category..."></textarea>
            <p v-if="form.errors.description" class="text-red-600 text-sm">{{ form.errors.description }}</p>
          </div>

          <!-- Status -->
          <div class="space-y-2">
            <Label class="text-sm font-medium text-gray-900">Status</Label>
            <div class="flex gap-4">
              <label class="flex items-center gap-2 cursor-pointer">
                <input v-model="form.is_active" type="radio" :value="true"
                  class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500" />
                <span class="text-sm text-gray-700">Active</span>
              </label>
              <label class="flex items-center gap-2 cursor-pointer">
                <input v-model="form.is_active" type="radio" :value="false"
                  class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500" />
                <span class="text-sm text-gray-700">Inactive</span>
              </label>
            </div>
            <p v-if="form.errors.is_active" class="text-red-600 text-sm">{{ form.errors.is_active }}</p>
          </div>

          <!-- Divider -->
          <div class="border-t border-gray-200"></div>

          <!-- Form Actions -->
          <div class="flex items-center justify-between">
            <Link href="/categories" class="px-4 py-2 text-sm text-gray-700 hover:text-gray-900 transition-colors">
              Cancel
            </Link>
            <div class="flex gap-3">
              <Button type="button" @click="form.reset()" variant="outline" :disabled="form.processing"
                class="border-gray-300 text-gray-700 hover:bg-gray-50">
                Reset
              </Button>
              <Button type="submit" :disabled="form.processing"
                class="bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                <Loader2 v-if="form.processing" class="h-4 w-4 animate-spin" />
                <span>{{ form.processing ? 'Updating...' : 'Update Category' }}</span>
              </Button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>
