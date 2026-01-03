<template>
  <div class="p-4 bg-white dark:bg-gray-800 rounded shadow text-center">
    <h3 class="text-gray-500 text-sm">{{ title }}</h3>
    <p
      class="text-2xl font-bold"
      :class="green ? 'text-green-600' : ''"
    >
      {{ value ?? 0 }}
    </p>
  </div>
</template>

<script setup>
    
defineProps({
  title: String,
  value: [Number, String],
  green: Boolean
})
</script>
