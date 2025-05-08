<!-- resources/js/Pages/Settings.vue -->
<template>
    <div>
      <h1 class="text-2xl font-bold mb-6 text-yellow-500">Settings</h1>
      <EditProfile />
    </div>
  </template>
  
  <script>
  import EditProfile from './Profile.vue'; // or wherever it's defined
  
  export default {
    components: {
      EditProfile
    }
  }
  </script>
  