<script setup lang="ts">
definePageMeta({
  middleware: [
    function () {
      return navigateTo('/invoices')
    }
  ]
})
</script>

<template>
  <div>Redirecting...</div>
</template>
