<script setup lang="ts">
import { ref } from 'vue';

const { createInvoice } = useInvoicesApi();
const router = useRouter();

const isSubmitting = ref(false);

const handleCreate = async (payload: any) => {
  isSubmitting.value = true;

  try {
    const response = await createInvoice(payload);
    await router.push(`/invoices/${response.data.id}`);
  } catch (error: any) {
    if (error.response?.status === 422) {
      console.error(
        'Помилка валідації на сервері: ' +
          Object.values(error.response._data.errors).flat().join(', ')
      );
    } else {
      console.error('Виникла невідома помилка при збереженні інвойсу.', error);
    }
  } finally {
    isSubmitting.value = false;
  }
};
</script>

<template>
  <div class="max-w-2xl mx-auto">
    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
      <div>
        <h1 class="text-3xl font-bold text-slate-900 mb-2">Новий інвойс</h1>
        <p class="text-slate-500">Заповніть форму для створення нового рахунку</p>
      </div>
      <button
        @click="navigateTo('/invoices')"
        class="text-slate-500 hover:text-slate-900 flex items-center gap-2 transition-colors"
      >
        <IconArrowLeft />
        Скасувати
      </button>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden relative p-8">
      <InvoiceForm
        :is-submitting="isSubmitting"
        @submit="handleCreate"
        @cancel="navigateTo('/invoices')"
      />

      <!-- Loading Overlay -->
      <LoadingOverlay v-if="isSubmitting" />
    </div>
  </div>
</template>
