<!-- <script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import PieChart from '@/components/PieChart.vue';
import LineChart from '@/components/LineChart.vue';

const summary = ref(null);
const categoryChartData = ref(null);
const monthlyChartData = ref(null);
const transactions = ref([]);
const loading = ref(true);

const fetchSummary = async () => {
  try {
    const res = await axios.get('/api/dashboard/summary', {
      headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
    });
    summary.value = res.data;
  } catch (err) {
    console.error('Summary error:', err);
  }
};

const fetchCategoryBreakdown = async () => {
  try {
    const res = await axios.get('/api/dashboard/category-breakdown', {
      headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
    });
    categoryChartData.value = {
      labels: res.data.map(c => c.category?.name || 'Uncategorized'),
      datasets: [{
        data: res.data.map(c => c.total),
        backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444'],
      }]
    };
  } catch (err) {
    console.error('Category breakdown error:', err);
  }
};

const fetchMonthlyTrends = async () => {
  try {
    const res = await axios.get('/api/dashboard/monthly-trends', {
      headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
    });
    monthlyChartData.value = {
      labels: res.data.map(m => `${m.month}/${m.year}`),
      datasets: [{
        label: 'Expenses',
        data: res.data.map(m => m.total),
        borderColor: '#3b82f6',
        backgroundColor: 'rgba(59, 130, 246, 0.2)',
      }]
    };
  } catch (err) {
    console.error('Monthly trends error:', err);
  }
};

const fetchTransactions = async () => {
  try {
    const res = await axios.get('/api/dashboard/transactions', {
      headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
    });
    transactions.value = res.data.data || res.data;
  } catch (err) {
    console.error('Transactions error:', err);
  } finally {
    loading.value = false;
  }
};

onMounted(async () => {
  await Promise.all([
    fetchSummary(),
    fetchCategoryBreakdown(),
    fetchMonthlyTrends(),
    fetchTransactions()
  ]);
});
</script>

<template>
  <div class="p-4">
    <h1 class="text-2xl font-bold mb-4">Dashboard</h1>

    <div v-if="loading">Loading...</div>
    <div v-else>
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white shadow rounded p-4">
          <p class="text-gray-500">Total Expenses</p>
          <p class="text-xl font-semibold">₦{{ summary?.total_expenses ?? 0 }}</p>
        </div>
        <div class="bg-white shadow rounded p-4">
          <p class="text-gray-500">Budget Limit</p>
          <p class="text-xl font-semibold">₦{{ summary?.budget_limit ?? 0 }}</p>
        </div>
        <div class="bg-white shadow rounded p-4">
          <p class="text-gray-500">Categories</p>
          <p class="text-xl font-semibold">{{ summary?.categories_count ?? 0 }}</p>
        </div>
        <div class="bg-white shadow rounded p-4">
          <p class="text-gray-500">Latest</p>
          <p class="text-xl font-semibold">{{ summary?.latest_expenses.length ?? 0 }} Txns</p>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div class="bg-white p-4 rounded shadow">
          <h2 class="font-semibold mb-2">Category Breakdown</h2>
          <PieChart v-if="categoryChartData" :data="categoryChartData" />
        </div>
        <div class="bg-white p-4 rounded shadow">
          <h2 class="font-semibold mb-2">Monthly Trends</h2>
          <LineChart v-if="monthlyChartData" :data="monthlyChartData" />
        </div>
      </div>

      <div class="bg-white p-4 rounded shadow">
        <h2 class="font-semibold mb-2">Recent Transactions</h2>
        <ul>
          <li v-for="txn in trAansactions" :key="txn.id" class="flex justify-between border-b py-2">
            <span>{{ txn.description }}</span>
            <span class="text-blue-600 font-semibold">₦{{ txn.amount }}</span>
          </li>
        </ul>
      </div>
    </div>
  </div>
</template> -->

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import PieChart from '@/components/PieChart.vue';
import LineChart from '@/components/LineChart.vue';
import { Wallet, TrendingUp, Tag, Clock } from 'lucide-vue-next';

const summary = ref(null);
const categoryChartData = ref(null);
const monthlyChartData = ref(null);
const transactions = ref([]);
const loading = ref(true);

const fetchSummary = async () => {
  try {
    const res = await axios.get('/api/dashboard/summary', {
      headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
    });
    summary.value = res.data;
  } catch (err) {
    console.error('Summary error:', err);
  }
};

const fetchCategoryBreakdown = async () => {
  try {
    const res = await axios.get('/api/dashboard/category-breakdown', {
      headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
    });
    categoryChartData.value = {
      labels: res.data.map(c => c.category?.name || 'Uncategorized'),
      datasets: [{
        data: res.data.map(c => c.total),
        backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444'],
      }]
    };
  } catch (err) {
    console.error('Category breakdown error:', err);
  }
};

const fetchMonthlyTrends = async () => {
  try {
    const res = await axios.get('/api/dashboard/monthly-trends', {
      headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
    });
    monthlyChartData.value = {
      labels: res.data.map(m => `${m.month}/${m.year}`),
      datasets: [{
        label: 'Expenses',
        data: res.data.map(m => m.total),
        borderColor: '#3b82f6',
        backgroundColor: 'rgba(59, 130, 246, 0.2)',
      }]
    };
  } catch (err) {
    console.error('Monthly trends error:', err);
  }
};

const fetchTransactions = async () => {
  try {
    const res = await axios.get('/api/dashboard/transactions', {
      headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
    });
    transactions.value = res.data.data || res.data;
  } catch (err) {
    console.error('Transactions error:', err);
  } finally {
    loading.value = false;
  }
};

onMounted(async () => {
  await Promise.all([
    fetchSummary(),
    fetchCategoryBreakdown(),
    fetchMonthlyTrends(),
    fetchTransactions()
  ]);
});
</script>

<template>
  <div class="p-4">
    <h1 class="text-2xl font-bold mb-4">Dashboard</h1>

    <div v-if="loading">Loading...</div>
    <div v-else>
      <h2 class="text-lg font-semibold mb-2">Summary</h2>
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white shadow rounded p-4 flex items-center gap-3">
          <Wallet class="text-blue-500" />
          <div>
            <p class="text-gray-500">Total Expenses</p>
            <p class="text-xl font-semibold">₦{{ summary?.total_expenses ?? 0 }}</p>
          </div>
        </div>
        <div class="bg-white shadow rounded p-4 flex items-center gap-3" :class="summary?.total_expenses > summary?.budget_limit ? 'bg-red-50 border border-red-400' : ''">
          <TrendingUp class="text-green-500" />
          <div>
            <p class="text-gray-500">Budget Limit</p>
            <p class="text-xl font-semibold">₦{{ summary?.budget_limit ?? 0 }}</p>
          </div>
        </div>
        <div class="bg-white shadow rounded p-4 flex items-center gap-3">
          <Tag class="text-yellow-500" />
          <div>
            <p class="text-gray-500">Categories</p>
            <p class="text-xl font-semibold">{{ summary?.categories_count ?? 0 }}</p>
          </div>
        </div>
        <div class="bg-white shadow rounded p-4 flex items-center gap-3">
          <Clock class="text-indigo-500" />
          <div>
            <p class="text-gray-500">Latest</p>
            <p class="text-xl font-semibold">{{ summary?.latest_expenses.length ?? 0 }} Txns</p>
          </div>
        </div>
      </div>

      <h2 class="text-lg font-semibold mb-2">Trends</h2>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div class="bg-white p-4 rounded shadow">
          <h2 class="font-semibold mb-2">Category Breakdown</h2>
          <PieChart v-if="categoryChartData" :data="categoryChartData" />
        </div>
        <div class="bg-white p-4 rounded shadow">
          <h2 class="font-semibold mb-2">Monthly Trends</h2>
          <LineChart v-if="monthlyChartData" :data="monthlyChartData" />
        </div>
      </div>

      <!-- <div class="bg-white p-4 rounded shadow">
        <h2 class="font-semibold mb-2 flex justify-between items-center">
          Recent Transactions
          <router-link to="/transactions" class="text-sm text-blue-600 hover:underline">View All</router-link>
        </h2>
        <ul>
          <li v-for="txn in transactions" :key="txn.id" class="flex justify-between border-b py-2">
            <span>{{ txn.description }}</span>
            <span class="text-blue-600 font-semibold">₦{{ txn.amount }}</span>
          </li>
        </ul>
      </div> -->
         <!-- Transactions List -->
      <div class="bg-white p-4 rounded shadow">
        <h2 class="font-semibold mb-2">Recent Transactions</h2>
        <ul v-if="transactions.length > 0">
          <li
            v-for="txn in transactions"
            :key="txn.id"
            class="flex justify-between border-b py-2"
          >
            <span>{{ txn.description }}</span>
            <span class="text-blue-600 font-semibold">₦{{ txn.amount }}</span>
          </li>
        </ul>
        <p v-else class="text-gray-500">No transactions found.</p>
      </div>
    </div>
  </div>
</template>
