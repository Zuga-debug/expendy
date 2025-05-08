
<script setup>
import { ref, computed, watch, onMounted } from 'vue';

import axios from 'axios';


const props = defineProps({ user: Object });
const emit = defineEmits(['avatarUpdated']);

const avatarUrl = computed(() => {
  const base = props.user?.avatar ? `/storage/${props.user.avatar}` : '/default-avatar.png';
  return `${base}?t=${Date.now()}`; // adds a timestamp to bust cache
});


const uploadAvatar = async (e) => {
  const formData = new FormData();
  formData.append("avatar", e.target.files[0]);

  const res = await axios.post("/api/profile/avatar", formData, {
    headers: {
      Authorization: `Bearer ${localStorage.getItem("token")}`,
      "Content-Type": "multipart/form-data"
    }
  });

  emit('avatarUpdated', res.data.avatar);
};
</script>


<template>
    <div>
      <img :src="avatarUrl" class="w-24 h-24 rounded-full" />
      <input type="file" @change="uploadAvatar" />
    </div>
  </template>
  