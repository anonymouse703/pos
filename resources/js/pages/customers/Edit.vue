<script setup lang="ts">
import { ref } from 'vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { ArrowLeft, Loader2, Upload, User, X } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';
import Input from '@/components/ui/input/Input.vue';
import Label from '@/components/ui/label/Label.vue';
import Button from '@/components/ui/button/Button.vue';
import { type BreadcrumbItem } from '@/types';

const props = defineProps<{
  customer: {
    data: {
        id: number;
        name: string;
        phone: string;
        email: string;
        address: string;
        credit_limit: number | null;
        opening_balance: number | null;
        profile_photo: {
            url: string;
            type: string;
        } | null;
    }
  }
}>();

// Breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Customers', href: '/customers' },
    { title: 'Edit', href: `/customers/${props.customer.data.id}/edit` },
];

// Form typing
interface CustomerForm {
    name: string
    phone: string
    email: string
    address: string
    credit_limit: number | ''
    opening_balance: number | ''
    profile_image: File | null
    remove_profile_image?: boolean
}


// Use Inertia form with pre-filled data
const form = useForm<CustomerForm>({
    name: props.customer.data.name,
    phone: props.customer.data.phone,
    email: props.customer.data.email,
    address: props.customer.data.address,
    credit_limit: props.customer.data.credit_limit ?? '',
    opening_balance: props.customer.data.opening_balance ?? '',
    profile_image: null,
    remove_profile_image: false,
})

// Profile image preview
const profilePreview = ref<string | null>(props.customer.data.profile_photo?.url || null);
const fileInputRef = ref<HTMLInputElement | null>(null);

// Handle file selection
const handleFileChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];

    if (file) {
        form.profile_image = file;
        form.remove_profile_image = false;

        const reader = new FileReader();
        reader.onload = (e) => {
            profilePreview.value = e.target?.result as string;
        };
        reader.readAsDataURL(file);
    }
};

// Remove image
const removeImage = () => {
    form.profile_image = null;
    profilePreview.value = null;
    form.remove_profile_image = true;

    if (fileInputRef.value) {
        fileInputRef.value.value = '';
    }
};

// Trigger file input
const triggerFileInput = () => {
    fileInputRef.value?.click();
};

// Submit form
const submit = () => {
    form.transform((data) => ({
        ...data,
        credit_limit: data.credit_limit ? parseFloat(data.credit_limit.toString()) : null,
        opening_balance: data.opening_balance ? parseFloat(data.opening_balance.toString()) : null,
    }))
    .put(`/customers/${props.customer.data.id}`, {
        onSuccess: () => {
            console.log('Customer updated successfully!');
        },
        onError: (errors) => {
            console.log('Validation errors:', errors);
        },
    });
};
</script>

<template>
    <Head title="Edit Customer" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-4">
            <!-- Header -->
            <div class="flex items-center gap-4">
                <Link href="/customers" class="text-gray-600 hover:text-gray-900 transition-colors">
                    <ArrowLeft class="h-5 w-5" />
                </Link>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Customer</h1>
                    <p class="text-sm text-gray-500 mt-1 dark:text-white">Update customer details</p>
                </div>
            </div>

            <!-- Form Card -->
            <div class="bg-gray-100 rounded-lg border border-gray-200 shadow-sm w-full max-w-5xl mx-auto">
                <form @submit.prevent="submit" class="p-6">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Left Side - Profile Image -->
                        <div class="lg:col-span-1">
                            <div class="space-y-4">
                                <Label class="text-sm font-medium text-gray-900 dark:text-gray-500">
                                    Profile Picture
                                </Label>

                                <div class="flex flex-col items-center space-y-4">
                                    <!-- Image Preview -->
                                    <div class="relative w-48 h-48">
                                        <div class="w-full h-full rounded-full border-4 border-gray-200 dark:border-gray-300 overflow-hidden bg-gray-100 dark:bg-gray-200">
                                            <img v-if="profilePreview" :src="profilePreview" alt="Profile preview"
                                                class="w-full h-full object-cover" />
                                            <div v-else class="flex items-center justify-center w-full h-full">
                                                <User class="h-20 w-20 text-gray-400 dark:text-gray-500" />
                                            </div>
                                        </div>

                                        <button v-if="profilePreview" @click="removeImage" type="button"
                                            class="absolute -top-1 -right-1 z-20 p-1.5 bg-red-500 hover:bg-red-600 text-white rounded-full shadow-lg transition-colors">
                                            <X class="h-4 w-4" />
                                        </button>
                                    </div>

                                    <!-- Upload Button -->
                                    <div class="w-full space-y-2">
                                        <input ref="fileInputRef" type="file" accept="image/*"
                                            @change="handleFileChange" class="hidden" />
                                        <Button type="button" @click="triggerFileInput" variant="outline"
                                            class="w-full border-gray-300 dark:border-gray-500 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-500 flex items-center justify-center gap-2">
                                            <Upload class="h-4 w-4" />
                                            Upload Photo
                                        </Button>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 text-center">
                                            JPG, PNG or GIF (max. 2MB)
                                        </p>
                                        <p v-if="form.errors.profile_image"
                                            class="text-red-600 dark:text-red-400 text-sm text-center">
                                            {{ form.errors.profile_image }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Side - Customer Details -->
                        <div class="lg:col-span-2 space-y-6">
                            <!-- Personal Information -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-500 mb-4">
                                    Personal Information
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <!-- Name -->
                                    <div class="md:col-span-2 space-y-2">
                                        <Label for="name" class="text-sm font-medium text-gray-900 dark:text-gray-500">
                                            Full Name <span class="text-red-500">*</span>
                                        </Label>
                                        <Input v-model="form.name" type="text" id="name" placeholder="John Doe"
                                            class="w-full bg-white dark:bg-gray-200 text-gray-900 dark:text-gray-500 border-gray-300 dark:border-gray-600"
                                            :class="{ 'border-red-500': form.errors.name }" required />
                                        <p v-if="form.errors.name" class="text-red-600 dark:text-red-400 text-sm">
                                            {{ form.errors.name }}
                                        </p>
                                    </div>

                                    <!-- Phone -->
                                    <div class="space-y-2">
                                        <Label for="phone" class="text-sm font-medium text-gray-900 dark:text-gray-500">
                                            Phone Number <span class="text-red-500">*</span>
                                        </Label>
                                        <Input v-model="form.phone" type="tel" id="phone"
                                            placeholder="+63 (000) 000-0000"
                                            class="w-full bg-white dark:bg-gray-200 text-gray-900 dark:text-gray-500 border-gray-300 dark:border-gray-600"
                                            :class="{ 'border-red-500': form.errors.phone }" required />
                                        <p v-if="form.errors.phone" class="text-red-600 dark:text-red-400 text-sm">
                                            {{ form.errors.phone }}
                                        </p>
                                    </div>

                                    <!-- Email -->
                                    <div class="space-y-2">
                                        <Label for="email" class="text-sm font-medium text-gray-900 dark:text-gray-500">
                                            Email Address <span class="text-red-500">*</span>
                                        </Label>
                                        <Input v-model="form.email" type="email" id="email"
                                            placeholder="john@example.com"
                                            class="w-full bg-white dark:bg-gray-200 text-gray-900 dark:text-gray-500 border-gray-300 dark:border-gray-600"
                                            :class="{ 'border-red-500': form.errors.email }" required />
                                        <p v-if="form.errors.email" class="text-red-600 dark:text-red-400 text-sm">
                                            {{ form.errors.email }}
                                        </p>
                                    </div>

                                    <!-- Address -->
                                    <div class="md:col-span-2 space-y-2">
                                        <Label for="address" class="text-sm font-medium text-gray-900 dark:text-gray-500">
                                            Address
                                        </Label>
                                        <textarea v-model="form.address" id="address" rows="3"
                                            class="w-full px-3 py-2 bg-white dark:bg-gray-200 text-gray-900 dark:text-gray-500 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all resize-none placeholder-gray-400 dark:placeholder-gray-500"
                                            :class="{ 'border-red-500': form.errors.address }"
                                            placeholder="Street address, city, state, zip code"></textarea>
                                        <p v-if="form.errors.address" class="text-red-600 dark:text-red-400 text-sm">
                                            {{ form.errors.address }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Financial Information -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-500 mb-4">
                                    Financial Information
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <!-- Credit Limit -->
                                    <div class="space-y-2">
                                        <Label for="credit_limit" class="text-sm font-medium text-gray-900 dark:text-gray-500">
                                            Credit Limit
                                        </Label>
                                        <div class="relative">
                                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 dark:text-gray-400">
                                                ₱
                                            </span>
                                            <Input v-model.number="form.credit_limit" type="number" id="credit_limit"
                                                step="0.01" placeholder="0.00"
                                                class="w-full pl-8 bg-white dark:bg-gray-200 text-gray-900 dark:text-gray-500 border-gray-300 dark:border-gray-600"
                                                :class="{ 'border-red-500': form.errors.credit_limit }" />
                                        </div>
                                        <p v-if="form.errors.credit_limit" class="text-red-600 dark:text-red-400 text-sm">
                                            {{ form.errors.credit_limit }}
                                        </p>
                                    </div>

                                    <!-- Opening Balance -->
                                    <div class="space-y-2">
                                        <Label for="opening_balance" class="text-sm font-medium text-gray-900 dark:text-gray-500">
                                            Opening Balance
                                        </Label>
                                        <div class="relative">
                                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 dark:text-gray-400">
                                                ₱
                                            </span>
                                            <Input v-model.number="form.opening_balance" type="number" id="opening_balance"
                                                step="0.01" placeholder="0.00"
                                                class="w-full pl-8 bg-white dark:bg-gray-200 text-gray-900 dark:text-gray-500 border-gray-300 dark:border-gray-600"
                                                :class="{ 'border-red-500': form.errors.opening_balance }" />
                                        </div>
                                        <p v-if="form.errors.opening_balance" class="text-red-600 dark:text-red-400 text-sm">
                                            {{ form.errors.opening_balance }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Divider -->
                            <div class="border-t border-gray-200 dark:border-gray-700"></div>

                            <!-- Form Actions -->
                            <div class="flex items-center justify-between">
                                <Link href="/customers"
                                    class="px-4 py-2 text-sm text-gray-700 dark:text-gray-500 hover:text-gray-900 dark:hover:text-gray-100 transition-colors">
                                    Cancel
                                </Link>
                                <div class="flex gap-3">
                                    <Button type="button" @click="form.reset(); removeImage()" variant="outline"
                                        :disabled="form.processing"
                                        class="border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-700">
                                        Reset
                                    </Button>
                                    <Button type="submit" :disabled="form.processing"
                                        class="bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                                        <Loader2 v-if="form.processing" class="h-4 w-4 animate-spin" />
                                        <span>{{ form.processing ? 'Updating...' : 'Update Customer' }}</span>
                                    </Button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
