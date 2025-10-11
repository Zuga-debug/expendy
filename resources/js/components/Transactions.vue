<template>
  <div class="p-6">
    <div class="flex justify-between items-center mb-4">
      <h1 class="text-2xl font-bold">Transactions</h1>
      <button
        @click="openModal()"
        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
      >
        + Add Transaction
      </button>
    </div>

    <div class="flex flex-wrap gap-2 mb-4 justify-between">
      <input
        v-model="searchQuery"
        placeholder="Search..."
        class="px-3 py-2 border rounded w-full md:w-1/3"
      />
      <div class="flex gap-2">
        <button
          @click="exportCSV"
          class="bg-green-600 text-white px-3 py-2 rounded hover:bg-green-700"
        >
          Export CSV
        </button>
        <button
          @click="printPage"
          class="bg-gray-700 text-white px-3 py-2 rounded hover:bg-gray-800"
        >
          Print
        </button>
      </div>
    </div>

    <!-- Transactions Table -->
    <div class="overflow-x-auto bg-white dark:bg-gray-900 rounded shadow">
      <table class="min-w-full">
        <thead class="bg-gray-100 dark:bg-gray-800 text-left">
          <tr>
            <th @click="sortBy('description')" class="px-4 py-2 cursor-pointer">Description</th>
            <th @click="sortBy('amount')" class="px-4 py-2 cursor-pointer">Amount</th>
            <th @click="sortBy('category_name')" class="px-4 py-2 cursor-pointer">Category</th>
            <th @click="sortBy('created_at')" class="px-4 py-2 cursor-pointer">Date</th>
            <th class="px-4 py-2 text-center">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="txn in filteredTransactions"
            :key="txn.id"
            class="border-t hover:bg-gray-50 dark:hover:bg-gray-800"
          >
            <td class="px-4 py-2">{{ txn.description }}</td>
            <td class="px-4 py-2">₦{{ txn.amount }}</td>
            <td class="px-4 py-2">{{ txn.category?.name || 'N/A' }}</td>
            <td class="px-4 py-2">{{ new Date(txn.created_at).toLocaleDateString() }}</td>
            <td class="px-4 py-2 flex justify-center gap-3">
              <button
                @click="openModal(txn)"
                class="text-blue-600 hover:underline"
              >Edit</button>
              <button
                @click="confirmDelete(txn.id)"
                class="text-red-600 hover:underline"
              >Delete</button>
            </td>
          </tr>
          <tr v-if="filteredTransactions.length === 0">
            <td colspan="5" class="px-4 py-4 text-center text-gray-500">
              No transactions found.
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Total Summary -->
    <div class="mt-3 text-right font-semibold">
      Total: ₦{{ totalAmount.toLocaleString() }}
    </div>

    <!-- Pagination -->
    <div class="mt-4 flex justify-center items-center gap-2">
      <button
        @click="prevPage"
        :disabled="page === 1"
        class="px-3 py-1 border rounded disabled:opacity-50"
      >Prev</button>
      <span class="px-3 py-1 border-t border-b">Page {{ page }}</span>
      <button
        @click="nextPage"
        :disabled="page * perPage >= transactions.length"
        class="px-3 py-1 border rounded disabled:opacity-50"
      >Next</button>
    </div>

    <!-- Modal -->
    <div
      v-if="showModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
      @click.self="closeModal"
    >
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 w-full max-w-md">
        <h2 class="text-xl font-semibold mb-4">
          {{ editingTransaction ? 'Edit Transaction' : 'New Transaction' }}
        </h2>

        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium mb-1">Description</label>
            <input
              v-model="form.description"
              type="text"
              class="w-full border rounded px-3 py-2"
              placeholder="Enter description"
            />
          </div>

          <div>
            <label class="block text-sm font-medium mb-1">Amount</label>
            <input
              v-model="form.amount"
              type="number"
              min="0"
              class="w-full border rounded px-3 py-2"
              placeholder="Enter amount"
            />
          </div>

          <div>
            <label class="block text-sm font-medium mb-1">Category</label>
            <select
              v-model="form.category_id"
              class="w-full border rounded px-3 py-2"
            >
              <option disabled value="">Select a category</option>
              <option
                v-for="cat in categories"
                :key="cat.id"
                :value="cat.id"
              >
                {{ cat.name }}
              </option>
            </select>
          </div>
        </div>

        <div class="flex justify-end gap-3 mt-6">
          <button @click="closeModal" class="px-4 py-2 border rounded">
            Cancel
          </button>
          <button
            @click="saveTransaction"
            class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
          >
            Save
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<!-- <script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'

// ----- state -----
const transactions = ref([])
const categories = ref([])

const form = ref({ description: '', amount: 0, category_id: null })
const editingTransaction = ref(null)
const saving = ref(false)

// pagination & sort
const page = ref(1)
const perPage = 10
const sortKey = ref('created_at')
const sortDesc = ref(true)
const searchQuery = ref('')

// filter state
const selectedCategory = ref('')
const startDate = ref('')
const endDate = ref('')
const minAmount = ref(null)
const maxAmount = ref(null)

// ----- helpers -----
const formatDate = (d) => {
  if (!d) return ''
  return new Date(d).toLocaleDateString()
}

// ----- fetch data -----
const fetchTransactions = async () => {
  try {
    const res = await axios.get('/api/dashboard/transactions', {
      headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
    })
    transactions.value = res.data.data || res.data || []
  } catch (err) {
    console.error('Failed to fetch transactions', err)
    transactions.value = []
  }
}

const fetchCategories = async () => {
  try {
    const res = await axios.get('/api/categories', {
      headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
    })
    categories.value = res.data || []
  } catch (err) {
    console.error('Failed to fetch categories', err)
    categories.value = []
  }
}

// ----- derived data & totals -----
const filteredTotalCount = computed(() => {
  // total count after filters (before pagination)
  return _applyFilters(transactions.value).length
})

const filteredTotalAmount = computed(() => {
  const arr = _applyFilters(transactions.value)
  return arr.reduce((s, t) => s + Number(t.amount || 0), 0)
})

// compute unique categories from categories API (safer)
const uniqueCategories = computed(() => categories.value.map(c => c.name))

// ----- filter application function (reusable) -----
function _applyFilters(source) {
  let data = Array.isArray(source) ? [...source] : []

  // text search (description, category name, amount)
  if (searchQuery.value) {
    const q = String(searchQuery.value).toLowerCase().trim()
    data = data.filter(t =>
      (t.description || '').toLowerCase().includes(q) ||
      (t.category?.name || '').toLowerCase().includes(q) ||
      String(t.amount || '').toLowerCase().includes(q)
    )
  }

  // category filter (category id)
  if (selectedCategory.value) {
    // selectedCategory is id (string/number)
    data = data.filter(t => String(t.category?.id) === String(selectedCategory.value))
  }

  // date filters
  if (startDate.value) {
    const s = new Date(startDate.value)
    data = data.filter(t => new Date(t.created_at) >= s)
  }
  if (endDate.value) {
    const e = new Date(endDate.value)
    // include entire day for end date
    e.setHours(23, 59, 59, 999)
    data = data.filter(t => new Date(t.created_at) <= e)
  }

  // amount filters
  if (minAmount.value !== null && minAmount.value !== '') {
    data = data.filter(t => Number(t.amount) >= Number(minAmount.value))
  }
  if (maxAmount.value !== null && maxAmount.value !== '') {
    data = data.filter(t => Number(t.amount) <= Number(maxAmount.value))
  }

  return data
}

// ----- final computed filtered + sorted + paginated list -----
const filteredTransactions = computed(() => {
  let data = _applyFilters(transactions.value)

  // sorting: support created_at and amount and description
  data.sort((a, b) => {
    let aVal = a[sortKey.value]
    let bVal = b[sortKey.value]

    // if sorting by category_name, use category name
    if (sortKey.value === 'category_name') {
      aVal = a.category?.name || ''
      bVal = b.category?.name || ''
    }

    // normalize for comparison
    aVal = aVal ?? ''
    bVal = bVal ?? ''

    // numbers vs strings
    if (!isNaN(Number(aVal)) && !isNaN(Number(bVal))) {
      return sortDesc.value ? Number(bVal) - Number(aVal) : Number(aVal) - Number(bVal)
    }

    return sortDesc.value
      ? String(bVal).toString().localeCompare(String(aVal).toString())
      : String(aVal).toString().localeCompare(String(bVal).toString())
  })

  // pagination
  const start = (page.value - 1) * perPage
  return data.slice(start, start + perPage)
})

// ----- UI helpers -----
const resetFilters = () => {
  searchQuery.value = ''
  selectedCategory.value = ''
  startDate.value = ''
  endDate.value = ''
  minAmount.value = null
  maxAmount.value = null
  page.value = 1
}

// ----- CRUD actions -----
const saveTransaction = async () => {
  saving.value = true
  try {
    const token = localStorage.getItem('auth_token')
    const isEdit = !!editingTransaction.value
    const url = isEdit ? `/api/dashboard/transactions/${editingTransaction.value.id}` : '/api/dashboard/transactions'
    const method = isEdit ? 'put' : 'post'
    const payload = {
      description: form.value.description,
      amount: form.value.amount,
      category_id: form.value.category_id
    }

    await axios[method](url, payload, { headers: { Authorization: `Bearer ${token}` } })
    await fetchTransactions()
    cancelEdit()
  } catch (err) {
    console.error('Failed to save transaction', err)
    alert(err.response?.data?.message || 'Error saving transaction')
  } finally {
    saving.value = false
  }
}

const editTransaction = (txn) => {
  editingTransaction.value = txn
  form.value = {
    description: txn.description || '',
    amount: txn.amount || 0,
    category_id: txn.category?.id || null
  }
  // scroll into view to show the form if needed
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

const cancelEdit = () => {
  editingTransaction.value = null
  form.value = { description: '', amount: 0, category_id: null }
}

const confirmDelete = async (id) => {
  if (!confirm('Are you sure you want to delete this transaction? This cannot be undone.')) return
  await deleteTransaction(id)
}

const deleteTransaction = async (id) => {
  try {
    const token = localStorage.getItem('auth_token')
    await axios.delete(`/api/dashboard/transactions/${id}`, { headers: { Authorization: `Bearer ${token}` } })
    await fetchTransactions()
  } catch (err) {
    console.error('Failed to delete transaction', err)
    alert('Failed to delete transaction')
  }
}

// export & print
const exportCSV = () => {
  const headers = ['Description', 'Amount', 'Category', 'Date']
  const rows = _applyFilters(transactions.value).map(t => [
    t.description,
    t.amount,
    t.category?.name || '',
    formatDate(t.created_at)
  ])
  const csv = [headers, ...rows].map(r => r.join(',')).join('\n')
  const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' })
  const link = document.createElement('a')
  link.href = URL.createObjectURL(blob)
  link.download = 'transactions.csv'
  link.click()
}

const printPage = () => window.print()

// sort & pagination helpers
const sortBy = (key) => {
  // allow 'category_name' sort key
  sortKey.value = key
  sortDesc.value = !sortDesc.value
}

const prevPage = () => { if (page.value > 1) page.value-- }
const nextPage = () => {
  // use filteredTotalCount (count before pagination) to determine disable
  if (page.value * perPage < filteredTotalCount.value) page.value++
}

// derived count for pagination
// const filteredTotalCount = computed(() => _applyFilters(transactions.value).length)

// ----- lifecycle -----
onMounted(() => {
  fetchCategories()
  fetchTransactions()
})
</script> -->

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'

const transactions = ref([])
const categories = ref([])
const showModal = ref(false)
const editingTransaction = ref(null)
const form = ref({ description: '', amount: '', category_id: null })

const page = ref(1)
const perPage = 10
const sortKey = ref('created_at')
const sortDesc = ref(true)
const searchQuery = ref('')

const fetchTransactions = async () => {
  try {
    const res = await axios.get('/api/dashboard/transactions', {
      headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
    })
    transactions.value = res.data.data || res.data
  } catch (error) {
    console.error('Fetch transactions failed:', error)
  }
}

const fetchCategories = async () => {
  try {
    const res = await axios.get('/api/categories', {
      headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
    })
    categories.value = res.data
  } catch (error) {
    console.error('Fetch categories failed:', error)
  }
}

const openModal = (txn = null) => {
  if (txn) {
    editingTransaction.value = { ...txn }
    form.value = {
      description: txn.description,
      amount: txn.amount,
      category_id: txn.category_id || txn.category?.id || null
    }
  } else {
    editingTransaction.value = null
    form.value = { description: '', amount: '', category_id: null }
  }
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  editingTransaction.value = null
  form.value = { description: '', amount: '', category_id: null }
}

const saveTransaction = async () => {
  const token = localStorage.getItem('auth_token')
  try {
    if (!form.value.description || !form.value.amount || !form.value.category_id) {
      alert('All fields are required.')
      return
    }

    if (editingTransaction.value) {
      // UPDATE existing
      await axios.put(`/api/dashboard/transactions/${editingTransaction.value.id}`, form.value, {
        headers: { Authorization: `Bearer ${token}` }
      })
    } else {
      // CREATE new
      await axios.post('/api/dashboard/transactions', form.value, {
        headers: { Authorization: `Bearer ${token}` }
      })
    }

    await fetchTransactions()
    closeModal()
  } catch (error) {
    console.error('Save failed:', error)
    alert('Failed to save transaction.')
  }
}

const confirmDelete = async (id) => {
  if (confirm('Are you sure you want to delete this transaction?')) {
    try {
      await axios.delete(`/api/dashboard/transactions/${id}`, {
        headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
      })
      await fetchTransactions()
    } catch (error) {
      console.error('Delete failed:', error)
      alert('Failed to delete transaction.')
    }
  }
}

const filteredTransactions = computed(() => {
  let data = [...transactions.value]

  if (searchQuery.value) {
    data = data.filter(t =>
      t.description.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      t.category?.name?.toLowerCase().includes(searchQuery.value.toLowerCase())
    )
  }

  data.sort((a, b) => {
    const aValue = a[sortKey.value] || ''
    const bValue = b[sortKey.value] || ''
    return sortDesc.value
      ? String(bValue).localeCompare(String(aValue))
      : String(aValue).localeCompare(String(bValue))
  })

  return data.slice((page.value - 1) * perPage, page.value * perPage)
})

const totalAmount = computed(() =>
  transactions.value.reduce((sum, txn) => sum + Number(txn.amount), 0)
)

const sortBy = key => {
  if (sortKey.value === key) sortDesc.value = !sortDesc.value
  else sortKey.value = key
}

const prevPage = () => { if (page.value > 1) page.value-- }
const nextPage = () => { if (page.value * perPage < transactions.value.length) page.value++ }

onMounted(() => {
  fetchTransactions()
  fetchCategories()
})
</script>

<style scoped>
/* optional small style */
th { user-select: none; }
</style>
