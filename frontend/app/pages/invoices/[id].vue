<script setup lang="ts">
import { computed } from 'vue';

const route = useRoute();
const { getInvoice, updateInvoice } = useInvoicesApi();

const { data: response, pending, error, refresh } = await getInvoice(route.params.id as string);

const invoice = computed(() => response.value?.data);
const isUpdating = ref(false);

const handleStatusChange = async (newStatus: 'approved' | 'rejected') => {
  if (!invoice.value) return;

  isUpdating.value = true;
  try {
    await updateInvoice(invoice.value.id, {
      status: newStatus
    });
    await refresh();
  } catch (e) {
    console.error(e);
  } finally {
    isUpdating.value = false;
  }
};

const handleUpdate = async (payload: any) => {
  if (!invoice.value) return;

  isUpdating.value = true;
  try {
    await updateInvoice(invoice.value.id, payload);
    await refresh();
  } catch (e: any) {
    if (e.response?.status === 422) {
      console.error(
        'Помилка валідації на сервері: ' + Object.values(e.response._data.errors).flat().join(', ')
      );
    } else {
      console.error('Виникла помилка при оновленні інвойсу.', e);
    }
  } finally {
    isUpdating.value = false;
  }
};
</script>

<template>
  <div class="space-y-6 max-w-4xl mx-auto">
    <!-- Header Actions -->
    <div class="flex items-center justify-between">
      <button
        @click="navigateTo('/invoices')"
        class="text-slate-500 hover:text-slate-900 flex items-center gap-2 transition-colors"
      >
        <IconArrowLeft />
        Назад до списку
      </button>

      <div v-if="invoice && invoice.status === 'pending'" class="flex items-center gap-3">
        <button
          @click="handleStatusChange('rejected')"
          :disabled="isUpdating"
          class="bg-white border border-red-200 text-red-600 hover:bg-red-50 px-4 py-2 rounded-lg font-medium shadow-sm transition-colors duration-200 disabled:opacity-50"
        >
          Відхилити
        </button>
        <button
          @click="handleStatusChange('approved')"
          :disabled="isUpdating"
          class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg font-medium shadow-sm transition-colors duration-200 flex items-center gap-2 disabled:opacity-50"
        >
          Затвердити
        </button>
      </div>
    </div>

    <!-- Error State -->
    <div v-if="error" class="bg-red-50 border border-red-200 rounded-xl p-6 text-center">
      <p class="text-red-600 font-medium">Інвойс не знайдено або сталася помилка</p>
      <p class="text-red-500 text-sm mt-1">{{ error.message }}</p>
    </div>

    <!-- Loading Skeleton -->
    <div
      v-else-if="pending"
      class="bg-white border border-slate-200 rounded-xl p-8 shadow-sm animate-pulse"
    >
      <div class="flex justify-between items-start mb-8">
        <div>
          <div class="h-8 bg-slate-200 rounded w-48 mb-2"></div>
          <div class="h-4 bg-slate-200 rounded w-32"></div>
        </div>
        <div class="h-8 bg-slate-200 rounded-full w-24"></div>
      </div>
      <div class="grid grid-cols-2 gap-8 mb-8">
        <div class="space-y-4">
          <div class="h-4 bg-slate-200 rounded w-full"></div>
          <div class="h-4 bg-slate-200 rounded w-3/4"></div>
        </div>
      </div>
    </div>

    <!-- Details Content -->
    <div
      v-else-if="invoice"
      class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden relative"
      :class="{ 'opacity-75 pointer-events-none': isUpdating }"
    >
      <!-- Top Section -->
      <div class="p-8 border-b border-slate-100 flex justify-between items-start">
        <div>
          <h1 class="text-3xl font-bold text-slate-900 mb-1">{{ invoice.number }}</h1>
          <p class="text-slate-500">Редагування інвойсу</p>
        </div>
        <span
          class="px-3 py-1.5 text-sm font-medium rounded-full border"
          :class="INVOICE_STATUS_CLASSES[invoice.status]"
        >
          {{ INVOICE_STATUS_LABELS[invoice.status] }}
        </span>
      </div>

      <!-- Form Section -->
      <div class="p-8">
        <InvoiceForm
          :key="invoice.updated_at"
          :initial-data="invoice"
          :is-readonly="invoice.status !== 'pending'"
          :is-submitting="isUpdating"
          @submit="handleUpdate"
          @cancel="navigateTo('/invoices')"
        />
      </div>

      <LoadingOverlay v-if="isUpdating" />
    </div>
  </div>
</template>
