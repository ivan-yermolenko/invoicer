<template>
  <div class="min-h-screen bg-slate-50 text-slate-900">
    <header class="bg-white border-b border-slate-200 sticky top-0 z-10">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
        <NuxtLink
          to="/invoices"
          class="flex items-center gap-2 hover:opacity-80 transition-opacity"
        >
          <div
            class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center text-white font-bold text-xl"
          >
            M
          </div>
          <span
            class="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-indigo-600"
          >
            Monefy
          </span>
        </NuxtLink>
      </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <NuxtPage />
    </main>
  </div>
</template>
