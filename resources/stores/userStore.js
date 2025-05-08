import { defineStore } from 'pinia';
import axios from 'axios';

export const useUserStore = defineStore('user', {
  state: () => ({
    user: null,
  }),
  actions: {
    async fetchUser() {
      try {
        const token = localStorage.getItem('token');
        if (!token) return;

        const response = await axios.get('/api/user', {
          headers: {
            Authorization: `Bearer ${token}`
          }
        });
        this.user = response.data;
      } catch (error) {
        console.error('User fetch error:', error);
        this.user = null;

        if (error.response?.status === 401) {
          localStorage.removeItem('token');
          window.location.href = '/login';
        }
      }
    },
    logout() {
      localStorage.removeItem('token');
      this.user = null;
      window.location.href = '/login';
    }
  }
});
