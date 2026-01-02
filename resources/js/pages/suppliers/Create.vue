<script setup lang="ts">
import Input from '@/components/ui/input/Input.vue';
import Label from '@/components/ui/label/Label.vue';
import Button from '@/components/ui/button/Button.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { ArrowLeft, Loader2 } from 'lucide-vue-next';
import { useCurrencyInput } from 'vue-currency-input';

// Breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Suppliers', href: '/suppliers' },
  { title: 'Create', href: '/suppliers.create' },
];

// Form typing
interface SupplierForm {
  name: string;
  contact_person: string;
  phone: string;
  email: string;
  address: string;
  terms_days: number | '';
  opening_balance: number | '';
}

// Initialize form
const form = useForm<SupplierForm>({
  name: '',
  contact_person: '',
  phone: '',
  email: '',
  address: '',
  terms_days: '',
  opening_balance: 0,
});

// Currency input options for opening balance
const currencyOptions = {
  locale: 'en-PH',
  currency: 'PHP',
  precision: 2,
  autoDecimalMode: true,
  distractionFree: false,
  useGrouping: true,
};

// Get ref for the masked input
const { inputRef: openingBalanceRef } = useCurrencyInput(currencyOptions);

// Submit handler
const submit = () => {
  form.post('/suppliers', {
    onSuccess: () => {
      console.log('Supplier created successfully!');
      form.reset();
    },
    onError: (errors) => {
      console.log('Validation errors:', errors);
    },
  });
};
</script>

<template>
  <Head title="Create Supplier" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-4">
      <!-- Header -->
      <div class="flex items-center gap-4">
        <Link href="/suppliers" class="text-gray-600 hover:text-gray-900 transition-colors">
          <ArrowLeft class="h-5 w-5" />
        </Link>
        <div>
          <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Create New Supplier</h1>
          <p class="text-sm text-gray-500 mt-1 dark:text-white">
            Add a new supplier to manage your vendors
          </p>
        </div>
      </div>

      <!-- Form Card -->
      <div class="bg-white rounded-lg border border-gray-200 shadow-sm w-full max-w-5xl mx-auto">
        <form @submit.prevent="submit" class="p-6 space-y-6">
          <!-- Supplier Name -->
          <div class="space-y-2">
            <Label for="name" class="text-sm font-medium text-gray-900">
              Supplier Name <span class="text-red-500">*</span>
            </Label>
            <Input
              v-model="form.name"
              type="text"
              id="name"
              placeholder="Supplier Name"
              class="w-full bg-white dark:bg-gray-200 text-gray-900 dark:text-gray-500 border-gray-300 dark:border-gray-600"
              :class="{ 'border-red-500': form.errors.name }"
              required
            />
            <p v-if="form.errors.name" class="text-red-600 text-sm">{{ form.errors.name }}</p>
          </div>

          <!-- Contact Person -->
          <div class="space-y-2">
            <Label for="contact_person" class="text-sm font-medium text-gray-900">Contact Person</Label>
            <Input
              v-model="form.contact_person"
              type="text"
              id="contact_person"
              placeholder="Contact Person"
              class="w-full bg-white dark:bg-gray-200 text-gray-900 dark:text-gray-500 border-gray-300 dark:border-gray-600"
              :class="{ 'border-red-500': form.errors.contact_person }"
            />
            <p v-if="form.errors.contact_person" class="text-red-600 text-sm">{{ form.errors.contact_person }}</p>
          </div>

          <!-- Phone -->
          <div class="space-y-2">
            <Label for="phone" class="text-sm font-medium text-gray-900">Phone</Label>
            <Input
              v-model="form.phone"
              type="text"
              id="phone"
              placeholder="Phone"
              class="w-full bg-white dark:bg-gray-200 text-gray-900 dark:text-gray-500 border-gray-300 dark:border-gray-600"
              :class="{ 'border-red-500': form.errors.phone }"
            />
            <p v-if="form.errors.phone" class="text-red-600 text-sm">{{ form.errors.phone }}</p>
          </div>

          <!-- Email -->
          <div class="space-y-2">
            <Label for="email" class="text-sm font-medium text-gray-900">Email</Label>
            <Input
              v-model="form.email"
              type="email"
              id="email"
              placeholder="Email"
              class="w-full bg-white dark:bg-gray-200 text-gray-900 dark:text-gray-500 border-gray-300 dark:border-gray-600"
              :class="{ 'border-red-500': form.errors.email }"
            />
            <p v-if="form.errors.email" class="text-red-600 text-sm">{{ form.errors.email }}</p>
          </div>

          <!-- Address -->
          <div class="space-y-2">
            <Label for="address" class="text-sm font-medium text-gray-900">Address</Label>
            <textarea
              v-model="form.address"
              id="address"
              rows="4"
              class="w-full px-3 py-2 border dark:text-gray-500 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all resize-none"
              :class="{ 'border-red-500': form.errors.address }"
              placeholder="Brief address of this supplier..."
            ></textarea>
            <p v-if="form.errors.address" class="text-red-600 text-sm">{{ form.errors.address }}</p>
          </div>

          <!-- Terms & Opening Balance -->
          <div class="flex gap-6">
            <!-- Terms Days -->
            <div class="space-y-2 w-1/2">
              <Label for="terms_days" class="text-sm font-medium text-gray-900">Terms (Days)</Label>
              <Input
                v-model="form.terms_days"
                type="number"
                id="terms_days"
                placeholder="Terms in days"
                class="w-full bg-white dark:bg-gray-200 text-gray-900 dark:text-gray-500 border-gray-300 dark:border-gray-600"
                :class="{ 'border-red-500': form.errors.terms_days }"
              />
              <p v-if="form.errors.terms_days" class="text-red-600 text-sm">{{ form.errors.terms_days }}</p>
            </div>

            <!-- Opening Balance -->
            <div class="space-y-2 w-1/2">
              <Label for="opening_balance" class="text-sm font-medium text-gray-900">Opening Balance</Label>
              <input
                ref="openingBalanceRef"
                v-model="form.opening_balance"
                type="text"
                placeholder="₱ 0.00"
                class="w-full bg-white dark:bg-gray-200 text-gray-900 dark:text-gray-500 border-gray-300 dark:border-gray-600 px-3 py-2 rounded"
              />
              <p v-if="form.errors.opening_balance" class="text-red-600 text-sm">{{ form.errors.opening_balance }}</p>
            </div>
          </div>

          <div class="border-t border-gray-200"></div>

          <!-- Form Actions -->
          <div class="flex items-center justify-between">
            <Link href="/suppliers" class="px-4 py-2 text-sm text-gray-700 hover:text-gray-900 transition-colors">
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
                <span>{{ form.processing ? 'Creating...' : 'Create Supplier' }}</span>
              </Button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>
