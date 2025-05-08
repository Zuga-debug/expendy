



<template>
  <div>
    <h2 class="text-xl font-bold mb-4">Edit Profile</h2>
    <AvatarUpload :user="user" @avatarUpdated="updateAvatar" />
    <ProfileForm :initialUser="user" />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import AvatarUpload from '@/Components/AvatarUpload.vue';
import ProfileForm from '@/Components/ProfileForm.vue';
import axios from 'axios';

const user = ref(null);

onMounted(async () => {
  const res = await axios.get('/api/user', {
    headers: { Authorization: `Bearer ${localStorage.getItem("token")}` }
  });
  user.value = res.data;
});

const updateAvatar = (avatarPath) => {
  user.value = {
    ...user.value,
    avatar: avatarPath
  };
};
</script>
