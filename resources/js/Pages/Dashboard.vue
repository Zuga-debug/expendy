
<!-- <script setup>

// export default {

//   data() {
//     return {
//       balance: 5000,
//       income: 8000,
//       expenses: 3000,
//       transactions: [
//         { id: 1, name: "Grocery", amount: 50, type: "expense" },
//         { id: 2, name: "Salary", amount: 2000, type: "income" },
//         { id: 3, name: "Electricity Bill", amount: 100, type: "expense" },
//       ],
//     };
//   },
// };

import { ref, onMounted } from 'vue';
import axios from 'axios';

const dashboardData = ref(null);

const fetchDashboardData = async () => {
  const res = await axios.get('/api/dashboard', {
    headers: {
      Authorization: `Bearer ${localStorage.getItem('token')}`
    }
  });
  dashboardData.value = res.data;
};

onMounted(() => {
  fetchDashboardData();

  // Optional: auto refresh every 30 seconds
  setInterval(fetchDashboardData, 30000);
});
</script> -->

<!-- <template>
    <div class="p-6 bg-gray-100 min-h-screen">
      Dashboard Header
      <div class="flex justify-between items-center mb-6 bg-white dark:bg-gray-900 text-black dark:text-white ">
        <h1 class="text-2xl font-bold">Dashboard</h1>
        <button class="bg-blue-500 text-white px-4 py-2 rounded">Add Expense</button>
      </div>
  
      Balance Overview Cards
      <div class="grid grid-cols-3 gap-4">
        <div class="bg-white p-6 rounded shadow">
          <h2 class="text-gray-500">Total Balance</h2>
          <p class="text-2xl font-semibold text-green-500">${{ balance }}</p>
        </div>
        <div class="bg-white p-6 rounded shadow">
          <h2 class="text-gray-500">Total Income</h2>
          <p class="text-2xl font-semibold text-blue-500">${{ income }}</p>
        </div>
        <div class="bg-white p-6 rounded shadow">
          <h2 class="text-gray-500">Total Expenses</h2>
          <p class="text-2xl font-semibold text-red-500">${{ expenses }}</p>
        </div>
      </div> -->
  
      <!-- Recent Transactions -->
      <!-- <div class="mt-6 bg-white p-6 rounded shadow">
        <h2 class="text-xl font-bold mb-4">Recent Transactions</h2>
        <ul>
          <li v-for="transaction in transactions" :key="transaction.id" class="flex justify-between py-2 border-b">
            <span>{{ transaction.name }}</span>
            <span :class="transaction.type === 'expense' ? 'text-red-500' : 'text-green-500'">
              ${{ transaction.amount }}
            </span>
          </li>
        </ul>
      </div> -->
     
  <!-- <div v-if="dashboardData">
    <h1 class="text-xl font-bold">Welcome back!</h1>
    <p>Total Expenses: {{ dashboardData.total_expenses }}</p>
    <p>Categories: {{ dashboardData.categories_count }}</p>

    <h2 class="mt-4 font-semibold">Latest Expenses</h2>
    <ul>
      <li v-for="expense in dashboardData.latest_expenses" :key="expense.id">
        {{ expense.description }} - {{ expense.amount }}
      </li>
    </ul>
  </div> -->


    <!-- </div>
  </template>
   -->


   <script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const summary = ref(null);
const loading = ref(true);

const fetchSummary = async () => {
  try {
    const res = await axios.get('/api/dashboard/summary', {
      headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
    });
    summary.value = res.data;
  } catch (err) {
    console.error('Failed to load dashboard summary:', err);
  } finally {
    loading.value = false;
  }
};

onMounted(fetchSummary);
</script>

<template>
  <div>
    <h2 class="text-xl font-bold mb-4">Dashboard</h2>

    <div v-if="loading">Loading...</div>
    <div v-else>
      <p><strong>Total Expenses:</strong> ₦{{ summary.total_expenses }}</p>
      <p><strong>Categories:</strong> {{ summary.categories_count }}</p>

      <h3 class="mt-4 font-semibold">Latest Transactions</h3>
      <ul>
        <li v-for="expense in summary.latest_expenses" :key="expense.id">
          ₦{{ expense.amount }} - {{ expense.description }}
        </li>
      </ul>
    </div>
  </div>
</template>



 