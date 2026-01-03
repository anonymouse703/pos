I have this money mask but I noticed something, when I input cost price and click outside it will format but when I
click selling price the cost price display is not mask anymore vice versa when creating selling price and click either
cost price or re-order


<script setup lang="ts">
import { ref } from 'vue';
import Input from '@/components/ui/input/Input.vue';
import Label from '@/components/ui/label/Label.vue';
import Button from '@/components/ui/button/Button.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { ArrowLeft, Loader2, Upload, X, PackageIcon } from 'lucide-vue-next';
import { useCurrencyInput } from 'vue-currency-input';


const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Products', href: '/products' },
    { title: 'Create', href: dashboard().url },
];

interface ProductForm {
    barcode: string;
    name: string;
    category_id: number | null;
    unit: string;
    cost_price: number | null;
    selling_price: number | null;
    reorder_level: number;
    product_photo: File | null;
}

const props = defineProps<{
    categories: {
        id: number;
        name: string;
    }[];
    units: {
        key: string;
        value: string;
    }[];
}>();

const categories = props.categories;
const units = props.units;

console.log(units);

const form = useForm<ProductForm>({
    barcode: '',
    name: '',
    category_id: null,
    unit: '',
    cost_price: 0,
    selling_price: 0,
    reorder_level: 0,
    product_photo: null,
});

const currencyOptions = {
    locale: 'en-PH',
    currency: 'PHP',
    precision: 2,
    autoDecimalMode: true,
    distractionFree: false,
    useGrouping: true,
};

const { inputRef: costPriceRef } = useCurrencyInput(currencyOptions);
const { inputRef: sellingPriceRef } = useCurrencyInput(currencyOptions);


// Profile image preview
const profilePreview = ref<string | null>(null);
const fileInputRef = ref<HTMLInputElement | null>(null);

const handleFileChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];

    if (file) {
        form.product_photo = file;

        // Create preview
        const reader = new FileReader();
        reader.onload = (e) => {
            profilePreview.value = e.target?.result as string;
        };
        reader.readAsDataURL(file);
    }
};

const removeImage = () => {
    form.product_photo = null;
    profilePreview.value = null;
    if (fileInputRef.value) {
        fileInputRef.value.value = '';
    }
};

const triggerFileInput = () => {
    fileInputRef.value?.click();
};

const submit = () => {
    form.transform((data) => ({
        ...data
    })).post('/products', {
        onSuccess: () => {
            console.log('Product created successfully!');
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

    <Head title="Create Product" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-4">
            <!-- Header -->
            <div class="flex items-center gap-4">
                <Link href="/products" class="text-gray-600 hover:text-gray-900 transition-colors">
                    <ArrowLeft class="h-5 w-5" />
                </Link>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Create New Product</h1>
                    <p class="text-sm text-gray-500 mt-1 dark:text-white">Add a new product to your database
                    </p>
                </div>
            </div>

            <!-- Form Card -->
            <div class="bg-gray-100 rounded-lg border border-gray-200 shadow-sm w-full max-w-5xl mx-auto">
                <form @submit.prevent="submit" class="p-6">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Left Side - Profile Image Upload -->
                        <div class="lg:col-span-1">
                            <div class="space-y-4">
                                <Label class="text-sm font-medium text-gray-900 dark:text-gray-500">
                                    Product Image
                                </Label>

                                <!-- Image Upload Area -->
                                <div class="flex flex-col items-center space-y-4">
                                    <!-- Preview or Placeholder -->
                                    <div class="relative w-48 h-48">
                                        <!-- Circle -->
                                        <div
                                            class="w-full h-full  border-4 border-gray-200 dark:border-gray-300 overflow-hidden bg-gray-100 dark:bg-gray-200">
                                            <img v-if="profilePreview" :src="profilePreview" alt="Profile preview"
                                                class="w-full h-full object-cover" />
                                            <div v-else class="flex items-center justify-center w-full h-full">
                                                <PackageIcon class="h-20 w-20 text-gray-400 dark:text-gray-500" />
                                            </div>
                                        </div>

                                        <!-- Remove button (NOT clipped) -->
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
                                        <p v-if="form.errors.product_photo"
                                            class="text-red-600 dark:text-red-400 text-sm text-center">
                                            {{ form.errors.product_photo }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Side - Product Details -->
                        <div class="lg:col-span-2 space-y-6">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-500 mb-4">
                                    Product Information
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="md:col-span-2 space-y-2">
                                        <Label for="barcode"
                                            class="text-sm font-medium text-gray-900 dark:text-gray-500">
                                            Barcode
                                        </Label>
                                        <Input v-model="form.barcode" type="text" id="barcode"
                                            placeholder="BARCODE12345"
                                            class="w-full bg-white dark:bg-gray-200 text-gray-900 dark:text-gray-500 border-gray-300 dark:border-gray-600"
                                            :class="{ 'border-red-500': form.errors.barcode }" />
                                        <p v-if="form.errors.barcode" class="text-red-600 dark:text-red-400 text-sm">
                                            {{ form.errors.barcode }}
                                        </p>
                                    </div>
                                    <div class="md:col-span-2 space-y-2">
                                        <Label for="name" class="text-sm font-medium text-gray-900 dark:text-gray-500">
                                            Product Name <span class="text-red-500">*</span>
                                        </Label>
                                        <Input v-model="form.name" type="text" id="name" placeholder="Abc Product"
                                            class="w-full bg-white dark:bg-gray-200 text-gray-900 dark:text-gray-500 border-gray-300 dark:border-gray-600"
                                            :class="{ 'border-red-500': form.errors.name }" required />
                                        <p v-if="form.errors.name" class="text-red-600 dark:text-red-400 text-sm">
                                            {{ form.errors.name }}
                                        </p>
                                    </div>
                                    <div class="md:col-span-2 space-y-2">
                                        <Label for="category"
                                            class="text-sm font-medium text-gray-900 dark:text-gray-500">
                                            Category <span class="text-red-500">*</span>
                                        </Label>
                                        <select v-model="form.category_id" id="category"
                                            class="w-full bg-white dark:bg-gray-200 text-gray-900 dark:text-gray-500 border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2"
                                            :class="{ 'border-red-500': form.errors.category_id }" required>
                                            <option value="" disabled>Select Category</option>
                                            <option v-for="category in categories" :key="category.id"
                                                :value="category.id">
                                                {{ category.name }}
                                            </option>
                                        </select>
                                        <p v-if="form.errors.category_id"
                                            class="text-red-600 dark:text-red-400 text-sm">
                                            {{ form.errors.category_id }}
                                        </p>
                                    </div>
                                    <div class="md:col-span-2 space-y-2">
                                        <Label for="category"
                                            class="text-sm font-medium text-gray-900 dark:text-gray-500">
                                            Unit <span class="text-red-500">*</span>
                                        </Label>
                                        <select v-model="form.unit" id="category"
                                            class="w-full bg-white dark:bg-gray-200 text-gray-900 dark:text-gray-500 border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2"
                                            :class="{ 'border-red-500': form.errors.unit }" required>
                                            <option value="" disabled>Select Unit</option>
                                            <option v-for="unit in units" :key="unit.key" :value="unit.key">
                                                {{ unit.key }}
                                            </option>
                                        </select>
                                        <p v-if="form.errors.unit" class="text-red-600 dark:text-red-400 text-sm">
                                            {{ form.errors.unit }}
                                        </p>
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-12 gap-4 mt-4">
                                    <!-- Cost Price -->
                                    <div class="md:col-span-4 space-y-2">
                                        <Label class="text-sm font-medium text-gray-900 dark:text-gray-500">
                                            Cost Price <span class="text-red-500">*</span>
                                        </Label>
                                        <input ref="costPriceRef" v-model="form.cost_price" type="text"
                                            placeholder="₱ 0.00"
                                            class="w-full px-4 py-2 rounded border bg-white dark:bg-gray-200 text-gray-900 dark:text-gray-500" />
                                    </div>

                                    <!-- Selling Price -->
                                    <div class="md:col-span-4 space-y-2">
                                        <Label class="text-sm font-medium text-gray-900 dark:text-gray-500">
                                            Selling Price <span class="text-red-500">*</span>
                                        </Label>
                                        <input ref="sellingPriceRef" v-model="form.selling_price" type="text"
                                            placeholder="₱ 0.00"
                                            class="w-full px-4 py-2 rounded border bg-white dark:bg-gray-200 text-gray-900 dark:text-gray-500" />
                                    </div>

                                    <!-- Reorder Level -->
                                    <div class="md:col-span-3 space-y-2">
                                        <Label class="text-sm font-medium text-gray-900 dark:text-gray-500">
                                            Reorder Level <span class="text-red-500">*</span>
                                        </Label>
                                        <input v-model="form.reorder_level" type="number" placeholder="0"
                                            class="w-full px-4 py-2 rounded border bg-white dark:bg-gray-200 text-gray-900 dark:text-gray-500" />
                                    </div>
                                </div>
                            </div>

                            <!-- Divider -->
                            <div class="border-t border-gray-200 dark:border-gray-700"></div>

                            <!-- Form Actions -->
                            <div class="flex items-center justify-between">
                                <Link href="/products"
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
                                        <span>{{ form.processing ? 'Creating...' : 'Create Product' }}</span>
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