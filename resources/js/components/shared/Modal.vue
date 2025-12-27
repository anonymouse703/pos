<script setup lang="ts">
import { AlertTriangle, X } from 'lucide-vue-next';

// Props with defaults
const props = withDefaults(
  defineProps<{
    show: boolean;
    title?: string;
    message?: string;
    confirmText?: string;
    cancelText?: string;
    variant?: 'danger' | 'warning' | 'info';
    icon?: boolean;
  }>(),
  {
    title: 'Confirm Action',
    message: 'Are you sure you want to proceed?',
    confirmText: 'Confirm',
    cancelText: 'Cancel',
    variant: 'danger',
    icon: true,
  }
);

const emit = defineEmits<{
  confirm: [];
  cancel: [];
  close: [];
}>();

const handleConfirm = () => {
  emit('confirm');
  emit('close');
};

const handleCancel = () => {
  emit('cancel');
  emit('close');
};

const handleBackdropClick = (e: MouseEvent) => {
  if (e.target === e.currentTarget) {
    handleCancel();
  }
};

// Variant styles for icon & confirm button
const getVariantStyles = () => {
  switch (props.variant) {
    case 'danger':
      return {
        icon: 'text-red-600 bg-red-100 dark:bg-red-900/20',
        button: 'bg-red-600 hover:bg-red-700 focus:ring-red-500',
      };
    case 'warning':
      return {
        icon: 'text-yellow-600 bg-yellow-100 dark:bg-yellow-900/20',
        button: 'bg-yellow-600 hover:bg-yellow-700 focus:ring-yellow-500',
      };
    case 'info':
      return {
        icon: 'text-blue-600 bg-blue-100 dark:bg-blue-900/20',
        button: 'bg-blue-600 hover:bg-blue-700 focus:ring-blue-500',
      };
    default:
      return {
        icon: 'text-red-600 bg-red-100 dark:bg-red-900/20',
        button: 'bg-red-600 hover:bg-red-700 focus:ring-red-500',
      };
  }
};

const variantStyles = getVariantStyles();
</script>

<template>
  <Teleport to="body">
    <Transition
      enter-active-class="transition-opacity duration-200"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition-opacity duration-200"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="show"
        @click="handleBackdropClick"
        class="fixed inset-0 z-50 flex items-center justify-center bg-gray-500/20 dark:bg-gray-200/10 backdrop-blur-sm p-4"
      >
        <Transition
          enter-active-class="transition-all duration-200"
          enter-from-class="opacity-0 scale-95"
          enter-to-class="opacity-100 scale-100"
          leave-active-class="transition-all duration-200"
          leave-from-class="opacity-100 scale-100"
          leave-to-class="opacity-0 scale-95"
        >
          <div
            v-if="show"
            class="bg-gray-100 dark:bg-gray-200 rounded-2xl shadow-2xl w-full max-w-md overflow-hidden"
            @click.stop
          >
            <!-- Close button -->
            <div class="relative px-6 pt-6">
              <button
                @click="handleCancel"
                class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors p-1 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700"
                aria-label="Close"
              >
                <X class="h-5 w-5" />
              </button>
            </div>

            <!-- Content -->
            <div class="px-6 pb-6 space-y-4">
              <div v-if="icon" class="flex justify-center">
                <div :class="['rounded-full p-3', variantStyles.icon]">
                  <AlertTriangle class="h-8 w-8" />
                </div>
              </div>

              <div class="text-center">
                <h3 class="text-xl font-semibold text-gray-600 dark:text-gray-500 mb-2">
                  {{ title }}
                </h3>
              </div>

              <div class="text-center">
                <p class="text-sm text-gray-600 dark:text-gray-500 leading-relaxed">
                  {{ message }}
                </p>
              </div>
            </div>

            <!-- Actions: Cancel left, Confirm right -->
            <div class="bg-gray-300 dark:bg-gray-300 px-6 py-4 flex gap-3 justify-end">
              <button
                @click="handleCancel"
                class="px-5 py-2.5 text-sm font-medium border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-500 transition-all focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 order-1"
              >
                {{ cancelText }}
              </button>
              <button
                @click="handleConfirm"
                :class="[
                  'px-5 py-2.5 text-sm font-medium text-white rounded-lg transition-all focus:outline-none focus:ring-2 focus:ring-offset-2 order-2',
                  variantStyles.button
                ]"
              >
                {{ confirmText }}
              </button>
            </div>
          </div>
        </Transition>
      </div>
    </Transition>
  </Teleport>
</template>
