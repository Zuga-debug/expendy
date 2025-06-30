<template>
  <div class="flex items-center justify-center min-h-screen bg-gray-400">
    <div class="w-full max-w-md bg-white p-6 rounded-lg shadow-lg">
      <h2 class="text-2xl font-semibold text-center mb-4">Login to Expendy</h2>

      <form @submit.prevent="login">
        <div class="mb-4">
          <label class="block text-gray-700">Email</label>
          <input type="email" v-model="email" required class="w-full p-2 border border-gray-300 rounded" autocomplete="email">
        </div>

        <div class="mb-4">
          <label class="block text-gray-700">Password</label>
          <input type="password" v-model="password" required class="w-full p-2 border border-gray-300 rounded" autocomplete="current-password">
        </div>

        <button type="submit" :disabled="loading" class="w-full bg-blue-500 text-white p-2 rounded hover:bg-blue-600 flex items-center justify-center">
  <svg v-if="loading" class="animate-spin h-5 w-5 text-white mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
  </svg>
  {{ loading ? 'Please wait...' : 'Login' }}
</button>

      </form>

      <p class="text-center mt-4">
        Don't have an account? 
        <router-link to="/register" class="text-blue-500 hover:underline">Register</router-link>
      </p>

      <p v-if="errorMessage" class="text-red-500 text-center mt-2">{{ errorMessage }}</p>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      email: '',
      password: '',
      errorMessage: '',
      loading: false,
    };
  },
  methods: {
    async login() {
      this.loading = true;
      try {
        const response = await axios.post('/api/login', {
          email: this.email,
          password: this.password
        });

        // Store token in localStorage
        localStorage.setItem('token', response.data.token);

        // Redirect to dashboard
        this.$router.push('/dashboard');
      } catch (error) {
        this.errorMessage = 'Invalid email or password';
      }
      finally {
        this.loading = false;
      }
    }
  }
};
</script>
