<script setup lang="ts">
import { ref } from 'vue';
import Input from '@/components/ui/input/Input.vue';
import Label from '@/components/ui/label/Label.vue';
import Button from '@/components/ui/button/Button.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { ArrowLeft, Loader2, Upload, User, X } from 'lucide-vue-next';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Customers', href: '/customers' },
    { title: 'Create', href: dashboard().url },
];

interface CustomerForm {
    name: string;
    phone: string;
    email: string;
    address: string;
    credit_limit: string;
    opening_balance: string;
    profile_image: File | null;
}

const form = useForm<CustomerForm>({
    name: '',
    phone: '',
    email: '',
    address: '',
    credit_limit: '',
    opening_balance: '',
    profile_image: null,
});

// Profile image preview
const profilePreview = ref<string | null>(null);
const fileInputRef = ref<HTMLInputElement | null>(null);

const handleFileChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];
    
    if (file) {
        form.profile_image = file;
        
        // Create preview
        const reader = new FileReader();
        reader.onload = (e) => {
            profilePreview.value = e.target?.result as string;
        };
        reader.readAsDataURL(file);
    }
};

const removeImage = () => {
    form.profile_image = null;
    profilePreview.value = null;
    if (fileInputRef.value) {
        fileInputRef.value.value = '';
    }
};

const triggerFileInput = () => {
    fileInputRef.value?.click();
};

const submit = () => {
    // Transform data before sending
    form.transform((data) => ({
        ...data,
        credit_limit: data.credit_limit ? parseFloat(data.credit_limit) : null,
        opening_balance: data.opening_balance ? parseFloat(data.opening_balance) : null,
    })).post('/customers', {
        onSuccess: () => {
            console.log('Customer created successfully!');
            form.reset();
            profilePreview.value = null;
        },
        onError: (errors) => {
            console.log('Validation errors:', errors);
        },
    });
};
</script>

<template>
    <Head title="Create Customer" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col items-center justify-start gap-6 rounded-xl p-4 bg-gray-100 dark:bg-gray-900">
            <div class="w-full max-w-6xl space-y-6">
                <!-- Header -->
                <div class="flex items-center gap-4">
                    <Link href="/customers" class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100 transition-colors">
                        <ArrowLeft class="h-5 w-5" />
                    </Link>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Create New Customer</h1>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            Add a new customer to your database
                        </p>
                    </div>
                </div>

                <!-- Form Card -->
                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm">
                    <form @submit.prevent="submit" class="p-6">
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                            <!-- Left Side - Profile Image Upload -->
                            <div class="lg:col-span-1">
                                <div class="space-y-4">
                                    <Label class="text-sm font-medium text-gray-900 dark:text-white">
                                        Profile Picture
                                    </Label>
                                    
                                    <!-- Image Upload Area -->
                                    <div class="flex flex-col items-center space-y-4">
                                        <!-- Preview or Placeholder -->
                                        <div class="relative w-48 h-48 rounded-full border-4 border-gray-200 dark:border-gray-700 overflow-hidden bg-gray-100 dark:bg-gray-700">
                                            <img
                                                v-if="profilePreview"
                                                :src="profilePreview"
                                                alt="Profile preview"
                                                class="w-full h-full object-cover"
                                            />
                                            <div v-else class="flex items-center justify-center w-full h-full">
                                                <User class="h-20 w-20 text-gray-400 dark:text-gray-500" />
                                            </div>
                                            
                                            <!-- Remove button -->
                                            <button
                                                v-if="profilePreview"
                                                @click="removeImage"
                                                type="button"
                                                class="absolute top-2 right-2 p-1.5 bg-red-500 hover:bg-red-600 text-white rounded-full transition-colors"
                                            >
                                                <X class="h-4 w-4" />
                                            </button>
                                        </div>

                                        <!-- Upload Button -->
                                        <div class="w-full space-y-2">
                                            <input
                                                ref="fileInputRef"
                                                type="file"
                                                accept="image/*"
                                                @change="handleFileChange"
                                                class="hidden"
                                            />
                                            <Button
                                                type="button"
                                                @click="triggerFileInput"
                                                variant="outline"
                                                class="w-full border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 flex items-center justify-center gap-2"
                                            >
                                                <Upload class="h-4 w-4" />
                                                Upload Photo
                                            </Button>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 text-center">
                                                JPG, PNG or GIF (max. 2MB)
                                            </p>
                                            <p v-if="form.errors.profile_image" class="text-red-600 dark:text-red-400 text-sm text-center">
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
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                                        Personal Information
                                    </h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <!-- Name -->
                                        <div class="md:col-span-2 space-y-2">
                                            <Label for="name" class="text-sm font-medium text-gray-900 dark:text-white">
                                                Full Name <span class="text-red-500">*</span>
                                            </Label>
                                            <Input
                                                v-model="form.name"
                                                type="text"
                                                id="name"
                                                placeholder="John Doe"
                                                class="w-full bg-white dark:bg-gray-700 text-gray-900 dark:text-white border-gray-300 dark:border-gray-600"
                                                :class="{ 'border-red-500': form.errors.name }"
                                                required
                                            />
                                            <p v-if="form.errors.name" class="text-red-600 dark:text-red-400 text-sm">
                                                {{ form.errors.name }}
                                            </p>
                                        </div>

                                        <!-- Phone -->
                                        <div class="space-y-2">
                                            <Label for="phone" class="text-sm font-medium text-gray-900 dark:text-white">
                                                Phone Number <span class="text-red-500">*</span>
                                            </Label>
                                            <Input
                                                v-model="form.phone"
                                                type="tel"
                                                id="phone"
                                                placeholder="+1 (555) 000-0000"
                                                class="w-full bg-white dark:bg-gray-700 text-gray-900 dark:text-white border-gray-300 dark:border-gray-600"
                                                :class="{ 'border-red-500': form.errors.phone }"
                                                required
                                            />
                                            <p v-if="form.errors.phone" class="text-red-600 dark:text-red-400 text-sm">
                                                {{ form.errors.phone }}
                                            </p>
                                        </div>

                                        <!-- Email -->
                                        <div class="space-y-2">
                                            <Label for="email" class="text-sm font-medium text-gray-900 dark:text-white">
                                                Email Address <span class="text-red-500">*</span>
                                            </Label>
                                            <Input
                                                v-model="form.email"
                                                type="email"
                                                id="email"
                                                placeholder="john@example.com"
                                                class="w-full bg-white dark:bg-gray-700 text-gray-900 dark:text-white border-gray-300 dark:border-gray-600"
                                                :class="{ 'border-red-500': form.errors.email }"
                                                required
                                            />
                                            <p v-if="form.errors.email" class="text-red-600 dark:text-red-400 text-sm">
                                                {{ form.errors.email }}
                                            </p>
                                        </div>

                                        <!-- Address -->
                                        <div class="md:col-span-2 space-y-2">
                                            <Label for="address" class="text-sm font-medium text-gray-900 dark:text-white">
                                                Address
                                            </Label>
                                            <textarea
                                                v-model="form.address"
                                                id="address"
                                                rows="3"
                                                class="w-full px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all resize-none placeholder-gray-400 dark:placeholder-gray-500"
                                                :class="{ 'border-red-500': form.errors.address }"
                                                placeholder="Street address, city, state, zip code"
                                            ></textarea>
                                            <p v-if="form.errors.address" class="text-red-600 dark:text-red-400 text-sm">
                                                {{ form.errors.address }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Financial Information -->
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                                        Financial Information
                                    </h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <!-- Credit Limit -->
                                        <div class="space-y-2">
                                            <Label for="credit_limit" class="text-sm font-medium text-gray-900 dark:text-white">
                                                Credit Limit
                                            </Label>
                                            <div class="relative">
                                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 dark:text-gray-400">
                                                    $
                                                </span>
                                                <Input
                                                    v-model="form.credit_limit"
                                                    type="number"
                                                    id="credit_limit"
                                                    step="0.01"
                                                    placeholder="0.00"
                                                    class="w-full pl-8 bg-white dark:bg-gray-700 text-gray-900 dark:text-white border-gray-300 dark:border-gray-600"
                                                    :class="{ 'border-red-500': form.errors.credit_limit }"
                                                />
                                            </div>
                                            <p v-if="form.errors.credit_limit" class="text-red-600 dark:text-red-400 text-sm">
                                                {{ form.errors.credit_limit }}
                                            </p>
                                        </div>

                                        <!-- Opening Balance -->
                                        <div class="space-y-2">
                                            <Label for="opening_balance" class="text-sm font-medium text-gray-900 dark:text-white">
                                                Opening Balance
                                            </Label>
                                            <div class="relative">
                                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 dark:text-gray-400">
                                                    $
                                                </span>
                                                <Input
                                                    v-model="form.opening_balance"
                                                    type="number"
                                                    id="opening_balance"
                                                    step="0.01"
                                                    placeholder="0.00"
                                                    class="w-full pl-8 bg-white dark:bg-gray-700 text-gray-900 dark:text-white border-gray-300 dark:border-gray-600"
                                                    :class="{ 'border-red-500': form.errors.opening_balance }"
                                                />
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
                                    <Link
                                        href="/customers"
                                        class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-gray-100 transition-colors"
                                    >
                                        Cancel
                                    </Link>
                                    <div class="flex gap-3">
                                        <Button
                                            type="button"
                                            @click="form.reset(); removeImage()"
                                            variant="outline"
                                            :disabled="form.processing"
                                            class="border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700"
                                        >
                                            Reset
                                        </Button>
                                        <Button
                                            type="submit"
                                            :disabled="form.processing"
                                            class="bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                                        >
                                            <Loader2 v-if="form.processing" class="h-4 w-4 animate-spin" />
                                            <span>{{ form.processing ? 'Creating...' : 'Create Customer' }}</span>
                                        </Button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>