
  
  <script setup>
  import { onMounted, ref } from 'vue';
  import { initDarkMode, toggleDarkMode as toggle } from '@/utils/darkModeToggle';
  import { MoonIcon, SunIcon } from 'lucide-vue-next';
  
  const isDark = ref(false);
  
  const toggleDarkMode = () => {
    toggle();
    isDark.value = document.documentElement.classList.contains('dark');
  };
  
  onMounted(() => {
    initDarkMode();
    isDark.value = document.documentElement.classList.contains('dark');
  });
  
  </script>
  


<template>
    <button @click="toggleDarkMode" class="ml-auto p-2">
      <MoonIcon v-if="isDark" />
      <SunIcon v-else />
    </button>
  </template>
