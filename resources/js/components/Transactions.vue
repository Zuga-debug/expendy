<template>
  <div class="p-6 min-h-screen bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100">

    <!-- Top bar: Search + Add + CSV/Print -->
    <div class="flex flex-wrap justify-between items-center mb-4 gap-2">
      <input
        v-model="searchQuery"
        type="text"
        placeholder="Search transactions..."
        class="px-3 py-2 border rounded w-1/3 dark:bg-gray-800 dark:border-gray-700 dark:text-white"
      />

      <div class="flex gap-2 flex-wrap">
        <button @click="openModal()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded">+ Add</button>
        <button @click="exportCSV" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded">Export CSV</button>
        <button @click="printPage" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded">Print</button>
      </div>
    </div>

    <!-- Transactions Table -->
    <div class="overflow-x-auto rounded shadow-lg">
      <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead class="bg-gray-200 dark:bg-gray-800">
          <tr>
            <th @click="sortBy('description')" class="px-4 py-2 cursor-pointer">Description</th>
            <th @click="sortBy('amount')" class="px-4 py-2 cursor-pointer">Amount</th>
            <th @click="sortBy('category')" class="px-4 py-2 cursor-pointer">Category</th>
            <th @click="sortBy('created_at')" class="px-4 py-2 cursor-pointer">Date</th>
            <th class="px-4 py-2">Actions</th>
          </tr>
        </thead>
        <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
          <tr v-for="txn in paginatedTransactions" :key="txn.id" class="hover:bg-gray-100 dark:hover:bg-gray-800">
            <td class="px-4 py-2">{{ txn.description }}</td>
            <td class="px-4 py-2">{{ txn.amount }}</td>
            <td class="px-4 py-2">{{ txn.category?.name || 'Uncategorized' }}</td>
            <td class="px-4 py-2">{{ new Date(txn.created_at).toLocaleDateString() }}</td>
            <td class="px-4 py-2 space-x-2">
              <button @click="openModal(txn)" class="px-2 py-1 bg-yellow-500 hover:bg-yellow-600 text-white rounded">Edit</button>
              <button @click="confirmDelete(txn.id)" class="px-2 py-1 bg-red-500 hover:bg-red-600 text-white rounded">Delete</button>
            </td>
          </tr>
          <tr v-if="filteredTransactions.length === 0">
            <td colspan="5" class="px-4 py-2 text-center text-gray-500 dark:text-gray-400">
              No transactions found.
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div class="flex justify-between items-center mt-4">
      <div>Total: {{ totalAmount }}</div>
      <div class="space-x-2">
        <button @click="prevPage" :disabled="page === 1" class="px-3 py-1 bg-gray-300 dark:bg-gray-700 rounded disabled:opacity-50">Prev</button>
        <span>Page {{ page }} of {{ totalPages }}</span>
        <button @click="nextPage" :disabled="page >= totalPages" class="px-3 py-1 bg-gray-300 dark:bg-gray-700 rounded disabled:opacity-50">Next</button>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-md relative">
        <h2 class="text-xl font-bold mb-4">{{ editingTransaction ? 'Edit' : 'Add' }} Transaction</h2>

        <div class="mb-4">
          <label class="block mb-1">Description</label>
          <input
            v-model="form.description"
            type="text"
            class="w-full px-3 py-2 border rounded dark:bg-gray-700 dark:border-gray-600 dark:text-white"
          />
        </div>

        <div class="mb-4">
          <label class="block mb-1">Amount</label>
          <input
            v-model.number="form.amount"
            type="number"
            class="w-full px-3 py-2 border rounded dark:bg-gray-700 dark:border-gray-600 dark:text-white"
          />
        </div>

        <div class="mb-4">
          <label class="block mb-1">Category</label>
          <select
            v-model="form.category_id"
            class="w-full px-3 py-2 border rounded dark:bg-gray-700 dark:border-gray-600 dark:text-white"
          >
            <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
          </select>
        </div>

        <div class="flex justify-end space-x-2">
          <button @click="closeModal" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded">Cancel</button>
          <button @click="saveTransaction" :disabled="saving" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded">
            {{ saving ? 'Saving...' : 'Save' }}
          </button>
        </div>

        <button @click="closeModal" class="absolute top-2 right-2 text-gray-600 dark:text-gray-300">&times;</button>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'
import { useEventBus } from '@vueuse/core'

const dashboardBus = useEventBus('dashboard:refresh')

const transactions = ref([])
const categories = ref([])
const showModal = ref(false)
const saving = ref(false)
const editingTransaction = ref(null)

const confirmDelete = (id) => {
  if (!id) return

  if (confirm('⚠️ Are you sure you want to delete this transaction?')) {
    deleteTransaction(id)
  }
}

const form = ref({
  description: '',
  amount: '',
  category_id: null
})

const searchQuery = ref('')
const page = ref(1)
const perPage = 10

const authHeader = () => ({
  headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
})

/* ================= FETCH ================= */
const fetchCategories = async () => {
  const res = await axios.get('/api/categories', authHeader())
  categories.value = res.data ?? []
}

const fetchTransactions = async () => {
  const res = await axios.get('/api/dashboard/transactions', authHeader())
  transactions.value = res.data.data ?? []
}

/* ================= MODAL ================= */
const openModal = (txn = null) => {
  editingTransaction.value = txn
  form.value = txn
    ? { description: txn.description, amount: txn.amount, category_id: txn.category_id }
    : { description: '', amount: '', category_id: categories.value[0]?.id || null }
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  editingTransaction.value = null
}

/* ================= SAVE ================= */
const saveTransaction = async () => {
  saving.value = true
  try {
    if (editingTransaction.value) {
      await axios.put(
        `/api/dashboard/transactions/${editingTransaction.value.id}`,
        form.value,
        authHeader()
      )
    } else {
      await axios.post('/api/dashboard/transactions', form.value, authHeader())
    }

    await fetchTransactions()
    dashboardBus.emit()   // 🔥 refresh dashboard
    closeModal()
  } finally {
    saving.value = false
  }
}

/* ================= DELETE ================= */
const deleteTransaction = async (id) => {
  if (!confirm('Delete this transaction?')) return
  await axios.delete(`/api/dashboard/transactions/${id}`, authHeader())
  await fetchTransactions()
  dashboardBus.emit()     // 🔥 refresh dashboard
}

/* ================= COMPUTED ================= */
const filteredTransactions = computed(() => {
  const q = searchQuery.value.toLowerCase()
  return transactions.value.filter(t =>
    t.description.toLowerCase().includes(q)
  )
})

const totalPages = computed(() =>
  Math.ceil(filteredTransactions.value.length / perPage)
)

const paginatedTransactions = computed(() =>
  filteredTransactions.value.slice(
    (page.value - 1) * perPage,
    page.value * perPage
  )
)

onMounted(() => {
  fetchCategories()
  fetchTransactions()
})
</script>
