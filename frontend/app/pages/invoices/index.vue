<script setup lang="ts">
import { computed } from 'vue';

const route = useRoute();
const router = useRouter();

const page = computed({
  get: () => Number(route.query.page) || 1,
  set: (val) => router.push({ query: { ...route.query, page: val } })
});

const query = computed(() => ({ page: page.value }));

const { getInvoices } = useInvoicesApi();
const { data: response, pending, error } = await getInvoices(query);

const invoices = computed(() => response.value?.data || []);
const meta = computed(() => response.value?.meta);

const columns = [
  { key: 'number', label: 'Номер', alignClass: 'text-left', skeletonClass: 'h-4 w-24' },
  {
    key: 'supplier_name',
    label: 'Постачальник',
    alignClass: 'text-left',
    skeletonClass: 'h-4 w-32'
  },
  {
    key: 'gross_amount',
    label: 'Сума',
    alignClass: 'text-right',
    skeletonClass: 'h-4 w-20 ml-auto'
  },
  {
    key: 'status',
    label: 'Статус',
    alignClass: 'text-center',
    skeletonClass: 'h-6 w-20 rounded-full mx-auto'
  },
  {
    key: 'due_date',
    label: 'Оплатити до',
    alignClass: 'text-right',
    skeletonClass: 'h-4 w-24 ml-auto'
  }
];
</script>

<template>
  <div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold text-slate-900">Інвойси</h1>
        <p class="text-sm text-slate-500 mt-1">Керуйте та відстежуйте інвойси вашої компанії</p>
      </div>
      <button
        @click="navigateTo('/invoices/create')"
        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium shadow-sm transition-colors duration-200 flex items-center gap-2"
      >
        <IconPlus />
        Новий інвойс
      </button>
    </div>

    <!-- Error State -->
    <div v-if="error" class="bg-red-50 border border-red-200 rounded-xl p-6 text-center">
      <p class="text-red-600 font-medium">Не вдалося завантажити інвойси</p>
      <p class="text-red-500 text-sm mt-1">{{ error.message }}</p>
    </div>

    <!-- Table Container -->
    <div v-else class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200">
          <thead class="bg-slate-50">
            <tr>
              <th
                v-for="col in columns"
                :key="col.key"
                scope="col"
                class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider"
                :class="col.alignClass"
              >
                {{ col.label }}
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-slate-200">
            <!-- Loading Skeletons -->
            <template v-if="pending">
              <tr v-for="i in 5" :key="i" class="animate-pulse">
                <td v-for="col in columns" :key="col.key" class="px-6 py-4">
                  <div class="bg-slate-200 rounded" :class="col.skeletonClass"></div>
                </td>
              </tr>
            </template>

            <!-- Empty State -->
            <tr v-else-if="invoices.length === 0">
              <td :colspan="columns.length" class="px-6 py-12 text-center text-slate-500">
                <p class="text-lg font-medium text-slate-900 mb-1">Інвойсів не знайдено</p>
                <p>Почніть зі створення вашого першого інвойсу.</p>
              </td>
            </tr>

            <!-- Data Rows -->
            <template v-else>
              <tr
                v-for="invoice in invoices"
                :key="invoice.id"
                class="hover:bg-slate-50 transition-colors duration-150 cursor-pointer group"
                @click="navigateTo(`/invoices/${invoice.id}`)"
              >
                <td
                  v-for="col in columns"
                  :key="col.key"
                  class="px-6 py-4 whitespace-nowrap text-sm"
                  :class="col.alignClass"
                >
                  <!-- Custom rendering per column -->
                  <template v-if="col.key === 'number'">
                    <span class="font-medium text-slate-900">{{ invoice.number }}</span>
                  </template>

                  <template v-else-if="col.key === 'supplier_name'">
                    <span class="text-slate-600">{{ invoice.supplier_name }}</span>
                  </template>

                  <template v-else-if="col.key === 'gross_amount'">
                    <span class="font-medium text-slate-900">{{
                      formatCurrency(invoice.gross_amount, invoice.currency)
                    }}</span>
                  </template>

                  <template v-else-if="col.key === 'status'">
                    <span
                      class="px-2.5 py-1 text-xs font-medium rounded-full border"
                      :class="INVOICE_STATUS_CLASSES[invoice.status]"
                    >
                      {{ INVOICE_STATUS_LABELS[invoice.status] }}
                    </span>
                  </template>

                  <template v-else-if="col.key === 'due_date'">
                    <span class="text-slate-500 group-hover:text-blue-600 transition-colors">{{
                      formatDate(invoice.due_date)
                    }}</span>
                  </template>
                </td>
              </tr>
            </template>
          </tbody>
        </table>

        <Pagination v-model="page" :meta="meta" />
      </div>
    </div>
  </div>
</template>
