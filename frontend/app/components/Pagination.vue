<script setup lang="ts">
import type { PaginationMeta } from '~/composables/useInvoicesApi';

const props = defineProps<{
  meta?: PaginationMeta;
}>();

const page = defineModel<number>({ required: true });
</script>

<template>
  <div
    v-if="meta && meta.last_page > 1"
    class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex items-center justify-between"
  >
    <button
      @click="page = meta.current_page - 1"
      :disabled="meta.current_page === 1"
      class="px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
    >
      Попередня
    </button>

    <span class="text-sm text-slate-600">
      Сторінка <span class="font-medium text-slate-900">{{ meta.current_page }}</span> з
      <span class="font-medium text-slate-900">{{ meta.last_page }}</span>
    </span>

    <button
      @click="page = meta.current_page + 1"
      :disabled="meta.current_page === meta.last_page"
      class="px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
    >
      Наступна
    </button>
  </div>
</template>
