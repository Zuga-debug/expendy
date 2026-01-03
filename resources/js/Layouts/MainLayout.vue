<template>
  <div class="flex bg-gray-100 dark:bg-gray-900 min-h-screen h-screen overflow-hidden">
    
    <!-- Mobile Backdrop -->
    <div
      v-if="showSidebar"
      class="fixed inset-0 bg-black bg-opacity-50 z-40 md:hidden"
      @click="showSidebar = false"
    ></div>

    <!-- Sidebar -->
    <aside
      :class="[
        'fixed md:relative top-0 left-0 z-50 md:z-auto h-full w-64 p-4 bg-white dark:bg-gray-800 shadow-lg overflow-y-auto',
        showSidebar ? 'translate-x-0' : '-translate-x-full md:translate-x-0'
      ]"
    >
      <!-- User Info -->
      <div v-if="user" class="flex items-center space-x-3 mb-8">
        <img :src="user?.avatar ? '/storage/' + user.avatar : '/default-avatar.png'" class="rounded-full w-10 h-10" />
        <div class="text-sm text-gray-800 dark:text-white">
          <p class="font-semibold">{{ user.name }}</p>
          <p class="text-gray-500 dark:text-gray-300 text-xs">{{ user.email }}</p>
        </div>
      </div>

      <!-- Navigation -->
      <nav class="space-y-2">
        <router-link
          to="/dashboard"
          class="flex items-center px-3 py-2 rounded hover:bg-blue-100 dark:hover:bg-blue-900 text-gray-700 dark:text-gray-200"
          active-class="bg-blue-500 text-white"
        >
          <LayoutDashboard class="w-5 h-5 mr-2" />
          Dashboard
        </router-link>

        <router-link
          to="/transactions"
          class="flex items-center px-3 py-2 rounded hover:bg-blue-100 dark:hover:bg-blue-900 text-gray-700 dark:text-gray-200"
          active-class="bg-blue-500 text-white"
        >
          <CreditCard class="w-5 h-5 mr-2" />
          Transactions
        </router-link>

        <router-link
          to="/categories"
          class="flex items-center px-3 py-2 rounded hover:bg-blue-100 dark:hover:bg-blue-900 text-gray-700 dark:text-gray-200"
          active-class="bg-blue-500 text-white"
        >
          <Receipt class="w-5 h-5 mr-2" />
          Categories
        </router-link>

        <router-link
          to="/reports"
          class="flex items-center px-3 py-2 rounded hover:bg-blue-100 dark:hover:bg-blue-900 text-gray-700 dark:text-gray-200"
          active-class="bg-blue-500 text-white"
        >
          <BarChart class="w-5 h-5 mr-2" />
          Reports
        </router-link>

        <router-link
          to="/settings"
          class="flex items-center px-3 py-2 rounded hover:bg-blue-100 dark:hover:bg-blue-900 text-gray-700 dark:text-gray-200"
          active-class="bg-blue-500 text-white"
        >
          <Settings class="w-5 h-5 mr-2" />
          Settings
        </router-link>

        <router-link
          to="/profile"
          class="flex px-3 py-2 rounded hover:bg-blue-100 text-gray-700"
          active-class="bg-blue-500 text-white"
        >
          Edit Profile
        </router-link>
      </nav>

      <DarkModeToggle class="mt-6" />

      <button
        @click="logout"
        class="mt-10 w-full bg-red-500 text-white py-2 rounded hover:bg-red-600"
      >
        Logout
      </button>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col h-screen">
      <!-- Mobile Topbar -->
      <header class="flex justify-between items-center p-4 bg-white dark:bg-gray-800 md:hidden shadow">
        <button @click="showSidebar = true">
          <Menu class="w-6 h-6 text-gray-800 dark:text-white" />
        </button>
        <h1 class="text-lg font-semibold text-gray-800 dark:text-white">Expendy</h1>
        <div></div>
      </header>

      <!-- Router View Area -->
      <main class="flex-1 overflow-y-auto p-6">
        <router-view :key="$route.fullPath" />
      </main>
    </div>
  </div>
</template>

<script>
import {
  LayoutDashboard,
  BarChart,
  Receipt,
  Settings,
  Menu,
  CreditCard
} from 'lucide-vue-next';
import axios from 'axios';
import DarkModeToggle from '../components/DarkModeToggle.vue';

export default {
  components: {
    LayoutDashboard,
    BarChart,
    Receipt,
    Settings,
    Menu,
    CreditCard,
    DarkModeToggle,
  },
  data() {
    return {
      showSidebar: false,
      user: null,
    };
  },
  mounted() {
    this.initUser();
    this.initDarkMode();
  },
  methods: {
    async initUser() {
      try {
        const token = localStorage.getItem('auth_token');
        if (!token) throw new Error('No token found');

        const { data } = await axios.get('/api/user', {
          headers: {
            Authorization: `Bearer ${token}`
          }
        });

        this.user = data;
      } catch (error) {
        console.error('Failed to fetch user:', error);
        this.user = null;

        if (error.response?.status === 401) {
          localStorage.removeItem('token');
          this.$router.push('/login');
        }
      }
    },

    initDarkMode() {
      const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
      const theme = localStorage.getItem('theme');
      const isDark = theme === 'dark' || (!theme && prefersDark);
      if (isDark) document.documentElement.classList.add('dark');
    },

    logout() {
      localStorage.removeItem('token');
      this.$router.push('/login');
    }
  }
};
</script>
