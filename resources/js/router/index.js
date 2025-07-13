import { createRouter, createWebHistory } from 'vue-router';
import Login from '../components/Login.vue'; 
 import Register from '../components/Register.vue';
import Dashboard from '../Pages/Dashboard.vue';
import Reports from '../Pages/Reports.vue';
import Expenses from '../Pages/Expenses.vue';
import Settings from '../Pages/Settings.vue';
import Profile from '../Pages/Profile.vue';
// import EditProfile from '../Pages/EditProfile.vue';

import AuthLayout from '@/layouts/AuthLayout.vue';
import MainLayout from '@/layouts/MainLayout.vue';



const routes = [
  {
    path: '/',
    redirect: '/login'
  },
  {
    path: '/',
    component: AuthLayout,
    children: [
      { path: 'login', component: Login },
      { path: 'register', component: Register }
    ]
  },
  {
    path: '/',
    component: MainLayout,
    children: [
      { path: 'dashboard', component: Dashboard },
      { path: 'expenses', component: Expenses },
      { path: 'reports', component: Reports },
      { path: 'settings', component: Settings },
      { path: 'profile', component: Profile }, 
      { path: 'transactions',  component: () => import('@/components/Transactions.vue'), }, 
  
    ],
    meta: { requiresAuth: true }
  }
];


const router = createRouter({
  history: createWebHistory(),
  routes
});

// 🔐 Navigation Guard
router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('token');
  if (to.meta.requiresAuth && !token) {
    next('/login');
  } else {
    next();
  }
});


export default router;
