<template>
  <div class="flex justify-center items-center min-h-screen bg-gray-100">
    <div class="bg-white p-8 rounded shadow-md w-96">
      <h2 class="text-2xl font-bold mb-6 text-center">Register</h2>
      <form @submit.prevent="register">
        <div class="mb-4">
          <label class="block text-gray-700">Name</label>
          <input type="text" v-model="form.name" class="w-full p-2 border rounded" required  autocomplete=""/>
        </div>
        <div class="mb-4">
          <label class="block text-gray-700">Email</label>
          <input type="email" v-model="form.email" class="w-full p-2 border rounded" required autocomplete="email" />
        </div>
        <div class="mb-4">
          <label class="block text-gray-700">Password</label>
          <input type="password" v-model="form.password" class="w-full p-2 border rounded" required autocomplete="current-password" />
        </div>
        <div class="mb-4">
          <label class="block text-gray-700">Confirm Password</label>
          <input type="password" v-model="form.password_confirmation" class="w-full p-2 border rounded" required autocomplete="new-password" />
        </div>
        <div class="mb-4 flex items-center justify-between">
          <label class="flex items-center">
            <input type="checkbox" class="mr-2" /> I agree to the terms and conditions
          </label>
          </div>
        <button type="submit" :disabled="loading" class="w-full bg-blue-500 text-white p-2 rounded hover:bg-blue-600 flex items-center justify-center">
  <svg v-if="loading" class="animate-spin h-5 w-5 text-white mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
  </svg>
  {{ loading ? 'Please wait...' : 'Login' }}
</button>
        <p v-if="errorMessage" class="text-red-500 mt-2 text-center">{{ errorMessage }}</p>
      </form>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      form: {
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
      },
      errorMessage: '',
      loading:false,
    };
  },
  methods: {
    async register() {
      this.loading = true;
      try {
        const response = await axios.post('/api/register', this.form);
        localStorage.setItem('auth_token', response.data.token); // Store token
        this.$router.push('/dashboard'); // Redirect to Dashboard
      } catch (error) {
        this.errorMessage = error.response?.data?.message || "Registration failed";
      }finally {
        this.loading = false;
      }
    }
  }
};
</script>
