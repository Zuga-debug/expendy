<template>
    <form @submit.prevent="updateProfile" class="space-y-4 max-w-md mx-auto">
      <h2 class="text-lg font-bold text-yellow-500">Edit Profile</h2>
  
      <div>
        <label>Name:</label>
        <input v-model="user.name" class="input" type="text" />
      </div>
  
      <div>
        <label>Email:</label>
        <input v-model="user.email" class="input" type="email" />
      </div>
  
      <button class="bg-blue-600 text-white px-4 py-2 rounded">Update</button>
    </form>
  </template>
  
  <script>
  export default {
    data() {
      return {
        user: {
          name: '',
          email: '',
        }
      };
    },
    async mounted() {
      const res = await axios.get('/api/user', {
        headers: { Authorization: `Bearer ${localStorage.getItem("token")}` }
      });
      this.user = res.data;
    },
    methods: {
      async updateProfile() {
        try {
          await axios.put('/api/user/profile', this.user, {
            headers: {
              Authorization: `Bearer ${localStorage.getItem("token")}`
            }
          });
          alert('Profile updated!');
        } catch (err) {
          console.error(err);
          alert('Something went wrong');
        }
      }
    }
  };
  </script>
  