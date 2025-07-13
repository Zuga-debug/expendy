<template>
  <div class="min-h-screen bg-gray-100 dark:bg-gray-900 flex">
   
    <!-- Mobile Backdrop -->
    <div
      v-if="showSidebar"
      class="fixed inset-0 bg-black bg-opacity-50 z-40 md:hidden"
      @click="showSidebar = false"
    ></div>

    <!-- Sidebar -->
    <aside
      :class="[
        'fixed md:relative z-50 md:z-auto transition-transform duration-300 bg-white dark:bg-gray-800 w-64 h-full p-4 shadow-lg',
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

      <!-- <button @click="toggleDarkMode" class="mt-4 px-4 py-2 bg-gray-800 text-white rounded">
  Toggle Dark Mode
</button> -->

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
          to="/expenses"
          class="flex items-center px-3 py-2 rounded hover:bg-blue-100 dark:hover:bg-blue-900 text-gray-700 dark:text-gray-200"
          active-class="bg-blue-500 text-white"
        >
          <Receipt class="w-5 h-5 mr-2" />
          Expenses
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
        <DarkModeToggle class="absolute top-4 right-4"/>
        <router-link
          to="/profile"
         class="flex px-3 py-2 rounded hover:bg-blue-100 text-gray-700"
         active-class="bg-blue-500 text-white"
        >
       EditProfile
       </router-link>
      </nav>

      <button
        @click="logout"
        class="mt-10 w-full bg-red-500 text-white py-2 rounded hover:bg-red-600"
      >
        Logout
      </button>
    </aside>

    <!-- Main content -->
    <div class="flex-1 flex flex-col">
      <!-- Topbar for mobile toggle -->
      <header class="flex justify-between items-center p-4 bg-white dark:bg-gray-800 md:hidden shadow">
        <button @click="showSidebar = true">
          <Menu class="w-6 h-6 text-gray-800 dark:text-white" />
        </button>
        <h1 class="text-lg font-semibold text-gray-800 dark:text-white">Expendy</h1>
        <div></div>
      </header>

      <main class="p-6 overflow-y-auto flex-1">
        <router-view :key="$route.fullPath"/>
      </main>
    </div>
  </div>
</template>

<script>
import { LayoutDashboard, BarChart, Receipt, Settings, Menu } from 'lucide-vue-next';
import axios from 'axios';
import DarkModeToggle from '../components/DarkModeToggle.vue';

export default {
  components: {
    LayoutDashboard,
    BarChart,
    Receipt,
    Settings,
    Menu,
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
    // 🔐 Fetch authenticated user info
    async initUser() {
      try {
        const token = localStorage.getItem('token');
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

    // 🌙 Initialize dark mode based on preference or localStorage
    initDarkMode() {
      const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
      const theme = localStorage.getItem('theme');
      const isDark = theme === 'dark' || (!theme && prefersDark);

      if (isDark) {
        document.documentElement.classList.add('dark');
      }
    },

    // 🌗 Toggle dark/light mode
    toggleDarkMode() {
      const isDark = document.documentElement.classList.toggle('dark');
      localStorage.setItem('theme', isDark ? 'dark' : 'light');
    },

    // 🚪 Log user out
    logout() {
      localStorage.removeItem('token');
      this.$router.push('/login');
    }
  }
};
</script>

