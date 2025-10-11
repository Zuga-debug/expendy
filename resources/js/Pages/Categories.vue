<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const categories = ref([])
const newCategory = ref('')
const editingCategory = ref(null)
const error = ref('')

const fetchCategories = async () => {
  try {
    const res = await axios.get('/api/categories', {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('auth_token')}`
      }
    })
    categories.value = res.data
  } catch (err) {
    console.error('Failed to fetch categories:', err)
  }
}

const createCategory = async () => {
  error.value = ''
  if (!newCategory.value.trim()) {
    error.value = 'Name is required.'
    return
  }
  try {
    const res = await axios.post('/api/categories', {
      name: newCategory.value
    }, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('auth_token')}`
      }
    })
    categories.value.push(res.data)
    newCategory.value = ''
  } catch (err) {
    error.value = err.response?.data?.message || 'Error creating category.'
  }
}

const deleteCategory = async (id) => {
  if (!confirm('Delete this category?')) return
  try {
    await axios.delete(`/api/categories/${id}`, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('auth_token')}`
      }
    })
    categories.value = categories.value.filter(c => c.id !== id)
  } catch (err) {
    console.error('Delete failed:', err)
  }
}

onMounted(fetchCategories)
</script>

<template>
  <div class="p-4">
    <h1 class="text-2xl font-bold mb-4">Manage Categories</h1>

    <div class="mb-4">
      <input v-model="newCategory" class="border p-2 rounded mr-2" placeholder="New category name" />
      <button @click="createCategory" class="bg-blue-600 text-white px-4 py-2 rounded">Add</button>
      <p class="text-red-500 text-sm mt-1">{{ error }}</p>
    </div>

    <ul class="divide-y">
      <li v-for="cat in categories" :key="cat.id" class="py-2 flex justify-between">
        <span>{{ cat.name }}</span>
        <button @click="deleteCategory(cat.id)" class="text-red-500 hover:underline">Delete</button>
      </li>
    </ul>
  </div>
</template>
