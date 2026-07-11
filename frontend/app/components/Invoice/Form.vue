<script setup lang="ts">
import { computed } from 'vue';
import { z } from 'zod';
import { useForm } from 'vee-validate';
import { toTypedSchema } from '@vee-validate/zod';
import type { Invoice } from '~/composables/useInvoicesApi';

const props = defineProps<{
  initialData?: Partial<Invoice> | null;
  isReadonly?: boolean;
  isSubmitting?: boolean;
}>();

const emit = defineEmits<{
  (e: 'submit', payload: any): void;
  (e: 'cancel'): void;
}>();

const invoiceSchema = z
  .object({
    number: z.string().min(1, 'Поле "Номер інвойсу" є обов\'язковим'),
    supplier_name: z.string().min(1, 'Поле "Назва компанії" є обов\'язковим'),
    supplier_tax_id: z.string().min(1, 'Поле "ЄДРПОУ / ІПН" є обов\'язковим'),
    net_amount: z
      .number({
        required_error: "Сума без ПДВ є обов'язковою",
        invalid_type_error: 'Введіть коректну суму'
      })
      .min(0.01, 'Сума має бути більшою за 0'),
    vat_amount: z
      .number({
        required_error: "Сума ПДВ є обов'язковою",
        invalid_type_error: 'Введіть коректну суму'
      })
      .min(0, "ПДВ не може бути від'ємним"),
    currency: z.enum(['UAH', 'USD', 'EUR']),
    issue_date: z.string().min(1, "Дата виставлення є обов'язковою"),
    due_date: z.string().min(1, "Дата оплати є обов'язковою")
  })
  .refine((data) => new Date(data.due_date) >= new Date(data.issue_date), {
    message: 'Дата оплати не може бути раніше дати виставлення',
    path: ['due_date']
  });

const { handleSubmit, errors, defineField, values } = useForm({
  validationSchema: toTypedSchema(invoiceSchema),
  initialValues: {
    number: props.initialData?.number ?? '',
    supplier_name: props.initialData?.supplier_name ?? '',
    supplier_tax_id: props.initialData?.supplier_tax_id ?? '',
    net_amount: props.initialData?.net_amount ?? undefined,
    vat_amount: props.initialData?.vat_amount ?? undefined,
    currency: props.initialData?.currency ?? 'UAH',
    issue_date: props.initialData?.issue_date ?? '',
    due_date: props.initialData?.due_date ?? ''
  }
});

const [number] = defineField('number');
const [supplierName] = defineField('supplier_name');
const [supplierTaxId] = defineField('supplier_tax_id');
const [netAmount] = defineField('net_amount');
const [vatAmount] = defineField('vat_amount');
const [currency] = defineField('currency');
const [issueDate] = defineField('issue_date');
const [dueDate] = defineField('due_date');

const grossAmount = computed(() => {
  const net = Number(values.net_amount) ?? 0;
  const vat = Number(values.vat_amount) ?? 0;
  return net + vat;
});

const INVOICE_CURRENCIES = ['UAH', 'USD', 'EUR'];

const onSubmit = handleSubmit((formValues) => {
  emit('submit', formValues);
});
</script>

<template>
  <form @submit="onSubmit" class="space-y-6">
    <!-- General Info Section -->
    <div>
      <h3 class="text-lg font-medium text-slate-900 mb-4 pb-2 border-b border-slate-100">
        Загальна інформація
      </h3>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Номер інвойсу</label>
          <input
            v-model="number"
            type="text"
            placeholder="Наприклад: INV-2026-001"
            :disabled="isReadonly"
            class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all disabled:opacity-50 disabled:cursor-not-allowed"
            :class="{ 'border-red-300 ring-1 ring-red-300': errors.number }"
          />
          <p v-if="errors.number" class="mt-1 text-sm text-red-500">{{ errors.number }}</p>
        </div>
      </div>
    </div>

    <!-- Supplier Section -->
    <div>
      <h3 class="text-lg font-medium text-slate-900 mb-4 pb-2 border-b border-slate-100">
        Дані постачальника
      </h3>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Назва компанії</label>
          <input
            v-model="supplierName"
            type="text"
            :disabled="isReadonly"
            class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all disabled:opacity-50 disabled:cursor-not-allowed"
            :class="{ 'border-red-300 ring-1 ring-red-300': errors.supplier_name }"
          />
          <p v-if="errors.supplier_name" class="mt-1 text-sm text-red-500">
            {{ errors.supplier_name }}
          </p>
        </div>

        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">ЄДРПОУ / ІПН (Tax ID)</label>
          <input
            v-model="supplierTaxId"
            type="text"
            :disabled="isReadonly"
            class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all disabled:opacity-50 disabled:cursor-not-allowed"
            :class="{ 'border-red-300 ring-1 ring-red-300': errors.supplier_tax_id }"
          />
          <p v-if="errors.supplier_tax_id" class="mt-1 text-sm text-red-500">
            {{ errors.supplier_tax_id }}
          </p>
        </div>
      </div>
    </div>

    <!-- Financials Section -->
    <div>
      <h3 class="text-lg font-medium text-slate-900 mb-4 pb-2 border-b border-slate-100">
        Фінанси
      </h3>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Сума без ПДВ (Net)</label>
          <input
            v-model.number="netAmount"
            type="number"
            step="0.01"
            min="0"
            :disabled="isReadonly"
            class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all disabled:opacity-50 disabled:cursor-not-allowed"
            :class="{ 'border-red-300 ring-1 ring-red-300': errors.net_amount }"
          />
          <p v-if="errors.net_amount" class="mt-1 text-sm text-red-500">{{ errors.net_amount }}</p>
        </div>

        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Сума ПДВ (VAT)</label>
          <input
            v-model.number="vatAmount"
            type="number"
            step="0.01"
            min="0"
            :disabled="isReadonly"
            class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all disabled:opacity-50 disabled:cursor-not-allowed"
            :class="{ 'border-red-300 ring-1 ring-red-300': errors.vat_amount }"
          />
          <p v-if="errors.vat_amount" class="mt-1 text-sm text-red-500">{{ errors.vat_amount }}</p>
        </div>

        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Валюта</label>
          <select
            v-model="currency"
            :disabled="isReadonly"
            class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all disabled:opacity-50 disabled:cursor-not-allowed"
            :class="{ 'border-red-300 ring-1 ring-red-300': errors.currency }"
          >
            <option v-for="c in INVOICE_CURRENCIES" :key="c" :value="c">
              {{ c }}
            </option>
          </select>
          <p v-if="errors.currency" class="mt-1 text-sm text-red-500">{{ errors.currency }}</p>
        </div>
      </div>

      <!-- Auto-calculated Gross Amount -->
      <div
        class="mt-4 p-4 bg-blue-50 rounded-lg border border-blue-100 flex justify-between items-center"
      >
        <span class="text-blue-900 font-medium">До сплати (автоматично):</span>
        <span class="text-xl font-bold text-blue-700">{{
          formatCurrency(grossAmount, currency ?? 'UAH')
        }}</span>
      </div>
    </div>

    <!-- Dates Section -->
    <div>
      <h3 class="text-lg font-medium text-slate-900 mb-4 pb-2 border-b border-slate-100">Дати</h3>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Дата виставлення</label>
          <input
            v-model="issueDate"
            type="date"
            :disabled="isReadonly"
            class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all disabled:opacity-50 disabled:cursor-not-allowed"
            :class="{ 'border-red-300 ring-1 ring-red-300': errors.issue_date }"
          />
          <p v-if="errors.issue_date" class="mt-1 text-sm text-red-500">{{ errors.issue_date }}</p>
        </div>

        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Оплатити до</label>
          <input
            v-model="dueDate"
            type="date"
            :disabled="isReadonly"
            class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all disabled:opacity-50 disabled:cursor-not-allowed"
            :class="{ 'border-red-300 ring-1 ring-red-300': errors.due_date }"
          />
          <p v-if="errors.due_date" class="mt-1 text-sm text-red-500">{{ errors.due_date }}</p>
        </div>
      </div>
    </div>

    <!-- Submit actions -->
    <div v-if="!isReadonly" class="pt-6 border-t border-slate-100 flex justify-end gap-3">
      <button
        type="button"
        @click="emit('cancel')"
        class="px-6 py-2.5 rounded-lg font-medium text-slate-700 hover:bg-slate-100 transition-colors"
      >
        Скасувати
      </button>
      <button
        type="submit"
        :disabled="isSubmitting"
        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-lg font-medium shadow-sm transition-colors duration-200 flex items-center gap-2 disabled:opacity-50"
      >
        Зберегти інвойс
      </button>
    </div>
  </form>
</template>
