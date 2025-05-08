

<template>
    <div class="space-y-4">
      <input v-model="user.name" class="input" placeholder="Full Name" />
      <input v-model="user.email" class="input" placeholder="Email" />
      <button @click="updateProfile" class="btn btn-primary">Update</button>
    </div>
  </template>
  
  <script setup>
  import { reactive } from 'vue';
  import axios from 'axios';
  
  const props = defineProps({ initialUser: Object });
  const user = reactive({ ...props.initialUser });
  
  const updateProfile = async () => {
    await axios.put('/api/user/profile', user, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem("token")}`
      },
    });
    alert('Profile updated!');
  };
  </script>
  