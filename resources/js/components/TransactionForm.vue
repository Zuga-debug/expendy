<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const form = ref({
  description: '',
  amount: 0,
  category_id: null,
})

const categories = ref([])
const formErrors = ref({})

const emit = defineEmits(['saved'])

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
  formErrors.value = {}

  if (!form.value.category_id) {
    formErrors.value.category_id = 'Please select a category.'
    return
  }
  console.log('Submitting form:', form.value);

  try {
    await axios.post('/api/dashboard/transactions', form.value, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('auth_token')}`
      }
    })

    emit('saved')

    // Reset form and errors
    form.value = {
      description: '',
      amount: 0,
      category_id: null,
    }
    formErrors.value = {}
  } catch (err) {
    if (err.response?.data?.errors) {
      formErrors.value = err.response.data.errors
    }
    console.error('Transaction save failed:', err)
  }
}

onMounted(fetchCategories)
</script>

<template>
  <form @submit.prevent="saveTransaction" class="space-y-4">
    <div>
      <input
        v-model="form.description"
        placeholder="Description"
        class="w-full px-4 py-2 border rounded"
      />
      <span v-if="formErrors.description" class="text-red-500 text-sm">
        {{ formErrors.description }}
      </span>
    </div>

    <div>
      <input
        v-model.number="form.amount"
        type="number"
        placeholder="Amount"
        class="w-full px-4 py-2 border rounded"
      />
      <span v-if="formErrors.amount" class="text-red-500 text-sm">
        {{ formErrors.amount }}
      </span>
    </div>

    <div>
      <select v-model.number="form.category_id" class="w-full px-4 py-2 border rounded">
        <option :value="null" disabled>-- Select Category --</option>

        <option v-for="cat in categories" :key="cat.id" :value="cat.id">
            {{ cat.name }}
        </option>
      </select>
      <span v-if="formErrors.category_id" class="text-red-500 text-sm">
        {{ formErrors.category_id }}
      </span>
    </div>

    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
      Save Transaction
    </button>
  </form>
</template>
