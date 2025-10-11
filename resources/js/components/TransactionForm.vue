<template>
  <form @submit.prevent="saveTransaction" class="space-y-4">
    <div>
      <label class="block mb-1 text-sm font-medium text-gray-700">Description</label>
      <input v-model="form.description" type="text" class="w-full border rounded px-3 py-2" required />
    </div>

    <div>
      <label class="block mb-1 text-sm font-medium text-gray-700">Amount</label>
      <input v-model.number="form.amount" type="number" class="w-full border rounded px-3 py-2" required />
    </div>

    <div>
      <label class="block mb-1 text-sm font-medium text-gray-700">Category</label>
      <select v-model.number="form.category_id" class="w-full border rounded px-3 py-2" required>
        <option :value="null" disabled>Select Category</option>
        <option v-for="category in categories" :key="category.id" :value="category.id">
          {{ category.name }}
        </option>
      </select>
    </div>

    <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">
      {{ isEditing ? 'Update' : 'Create' }} Transaction
    </button>
  </form>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue'
import axios from 'axios'

const props = defineProps({
  initialData: Object
})

const emit = defineEmits(['saved'])

const form = ref({
  description: '',
  amount: 0,
  category_id: null
})

const isEditing = ref(false)
const categories = ref([])

watch(
  () => props.initialData,
  (newVal) => {
    if (newVal && newVal.id) {
      form.value = {
        description: newVal.description,
        amount: newVal.amount,
        category_id: newVal.category?.id ?? null
      }
      isEditing.value = true
    } else {
      resetForm()
    }
  },
  { immediate: true }
)

const resetForm = () => {
  form.value = {
    description: '',
    amount: 0,
    category_id: null
  }
  isEditing.value = false
}

const fetchCategories = async () => {
  try {
    const res = await axios.get('/api/categories', {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('auth_token')}`
      }
    })
    categories.value = res.data
  } catch (err) {
    console.error('Failed to load categories:', err)
  }
}

const saveTransaction = async () => {
  const token = localStorage.getItem('auth_token')
  const isEdit = isEditing.value && props.initialData?.id

  const url = isEdit
    ? `/api/dashboard/transactions/${props.initialData.id}`
    : '/api/dashboard/transactions'

  const method = isEdit ? 'put' : 'post'

  try {
    await axios[method](url, form.value, {
      headers: { Authorization: `Bearer ${token}` }
    })
    emit('saved')
    resetForm()
  } catch (error) {
    console.error('Failed to save transaction', error)
    alert('Error saving transaction')
  }
}

onMounted(fetchCategories)
</script>
