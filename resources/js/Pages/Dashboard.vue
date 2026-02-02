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
    <!-- Budget Input -->
<div class="mb-6 bg-white dark:bg-gray-800 rounded shadow p-4">
  <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
    <div>
      <h3 class="font-semibold text-gray-700 dark:text-gray-200">
        Monthly Budget
      </h3>
      <p class="text-sm text-gray-500 dark:text-gray-400">
        Set how much you plan to spend this month
      </p>
    </div>

    <div class="flex gap-2">
      <input
        v-model="budgetAmount"
        type="number"
        placeholder="₦ Enter amount"
        class="px-3 py-2 border rounded w-40
               dark:bg-gray-700 dark:border-gray-600 dark:text-white"
      />

      <button
        @click="saveBudget"
        :disabled="savingBudget"
        class="px-4 py-2 bg-blue-500 text-white rounded
               hover:bg-blue-600 disabled:opacity-50"
      >
        {{ savingBudget ? 'Saving…' : 'Save' }}
      </button>
    </div>
  </div>

  <!-- Progress Bar -->
  <div v-if="summary.budget_limit > 0" class="mt-4">
    <div class="flex justify-between text-sm mb-1">
      <span class="text-gray-500 dark:text-gray-400">Budget used</span>
      <span class="font-medium">
        {{ budget.percentage_used }}%
      </span>
    </div>

    <div class="w-full h-2 bg-gray-200 dark:bg-gray-700 rounded">
      <div
        class="h-2 rounded transition-all"
        :class="budget.percentage_used > 90 ? 'bg-red-500' : 'bg-green-500'"
        :style="{ width: budget.percentage_used + '%' }"
      ></div>
    </div>
  </div>
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
    <div class="bg-white dark:bg-gray-800 p-4 rounded shadow mt-6">
  <h2 class="font-semibold mb-4">Budget History</h2>

  <table class="w-full text-sm">
    <thead>
      <tr class="text-left text-gray-500">
        <th>Month</th>
        <th>Amount</th>
        <th class="text-right">Actions</th>
      </tr>
    </thead>

    <tbody>
      <tr v-for="b in budgets" :key="b.id" class="border-t dark:border-gray-700">
        <td class="p-2">
          {{ monthName(b.month) }} {{ b.year }}
        </td>

        <td class="p-2">
          <input
            v-if="editing === b.id"
            v-model.number="editAmount"
            type="number"
            class="w-24 px-2 py-1 border rounded"
          />
          <span v-else>₦{{ b.amount }}</span>
        </td>

        <td class="p-2 text-right space-x-2">
          <button
            v-if="editing !== b.id"
            @click="startEdit(b)"
            class="text-blue-500"
          >Edit</button>

          <button
            v-if="editing === b.id"
            @click="saveEdit(b)"
            class="text-green-500"
          >Save</button>

          <button
            v-if="editing === b.id"
            @click="editing = null"
            class="text-gray-400"
          >Cancel</button>

          <button
            @click="deleteBudget(b.id)"
            class="text-red-500"
          >Delete</button>
        </td>
      </tr>

      <tr v-if="budgets.length === 0">
        <td colspan="3" class="text-center text-gray-400 p-4">
          No budgets yet
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
/* ================= BUDGET INPUT ================= */
const budgetAmount = ref('')
const savingBudget = ref(false)

const saveBudget = async () => {
  if (!budgetAmount.value) return

  savingBudget.value = true
  try {
    await axios.post(
      '/api/dashboard/budget',
      { amount: budgetAmount.value },
      authHeader()
    )

    budgetAmount.value = ''
    dashboardBus.emit() // 🔥 refresh everything
  } catch (e) {
    console.error(e)
    alert('Failed to save budget')
  } finally {
    savingBudget.value = false
  }
}


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
const budgets = ref([])
const editing = ref(null)
const editAmount = ref(0)

const fetchBudgets = async () => {
  const res = await axios.get('/api/dashboard/budgets', authHeader())
  budgets.value = res.data.data ?? [] 
}

const startEdit = (budget) => {
  editing.value = budget.id
  editAmount.value = budget.amount
}

const saveEdit = async (budget) => {
  await axios.put(
    `/api/dashboard/budget/${budget.id}`,
    { amount: editAmount.value },
    authHeader()
  )

  editing.value = null
  fetchDashboard()
  fetchBudgets()
}

const deleteBudget = async (id) => {
  if (!confirm('Delete this budget?')) return

  await axios.delete(`/api/dashboard/budget/${id}`, authHeader())
  fetchDashboard()
  fetchBudgets()
}

const monthName = (m) =>
  new Date(2024, m - 1).toLocaleString('default', { month: 'short' })


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
    fetchTrendChart(),
    fetchBudgets()
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

