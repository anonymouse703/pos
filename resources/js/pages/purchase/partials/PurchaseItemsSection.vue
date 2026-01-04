<script setup lang="ts">
import { watch } from 'vue';
import Input from '@/components/ui/input/Input.vue';
import Label from '@/components/ui/label/Label.vue';
import Button from '@/components/ui/button/Button.vue';
import { Plus, Trash2 } from 'lucide-vue-next';

// Purchase Item Interface
export interface PurchaseItem {
    product_id: number;
    quantity: number;
    unit_cost: number;
    discount: number;
    tax_rate: number;
    tax_amount: number;
    total: number;
    batch_number: string;
    expiry_date: string;
}

// Props
const props = defineProps<{
    items: PurchaseItem[];
    products: Array<{ id: number; name: string }>;
}>();

// Emits
const emit = defineEmits<{
    'add-item': [];
    'remove-item': [index: number];
    'update-items': [items: PurchaseItem[]];
}>();

// Add new item
const addItem = () => {
    emit('add-item');
};

// Remove item
const removeItem = (index: number) => {
    emit('remove-item', index);
};

// Calculate item total
const calculateItemTotal = (item: PurchaseItem) => {
    const subtotal = item.quantity * item.unit_cost;
    const afterDiscount = subtotal - item.discount;
    item.tax_amount = (afterDiscount * item.tax_rate) / 100;
    item.total = afterDiscount + item.tax_amount;
};

// Watch for changes in items
watch(() => props.items, () => {
    props.items.forEach(item => calculateItemTotal(item));
    emit('update-items', props.items);
}, { deep: true });
</script>

<template>
    <div>
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900">Purchase Items</h2>
            <Button type="button" @click="addItem" variant="outline" class="flex items-center gap-2">
                <Plus class="h-4 w-4" />
                Add Item
            </Button>
        </div>

        <!-- Items List -->
        <div v-if="items.length > 0" class="space-y-4">
            <div 
                v-for="(item, index) in items" 
                :key="index"
                class="p-4 border border-gray-200 rounded-lg bg-gray-50"
            >
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Product -->
                    <div class="space-y-2 lg:col-span-2">
                        <Label :for="`product_${index}`">Product *</Label>
                        <select 
                            :id="`product_${index}`"
                            v-model="item.product_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            required
                        >
                            <option :value="0" disabled>Select Product</option>
                            <option v-for="product in products" :key="product.id" :value="product.id">
                                {{ product.name }}
                            </option>
                        </select>
                    </div>

                    <!-- Quantity -->
                    <div class="space-y-2">
                        <Label :for="`quantity_${index}`">Quantity *</Label>
                        <Input 
                            :id="`quantity_${index}`"
                            v-model.number="item.quantity"
                            type="number"
                            min="1"
                            step="1"
                            required
                        />
                    </div>

                    <!-- Unit Cost -->
                    <div class="space-y-2">
                        <Label :for="`unit_cost_${index}`">Unit Cost *</Label>
                        <Input 
                            :id="`unit_cost_${index}`"
                            v-model.number="item.unit_cost"
                            type="number"
                            min="0"
                            step="0.01"
                            required
                        />
                    </div>

                    <!-- Discount -->
                    <div class="space-y-2">
                        <Label :for="`discount_${index}`">Discount</Label>
                        <Input 
                            :id="`discount_${index}`"
                            v-model.number="item.discount"
                            type="number"
                            min="0"
                            step="0.01"
                        />
                    </div>

                    <!-- Tax Rate -->
                    <div class="space-y-2">
                        <Label :for="`tax_rate_${index}`">Tax Rate (%)</Label>
                        <Input 
                            :id="`tax_rate_${index}`"
                            v-model.number="item.tax_rate"
                            type="number"
                            min="0"
                            step="0.01"
                        />
                    </div>

                    <!-- Batch Number -->
                    <div class="space-y-2">
                        <Label :for="`batch_${index}`">Batch Number</Label>
                        <Input 
                            :id="`batch_${index}`"
                            v-model="item.batch_number"
                            type="text"
                        />
                    </div>

                    <!-- Expiry Date -->
                    <div class="space-y-2">
                        <Label :for="`expiry_${index}`">Expiry Date</Label>
                        <Input 
                            :id="`expiry_${index}`"
                            v-model="item.expiry_date"
                            type="date"
                        />
                    </div>
                </div>

                <!-- Item Total and Remove Button -->
                <div class="flex items-center justify-between mt-4 pt-4 border-t border-gray-300">
                    <div class="text-sm">
                        <span class="text-gray-600">Item Total: </span>
                        <span class="font-semibold text-gray-900">₱{{ item.total.toFixed(2) }}</span>
                    </div>
                    <Button 
                        type="button" 
                        @click="removeItem(index)" 
                        variant="outline"
                        class="text-red-600 hover:text-red-700 hover:bg-red-50 border-red-300"
                    >
                        <Trash2 class="h-4 w-4" />
                    </Button>
                </div>
            </div>
        </div>

        <!-- Empty State -->
        <div v-else class="text-center py-12 border-2 border-dashed border-gray-300 rounded-lg">
            <p class="text-gray-500 mb-4">No items added yet</p>
            <Button type="button" @click="addItem" variant="outline" class="flex items-center gap-2 mx-auto">
                <Plus class="h-4 w-4" />
                Add First Item
            </Button>
        </div>
    </div>
</template>
