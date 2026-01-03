<template>
  <div class="p-4 relative min-h-[600px]">
    <!-- Spinner -->
    <transition name="fade">
      <div
        v-show="loading"
        class="absolute inset-0 flex items-center justify-center bg-white/70 dark:bg-gray-900/70 z-50 rounded-lg"
      >
        <div class="w-10 h-10 border-4 border-blue-500 border-t-transparent rounded-full animate-spin"></div>
      </div>
    </transition>

    <!-- Refresh -->
    <div class="flex justify-end mb-3">
      <button
        @click="refreshDashboard"
        class="px-3 py-1 text-sm bg-blue-500 text-white rounded hover:bg-blue-600"
        :disabled="loading"
      >
        {{ loading ? 'Refreshing…' : 'Refresh' }}
      </button>
    </div>

    <!-- Summary -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
      <template v-if="loading">
        <SkeletonCard v-for="n in 4" :key="n" />
</template>
  <template v-else>
    <StatCard title="Total Expenses" :value="summary.total_expenses" />
    <StatCard title="Budget Limit" :value="summary.budget_limit" />
    <StatCard title="Categories" :value="summary.categories_count" />
    <StatCard title="Remaining Budget" :value="budget.remaining_budget" green />
  </template>
</div>


    <!-- Charts -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
      <div class="bg-white dark:bg-gray-800 p-4 rounded shadow">
        <h2 class="font-semibold mb-2">Category Breakdown</h2>
        <canvas ref="categoryCanvas"></canvas>
      </div>

      <div class="bg-white dark:bg-gray-800 p-4 rounded shadow">
        <h2 class="font-semibold mb-2">Monthly Trends</h2>
        <canvas ref="trendCanvas"></canvas>
      </div>
    </div>

    <!-- Transactions -->
    <div class="bg-white dark:bg-gray-800 p-4 rounded shadow">
      <h2 class="font-semibold mb-4">Recent Transactions</h2>
      <table class="w-full text-sm">
        <tbody>
          <tr
            v-for="txn in summary.latest_expenses || []"
            :key="txn.id"
            class="border-t dark:border-gray-600"
          >
            <td class="p-2">{{ txn.description }}</td>
            <td class="p-2">₦{{ txn.amount }}</td>
            <td class="p-2">{{ new Date(txn.created_at).toLocaleDateString() }}</td>
          </tr>
          <tr v-if="!summary.latest_expenses?.length">
            <td colspan="3" class="text-center text-gray-400 p-3">
              No transactions yet
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount, nextTick } from 'vue'
import axios from 'axios'
import Chart from 'chart.js/auto'
import { useEventBus } from '@vueuse/core'
import StatCard from '@/Components/StatCard.vue'
import SkeletonCard from '@/Components/SkeletonCard.vue'

const dashboardBus = useEventBus('dashboard:refresh')

const loading = ref(true)

const summary = ref({
  total_expenses: 0,
  budget_limit: 0,
  categories_count: 0,
  latest_expenses: []
})

const budget = ref({
  remaining_budget: 0
})

const categoryCanvas = ref(null)
const trendCanvas = ref(null)

let categoryChart = null
let trendChart = null

const authHeader = () => ({
  headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
})

const chartTextColor = () =>
  document.documentElement.classList.contains('dark')
    ? '#d1d5db'
    : '#374151'

/* ================= FETCH ================= */
const fetchSummary = async () => {
  const res = await axios.get('/api/dashboard/summary', authHeader())
  summary.value = res.data.data
}

const fetchBudget = async () => {
  const res = await axios.get('/api/dashboard/budget-status', authHeader())
  budget.value = res.data.data
}

const fetchCategoryChart = async () => {
  const res = await axios.get('/api/dashboard/category-breakdown', authHeader())
  const rows = res.data.data ?? []

  await nextTick()
  categoryChart?.destroy()

  categoryChart = new Chart(categoryCanvas.value, {
    type: 'doughnut',
    data: {
      labels: rows.map(r => r.category_name),
      datasets: [{ data: rows.map(r => r.total) }]
    },
    options: {
      plugins: {
        legend: { labels: { color: chartTextColor() } }
      }
    }
  })
}

const fetchTrendChart = async () => {
  const res = await axios.get('/api/dashboard/monthly-trends', authHeader())
  const rows = res.data.data.reverse()

  await nextTick()
  trendChart?.destroy()

  trendChart = new Chart(trendCanvas.value, {
    type: 'line',
    data: {
      labels: rows.map(r => r.label),
      datasets: [{
        data: rows.map(r => r.total),
        borderColor: '#3b82f6',
        fill: true
      }]
    },
    options: {
      scales: {
        x: { ticks: { color: chartTextColor() } },
        y: { ticks: { color: chartTextColor() }, beginAtZero: true }
      }
    }
  })
}

/* ================= MASTER ================= */
const fetchDashboard = async () => {
  loading.value = true
  await Promise.all([
    fetchSummary(),
    fetchBudget(),
    fetchCategoryChart(),
    fetchTrendChart()
  ])
  loading.value = false
}

/* ================= EVENTS ================= */
dashboardBus.on(fetchDashboard)

onMounted(fetchDashboard)

onBeforeUnmount(() => {
  categoryChart?.destroy()
  trendChart?.destroy()
})
</script>

