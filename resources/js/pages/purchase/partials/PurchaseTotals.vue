<script setup lang="ts">
import Input from '@/components/ui/input/Input.vue';
import Label from '@/components/ui/label/Label.vue';

// Props
defineProps<{
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
                <span class="text-gray-600">Subtotal:</span>
                <span class="font-medium">₱{{ subtotal.toFixed(2) }}</span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-gray-600">Discount:</span>
                <span class="font-medium text-red-600">-₱{{ discount.toFixed(2) }}</span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-gray-600">Tax:</span>
                <span class="font-medium">₱{{ tax.toFixed(2) }}</span>
            </div>
            <div class="flex justify-between items-center text-sm">
                <Label for="shipping">Shipping:</Label>
                <Input 
                    id="shipping"
                    :value="shipping"
                    @input="updateShipping"
                    type="number"
                    min="0"
                    step="0.01"
                    class="w-32"
                />
            </div>
            <div class="flex justify-between text-lg font-semibold pt-3 border-t border-gray-300">
                <span>Total:</span>
                <span>₱{{ total.toFixed(2) }}</span>
            </div>
            <div class="flex justify-between items-center text-sm pt-2">
                <Label for="amount_paid">Amount Paid:</Label>
                <Input 
                    id="amount_paid"
                    :value="amountPaid"
                    @input="updateAmountPaid"
                    type="number"
                    min="0"
                    step="0.01"
                    class="w-32"
                />
            </div>
            <div class="flex justify-between text-sm font-medium" :class="balance > 0 ? 'text-red-600' : 'text-green-600'">
                <span>Balance:</span>
                <span>₱{{ balance.toFixed(2) }}</span>
            </div>
        </div>
    </div>
</template>