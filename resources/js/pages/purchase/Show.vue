<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { ArrowLeft, Calendar, FileText, CreditCard, Package, User, Edit, Trash2, Printer } from 'lucide-vue-next';
import Button from '@/components/ui/button/Button.vue';

// Purchase Item Interface
interface PurchaseItem {
    id: number;
    product_id: number;
    product: {
        id: number;
        name: string;
    };
    quantity: number;
    cost_price: number;
    unit_cost: number;
    discount: number;
    tax_rate: number;
    tax_amount: number;
    total: number;
    batch_number: string;
    expiry_date: string;
}

// Purchase Interface
interface Purchase {
    id: number;
    supplier_id: number;
    supplier: {
        id: number;
        name: string;
        email?: string;
        phone?: string;
    };
    invoice_no: string;
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
    payment_status: string;
    notes: string;
    reference: string;
    items: PurchaseItem[];
    created_at: string;
    updated_at: string;
}

const props = defineProps<{
    purchase: Purchase;
}>();

console.log(props.purchase);

// Breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Purchases', href: '/purchases' },
    { title: `Purchase #${props.purchase.invoice_no}`, href: `/purchases/${props.purchase.id}` },
];

// Format date
const formatDate = (date: string) => {
    if (!date) return 'N/A';
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};

// Format currency
const formatCurrency = (amount: number) => {
    return `₱${Number(amount).toFixed(2)}`;
};

// Get status badge color
const getStatusColor = (payment_status: string) => {
    if (!payment_status) return 'bg-gray-100 text-gray-800';

    const colors: Record<string, string> = {
        'paid': 'bg-green-100 text-green-800',
        'partial': 'bg-yellow-100 text-yellow-800',
        'unpaid': 'bg-red-100 text-red-800',
        'pending': 'bg-blue-100 text-blue-800',
    };
    return colors[payment_status.toLowerCase()] || 'bg-gray-100 text-gray-800';
};

// Get payment method label
const getPaymentMethodLabel = (method: string) => {
    const methods: Record<string, string> = {
        'cash': 'Cash',
        'bank_transfer': 'Bank Transfer',
        'credit_card': 'Credit Card',
        'check': 'Check',
    };
    return methods[method] || method;
};

// Print function
const handlePrint = () => {
    window.print();
};
</script>

<template>

    <Head :title="`Purchase #${purchase.invoice_no}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-4">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <Link href="/purchases" class="text-gray-600 hover:text-gray-900 transition-colors">
                        <ArrowLeft class="h-5 w-5" />
                    </Link>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                            Purchase #{{ purchase.invoice_no }}
                        </h1>
                        <p class="text-sm text-gray-500 mt-1">
                            Created on {{ formatDate(purchase.created_at) }}
                        </p>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-3 print:hidden">
                    <Button type="button" variant="outline" @click="handlePrint" class="flex items-center gap-2">
                        <Printer class="h-4 w-4" />
                        Print
                    </Button>
                    <Link :href="`/purchases/${purchase.id}/edit`">
                        <Button variant="outline" class="flex items-center gap-2">
                            <Edit class="h-4 w-4" />
                            Edit
                        </Button>
                    </Link>
                    <Button variant="outline"
                        class="flex items-center gap-2 text-red-600 hover:text-red-700 hover:bg-red-50 border-red-300">
                        <Trash2 class="h-4 w-4" />
                        Delete
                    </Button>
                </div>
            </div>

            <!-- Main Content -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="p-6 space-y-8">

                    <!-- Purchase Details Section -->
                    <div>
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-lg font-semibold text-gray-900">Purchase Details</h2>
                            <span v-if="purchase.payment_status" class="px-3 py-1 rounded-full text-sm font-medium"
                                :class="getStatusColor(purchase.payment_status)">
                                {{ purchase.payment_status.charAt(0).toUpperCase() + purchase.payment_status.slice(1) }}
                            </span>
                            <span v-else class="px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                N/A
                            </span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <!-- Supplier Info -->
                            <div class="space-y-2">
                                <div class="flex items-center gap-2 text-sm text-gray-500">
                                    <User class="h-4 w-4" />
                                    <span class="font-medium">Supplier</span>
                                </div>
                                <p class="text-gray-900 font-medium">{{ purchase.supplier.name }}</p>
                                <p v-if="purchase.supplier.email" class="text-sm text-gray-600">
                                    {{ purchase.supplier.email }}
                                </p>
                                <p v-if="purchase.supplier.phone" class="text-sm text-gray-600">
                                    {{ purchase.supplier.phone }}
                                </p>
                            </div>

                            <!-- Purchase Date -->
                            <div class="space-y-2">
                                <div class="flex items-center gap-2 text-sm text-gray-500">
                                    <Calendar class="h-4 w-4" />
                                    <span class="font-medium">Purchase Date</span>
                                </div>
                                <p class="text-gray-900">{{ formatDate(purchase.purchase_date) }}</p>
                            </div>

                            <!-- Due Date -->
                            <div class="space-y-2">
                                <div class="flex items-center gap-2 text-sm text-gray-500">
                                    <Calendar class="h-4 w-4" />
                                    <span class="font-medium">Due Date</span>
                                </div>
                                <p class="text-gray-900">{{ formatDate(purchase.due_date) }}</p>
                            </div>

                            <!-- Invoice Number -->
                            <div class="space-y-2">
                                <div class="flex items-center gap-2 text-sm text-gray-500">
                                    <FileText class="h-4 w-4" />
                                    <span class="font-medium">Invoice Number</span>
                                </div>
                                <p class="text-gray-900 font-mono">{{ purchase.invoice_no }}</p>
                            </div>

                            <!-- Reference -->
                            <div class="space-y-2" v-if="purchase.reference">
                                <div class="flex items-center gap-2 text-sm text-gray-500">
                                    <FileText class="h-4 w-4" />
                                    <span class="font-medium">Reference</span>
                                </div>
                                <p class="text-gray-900">{{ purchase.reference }}</p>
                            </div>

                            <!-- Payment Method -->
                            <div class="space-y-2">
                                <div class="flex items-center gap-2 text-sm text-gray-500">
                                    <CreditCard class="h-4 w-4" />
                                    <span class="font-medium">Payment Method</span>
                                </div>
                                <p class="text-gray-900">{{ getPaymentMethodLabel(purchase.payment_method) }}</p>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div v-if="purchase.notes" class="mt-6 space-y-2">
                            <div class="text-sm text-gray-500 font-medium">Notes</div>
                            <p class="text-gray-900 bg-gray-50 p-4 rounded-lg">{{ purchase.notes }}</p>
                        </div>
                    </div>

                    <!-- Divider -->
                    <div class="border-t border-gray-200"></div>

                    <!-- Purchase Items Section -->
                    <div>
                        <div class="flex items-center gap-2 mb-6">
                            <Package class="h-5 w-5 text-gray-600" />
                            <h2 class="text-lg font-semibold text-gray-900">Purchase Items</h2>
                        </div>

                        <!-- Items Table -->
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="border-b border-gray-200 bg-gray-50">
                                        <th
                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Product
                                        </th>
                                        <th
                                            class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Quantity
                                        </th>
                                        <th
                                            class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Cost Price
                                        </th>
                                        <th
                                            class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Unit Cost
                                        </th>
                                        <th
                                            class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Discount
                                        </th>
                                        <th
                                            class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Tax
                                        </th>
                                        <th
                                            class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Total
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="item in purchase.items" :key="item.id" class="hover:bg-gray-50">
                                        <td class="px-4 py-4">
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">
                                                    {{ item.product.name }}
                                                </p>
                                                <div class="flex gap-4 mt-1">
                                                    <p v-if="item.batch_number" class="text-xs text-gray-500">
                                                        Batch: {{ item.batch_number }}
                                                    </p>
                                                    <p v-if="item.expiry_date" class="text-xs text-gray-500">
                                                        Exp: {{ formatDate(item.expiry_date) }}
                                                    </p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 text-center text-sm text-gray-900">
                                            {{ item.quantity }}
                                        </td>
                                        <td class="px-4 py-4 text-right text-sm text-gray-900">
                                            {{ formatCurrency(item.cost_price) }}
                                        </td>
                                        <td class="px-4 py-4 text-right text-sm text-gray-900">
                                            {{ formatCurrency(item.unit_cost) }}
                                        </td>
                                        <td class="px-4 py-4 text-right text-sm text-red-600">
                                            {{ formatCurrency(item.discount) }}
                                        </td>
                                        <td class="px-4 py-4 text-right text-sm text-gray-900">
                                            {{ formatCurrency(item.tax_amount) }}
                                            <span class="text-xs text-gray-500">({{ item.tax_rate }}%)</span>
                                        </td>
                                        <td class="px-4 py-4 text-right text-sm font-medium text-gray-900">
                                            {{ formatCurrency(item.total) }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Divider -->
                    <div class="border-t border-gray-200"></div>

                    <!-- Billing Summary Section -->
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 mb-6">Billing Summary</h2>

                        <div class="flex justify-end">
                            <div class="w-full max-w-md space-y-3">
                                <!-- Subtotal -->
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Subtotal:</span>
                                    <span class="font-medium text-gray-900">{{ formatCurrency(purchase.subtotal)
                                        }}</span>
                                </div>

                                <!-- Discount -->
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Discount:</span>
                                    <span class="font-medium text-red-600">-{{ formatCurrency(purchase.discount)
                                        }}</span>
                                </div>

                                <!-- Tax -->
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Tax:</span>
                                    <span class="font-medium text-gray-900">{{ formatCurrency(purchase.tax) }}</span>
                                </div>

                                <!-- Shipping -->
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Shipping:</span>
                                    <span class="font-medium text-gray-900">{{ formatCurrency(purchase.shipping)
                                        }}</span>
                                </div>

                                <!-- Total -->
                                <div class="flex justify-between text-lg font-semibold pt-3 border-t border-gray-300">
                                    <span class="text-gray-900">Total:</span>
                                    <span class="text-gray-900">{{ formatCurrency(purchase.total) }}</span>
                                </div>

                                <!-- Amount Paid -->
                                <div class="flex justify-between text-sm pt-2">
                                    <span class="text-gray-600">Amount Paid:</span>
                                    <span class="font-medium text-green-600">{{ formatCurrency(purchase.amount_paid)
                                        }}</span>
                                </div>

                                <!-- Balance -->
                                <div class="flex justify-between text-lg font-semibold pt-2 border-t border-gray-300">
                                    <span :class="purchase.balance > 0 ? 'text-red-600' : 'text-green-600'">
                                        Balance Due:
                                    </span>
                                    <span :class="purchase.balance > 0 ? 'text-red-600' : 'text-green-600'">
                                        {{ formatCurrency(purchase.balance) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style>
@media print {

    /* Hide elements during print */
    .print\:hidden {
        display: none !important;
    }

    /* Adjust page margins */
    @page {
        margin: 1cm;
    }

    /* Prevent page breaks inside tables */
    table {
        page-break-inside: avoid;
    }

    tr {
        page-break-inside: avoid;
    }
}
</style>