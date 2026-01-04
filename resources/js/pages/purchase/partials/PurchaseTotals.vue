<script setup lang="ts">
import { computed } from 'vue';
import Input from '@/components/ui/input/Input.vue';
import Label from '@/components/ui/label/Label.vue';

// Props
const props = defineProps<{
    subtotal: number;
    discount: number;
    tax: number;
    shipping: number;
    total: number;
    amountPaid: number;
    balance: number;
}>();

// Emits
const emit = defineEmits<{
    'update:shipping': [value: number];
    'update:amountPaid': [value: number];
}>();

// Ensure numbers are properly converted
const safeNumber = (value: any): number => {
    const num = typeof value === 'string' ? parseFloat(value) : value;
    return isNaN(num) ? 0 : num;
};

const subtotalNum = computed(() => safeNumber(props.subtotal));
const discountNum = computed(() => safeNumber(props.discount));
const taxNum = computed(() => safeNumber(props.tax));
const shippingNum = computed(() => safeNumber(props.shipping));
const totalNum = computed(() => safeNumber(props.total));
const amountPaidNum = computed(() => safeNumber(props.amountPaid));
const balanceNum = computed(() => safeNumber(props.balance));

const updateShipping = (event: Event) => {
    const value = parseFloat((event.target as HTMLInputElement).value) || 0;
    emit('update:shipping', value);
};

const updateAmountPaid = (event: Event) => {
    const value = parseFloat((event.target as HTMLInputElement).value) || 0;
    emit('update:amountPaid', value);
};
</script>

<template>
    <div class="flex justify-end">
        <div class="w-full max-w-md space-y-3">
            <div class="flex justify-between text-sm">
                <span class="text-sm font-medium text-gray-900 dark:text-gray-500">Subtotal:</span>
                <span class="font-medium text-gray-900 dark:text-gray-500">₱{{ subtotalNum.toFixed(2) }}</span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-sm font-medium text-gray-900 dark:text-gray-500">Discount:</span>
                <span class="font-medium text-red-600">-₱{{ discountNum.toFixed(2) }}</span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-sm font-medium text-gray-900 dark:text-gray-500">Tax:</span>
                <span class="font-medium text-gray-900 dark:text-gray-500">₱{{ taxNum.toFixed(2) }}</span>
            </div>
            <div class="flex justify-between items-center text-sm">
                <Label for="shipping" class="text-sm font-medium text-gray-900 dark:text-gray-500">Shipping:</Label>
                <Input 
                    id="shipping"
                    :value="shippingNum"
                    @input="updateShipping"
                    type="number"
                    min="0"
                    step="0.01"
                    class="w-32"
                />
            </div>
            <div class="flex justify-between text-lg font-semibold pt-3 border-t border-gray-300 dark:border-gray-600">
                <span class="text-gray-900 dark:text-gray-500">Total:</span>
                <span class="text-gray-900 dark:text-gray-500">₱{{ totalNum.toFixed(2) }}</span>
            </div>
            <div class="flex justify-between items-center text-sm pt-2">
                <Label for="amount_paid" class="text-sm font-medium text-gray-900 dark:text-gray-500">Amount Paid:</Label>
                <Input 
                    id="amount_paid"
                    :value="amountPaidNum"
                    @input="updateAmountPaid"
                    type="number"
                    min="0"
                    step="0.01"
                    class="w-32"
                />
            </div>
            <div class="flex justify-between text-sm font-medium" :class="balanceNum > 0 ? 'text-red-600' : 'text-green-600'">
                <span>Balance:</span>
                <span>₱{{ balanceNum.toFixed(2) }}</span>
            </div>
        </div>
    </div>
</template>