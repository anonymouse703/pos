<script setup lang="ts">
import { watch } from 'vue';
import Input from '@/components/ui/input/Input.vue';
import Label from '@/components/ui/label/Label.vue';
import Button from '@/components/ui/button/Button.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import PurchaseItemsSection from './partials/PurchaseItemsSection.vue';
import PurchaseTotals from './partials/PurchaseTotals.vue';
import type { PurchaseItem } from './partials/PurchaseItemsSection.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { ArrowLeft, Loader2 } from 'lucide-vue-next';

const props = defineProps<{
    suppliers: {
        id: number;
        name: string;
    }[];
    payment_methods: {
        key: string;
        label: string;
    }[];
    payment_statuses: {
        key: string;
        label: string;
    }[];
    products: {
        id: number;
        name: string;
    }[];
}>();

const suppliers = props.suppliers;
const payment_methods = props.payment_methods;
const products = props.products;
const payment_statuses = props.payment_statuses;

// Breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Purchases', href: '/purchases' },
    { title: 'Create', href: '/purchases/create' },
];

// Form typing
interface PurchaseForm {
    supplier_id: number;
    invoice_number: string;
    purchase_date: string;
    due_date: string;
    subtotal: number;
    tax: number;
    discount: number;
    shipping: number;
    total: number;
    amount_paid: number;
    balance: number;
    payment_method: string;
    notes: string;
    reference: string;
    items: PurchaseItem[];
    status: string;
}

// Use Inertia form with typing
const form = useForm<PurchaseForm>({
    supplier_id: 0,
    invoice_number: '',
    purchase_date: new Date().toISOString().split('T')[0],
    due_date: '',
    subtotal: 0,
    tax: 0,
    discount: 0,
    shipping: 0,
    total: 0,
    amount_paid: 0,
    balance: 0,
    payment_method: '',
    notes: '',
    reference: '',
    items: [],
    status: '',
});

// Add new item
const addItem = () => {
    form.items.push({
        product_id: 0,
        quantity: 1,
        unit_cost: 0,
        discount: 0,
        tax_rate: 0,
        tax_amount: 0,
        total: 0,
        batch_number: '',
        expiry_date: '',
    });
};

// Remove item
const removeItem = (index: number) => {
    form.items.splice(index, 1);
    calculateTotals();
};

// Calculate purchase totals
const calculateTotals = () => {
    // Calculate subtotal from all items
    form.subtotal = form.items.reduce((sum, item) => {
        return sum + (item.quantity * item.unit_cost);
    }, 0);

    // Calculate total tax from items
    const itemsTax = form.items.reduce((sum, item) => sum + item.tax_amount, 0);

    // Calculate total discount from items
    const itemsDiscount = form.items.reduce((sum, item) => sum + item.discount, 0);

    form.tax = itemsTax;
    form.discount = itemsDiscount;

    // Calculate final total
    form.total = form.subtotal - form.discount + form.tax + form.shipping;

    // Calculate balance
    form.balance = form.total - form.amount_paid;
};

// Handle items update from child component
const handleItemsUpdate = () => {
    calculateTotals();
};

// Watch for changes in shipping and amount paid
watch([() => form.shipping, () => form.amount_paid], () => {
    calculateTotals();
});

// Submit handler
const submit = () => {
    form.post('/purchases', {
        onSuccess: () => {
            console.log('Purchase created successfully!');
            form.reset();
        },
        onError: (errors) => {
            console.log('Validation errors:', errors);
        },
    });
};
</script>

<template>

    <Head title="Create Purchase" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-4">
            <!-- Header -->
            <div class="flex items-center gap-4">
                <Link href="/purchases" class="text-gray-600 hover:text-gray-900 transition-colors">
                    <ArrowLeft class="h-5 w-5" />
                </Link>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Create New Purchase</h1>
                    <p class="text-sm text-gray-500 mt-1 dark:text-white">Add a new purchase to your records</p>
                </div>
            </div>

            <!-- Form Card -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm w-full max-w-7xl mx-auto">
                <form @submit.prevent="submit" class="p-6 space-y-8">

                    <!-- Purchase Details Section -->
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Purchase Details</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-4">
                            <!-- Purchase Date -->
                            <div class="space-y-2">
                                <Label for="purchase_date"
                                    class="text-sm font-medium text-gray-900 dark:text-gray-500">Purchase Date *</Label>
                                <Input id="purchase_date" v-model="form.purchase_date" type="date"
                                    class="w-full bg-white dark:bg-gray-200 text-gray-900 dark:text-gray-500 border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2" />
                                <span v-if="form.errors.purchase_date" class="text-sm text-red-600">{{
                                    form.errors.purchase_date }}</span>
                            </div>

                            <!-- Invoice Number -->
                            <div class="space-y-2">
                                <Label for="invoice_number"
                                    class="text-sm font-medium text-gray-900 dark:text-gray-500">Invoice Number
                                    *</Label>
                                <Input id="invoice_number" v-model="form.invoice_number" type="text"
                                    class="w-full px-4 py-2 rounded border bg-white dark:bg-gray-200 text-gray-900 dark:text-gray-500"
                                    placeholder="INV-001" required />
                                <span v-if="form.errors.invoice_number" class="text-sm text-red-600">{{
                                    form.errors.invoice_number }}</span>
                            </div>

                            <!-- Supplier -->
                            <div class="space-y-2">
                                <Label for="supplier_id"
                                    class="text-sm font-medium text-gray-900 dark:text-gray-500">Supplier *</Label>
                                <select v-model="form.supplier_id" id="supplier_id"
                                    class="w-full bg-white dark:bg-gray-200 text-gray-900 dark:text-gray-500 border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2"
                                    :class="{ 'border-red-500': form.errors.supplier_id }" required>
                                    <option value="" disabled>Select Supplier</option>
                                    <option v-for="supplier in suppliers" :key="supplier.id" :value="supplier.id">
                                        {{ supplier.name }}
                                    </option>
                                </select>
                                <p v-if="form.errors.supplier_id" class="text-red-600 dark:text-red-400 text-sm">
                                    {{ form.errors.supplier_id }}
                                </p>
                                <span v-if="form.errors.supplier_id" class="text-sm text-red-600">{{
                                    form.errors.supplier_id }}</span>
                            </div>

                            <!-- Reference -->
                            <div class="space-y-2">
                                <Label for="reference"
                                    class="text-sm font-medium text-gray-900 dark:text-gray-500">Reference</Label>
                                <Input id="reference" v-model="form.reference" type="text"
                                    class="w-full px-4 py-2 rounded border bg-white dark:bg-gray-200 text-gray-900 dark:text-gray-500"
                                    placeholder="Reference number" />
                            </div>

                            <!-- Payment Method -->
                            <div class="space-y-2">
                                <Label for="payment_method"
                                    class="text-sm font-medium text-gray-900 dark:text-gray-500">Payment Method</Label>
                                <select v-model="form.payment_method" id="payment_method"
                                    class="w-full bg-white dark:bg-gray-200 text-gray-900 dark:text-gray-500 border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2"
                                    :class="{ 'border-red-500': form.errors.payment_method }" required>
                                    <option value="" disabled>Select Payment Method</option>
                                    <option v-for="payment_method in payment_methods" :key="payment_method.key"
                                        :value="payment_method.key">
                                        {{ payment_method.label  }}
                                    </option>
                                </select>
                                <p v-if="form.errors.payment_method" class="text-red-600 dark:text-red-400 text-sm">
                                    {{ form.errors.payment_method }}
                                </p>
                            </div>

                            <!-- Due Date -->
                            <div class="space-y-2">
                                <Label for="due_date" class="text-sm font-medium text-gray-900 dark:text-gray-500">Due
                                    Date</Label>
                                <Input id="due_date" v-model="form.due_date" type="date"
                                    class="w-full bg-white dark:bg-gray-200 text-gray-900 dark:text-gray-500 border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2" />
                                <span v-if="form.errors.due_date" class="text-sm text-red-600">{{ form.errors.due_date
                                }}</span>
                            </div>

                            <!-- Payment Status -->
                            <div class="space-y-2">
                                <Label for="payment_status"
                                    class="text-sm font-medium text-gray-900 dark:text-gray-500">Payment Status</Label>
                                <select v-model="form.status" id="payment_status"
                                    class="w-full bg-white dark:bg-gray-200 text-gray-900 dark:text-gray-500 border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2"
                                    :class="{ 'border-red-500': form.errors.status }" required>
                                    <option value="" disabled>Select Payment Status</option>
                                    <option v-for="payment_status in payment_statuses" :key="payment_status.key"
                                        :value="payment_status.key">
                                        {{ payment_status.label }}
                                    </option>
                                </select>
                                <p v-if="form.errors.status" class="text-red-600 dark:text-red-400 text-sm">
                                    {{ form.errors.status }}
                                </p>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="space-y-2 mt-4">
                            <Label for="notes"
                                class="text-sm font-medium text-gray-900 dark:text-gray-500">Notes</Label>
                            <textarea id="notes" v-model="form.notes" rows="3"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Additional notes..."></textarea>
                        </div>
                    </div>

                    <!-- Divider -->
                    <div class="border-t border-gray-200"></div>

                    <!-- Purchase Items Section (Component) -->
                    <PurchaseItemsSection :items="form.items" :products="products" @add-item="addItem"
                        @remove-item="removeItem" @update-items="handleItemsUpdate" />

                    <!-- Divider -->
                    <div class="border-t border-gray-200"></div>

                    <!-- Totals Section (Component) -->
                    <PurchaseTotals :subtotal="form.subtotal" :discount="form.discount" :tax="form.tax"
                        :shipping="form.shipping" :total="form.total" :amount-paid="form.amount_paid"
                        :balance="form.balance" @update:shipping="form.shipping = $event"
                        @update:amount-paid="form.amount_paid = $event" />

                    <!-- Divider -->
                    <div class="border-t border-gray-200"></div>

                    <!-- Form Actions -->
                    <div class="flex items-center justify-between">
                        <Link href="/purchases"
                            class="px-4 py-2 text-sm text-gray-700 hover:text-gray-900 transition-colors">
                            Cancel
                        </Link>
                        <div class="flex gap-3">
                            <Button type="button" @click="form.reset()" variant="outline" :disabled="form.processing"
                                class="border-gray-300 text-gray-700 hover:bg-gray-50">
                                Reset
                            </Button>
                            <Button type="submit" :disabled="form.processing || form.items.length === 0"
                                class="bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                                <Loader2 v-if="form.processing" class="h-4 w-4 animate-spin" />
                                <span>{{ form.processing ? 'Creating...' : 'Create Purchase' }}</span>
                            </Button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>