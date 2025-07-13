<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const transactions = ref([]);
const loading = ref(true);

const fetchTransactions = async () => {
  try {
    const res = await axios.get('/api/dashboard/transactions', {
      headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
    });
    transactions.value = res.data.data || res.data;
  } catch (err) {
    console.error('Fetch error:', err);
  } finally {
    loading.value = false;
  }
};

onMounted(fetchTransactions);
</script>

<template>

   
  <div class="p-4">
    <h1 class="text-2xl font-bold mb-4">Transactions</h1>
    <TransactionForm @created="fetchTransactions" />

    <div v-if="loading">Loading...</div>
    <ul v-else>
      <li v-for="txn in transactions" :key="txn.id" class="flex justify-between border-b py-2">
        <span>{{ txn.description }}</span>
        <span class="text-blue-600 font-semibold">₦{{ txn.amount }}</span>
      </li>
    </ul>
  </div>

</template>

<script>
import TransactionForm from '@/components/TransactionForm.vue';
</script>
