<template>
  <div class="space-y-6 pb-8">
    <!-- Breadcrumb -->
    <nav class="flex items-center space-x-2 text-sm" aria-label="Breadcrumb">
      <router-link to="/admin/dashboard" :class="isDarkMode ? 'text-gray-400 hover:text-gray-200' : 'text-gray-500 hover:text-gray-700'">
        <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
          <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
        </svg>
        Dashboard
      </router-link>
      <span :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'">/</span>
      <router-link to="/admin/customers" :class="isDarkMode ? 'text-gray-400 hover:text-gray-200' : 'text-gray-500 hover:text-gray-700'">
        Customers
      </router-link>
      <span :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'">/</span>
      <span :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">{{ customer?.name || 'Loading...' }}</span>
    </nav>

    <!-- Header -->
    <div class="flex items-center gap-4">
      <router-link 
        to="/admin/customers" 
        class="p-2 rounded-lg transition-colors"
        :class="isDarkMode ? 'hover:bg-gray-700 text-gray-400' : 'hover:bg-gray-100 text-gray-600'"
        aria-label="Back to customers"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
      </router-link>
      <div>
        <h1 class="text-2xl md:text-3xl font-bold" :class="isDarkMode ? 'text-white' : 'text-gray-900'">Customer Details</h1>
        <p class="text-sm" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">{{ customer?.name || 'Loading...' }}</p>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" :class="[
      'rounded-2xl shadow-lg p-12 text-center',
      isDarkMode ? 'bg-gray-800' : 'bg-white'
    ]">
      <div class="flex flex-col items-center justify-center">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
        <p class="mt-4" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Loading customer details...</p>
      </div>
    </div>

    <!-- Customer Content -->
    <div v-else-if="customer" class="space-y-6">
      <!-- Customer Profile Card -->
      <div :class="[
        'rounded-2xl shadow-lg',
        isDarkMode ? 'bg-gray-800' : 'bg-white'
      ]">
        <div class="p-6 flex flex-col sm:flex-row items-start sm:items-center gap-6">
          <!-- Avatar -->
          <div class="w-20 h-20 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center flex-shrink-0">
            <span class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ getInitials(customer.name) }}</span>
          </div>
          
          <!-- Customer Info -->
          <div class="flex-1">
            <h2 class="text-2xl font-bold" :class="isDarkMode ? 'text-white' : 'text-gray-900'">{{ customer.name }}</h2>
            <div class="flex flex-wrap gap-4 mt-1">
              <span class="text-sm flex items-center gap-1" :class="isDarkMode ? 'text-gray-300' : 'text-gray-600'">
                <svg class="w-4 h-4" :class="isDarkMode ? 'text-gray-400' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                {{ customer.email }}
              </span>
              <span v-if="customer.phone" class="text-sm flex items-center gap-1" :class="isDarkMode ? 'text-gray-300' : 'text-gray-600'">
                <svg class="w-4 h-4" :class="isDarkMode ? 'text-gray-400' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                </svg>
                {{ customer.phone }}
              </span>
              <span class="text-sm flex items-center gap-1">
                <span class="px-2 py-1 text-xs font-semibold rounded-full" :class="customer.status === 'active' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'">
                  {{ customer.status || 'Active' }}
                </span>
              </span>
              <span class="text-sm" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">
                Joined {{ formatDate(customer.created_at) }}
              </span>
            </div>
          </div>

          <!-- Quick Stats -->
          <div class="flex gap-6 text-center">
            <div>
              <p class="text-2xl font-bold" :class="isDarkMode ? 'text-white' : 'text-gray-900'">{{ customer.orders_count || 0 }}</p>
              <p class="text-xs" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Orders</p>
            </div>
            <div>
              <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">${{ parseFloat(customer.total_spent || 0).toFixed(2) }}</p>
              <p class="text-xs" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Total Spent</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div :class="[
          'rounded-2xl shadow-lg p-5 border-l-4 border-blue-500',
          isDarkMode ? 'bg-gray-800' : 'bg-white'
        ]">
          <p class="text-sm" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Total Orders</p>
          <p class="text-2xl font-bold" :class="isDarkMode ? 'text-white' : 'text-gray-900'">{{ customer.orders_count || 0 }}</p>
        </div>
        <div :class="[
          'rounded-2xl shadow-lg p-5 border-l-4 border-green-500',
          isDarkMode ? 'bg-gray-800' : 'bg-white'
        ]">
          <p class="text-sm" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Total Spent</p>
          <p class="text-2xl font-bold text-green-600 dark:text-green-400">${{ parseFloat(customer.total_spent || 0).toFixed(2) }}</p>
        </div>
        <div :class="[
          'rounded-2xl shadow-lg p-5 border-l-4 border-purple-500',
          isDarkMode ? 'bg-gray-800' : 'bg-white'
        ]">
          <p class="text-sm" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Average Order</p>
          <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">
            ${{ customer.orders_count > 0 ? parseFloat(customer.total_spent / customer.orders_count).toFixed(2) : '0.00' }}
          </p>
        </div>
        <div :class="[
          'rounded-2xl shadow-lg p-5 border-l-4 border-orange-500',
          isDarkMode ? 'bg-gray-800' : 'bg-white'
        ]">
          <p class="text-sm" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Last Order</p>
          <p class="text-lg font-semibold" :class="isDarkMode ? 'text-white' : 'text-gray-900'">{{ customer.last_order_date ? formatDate(customer.last_order_date) : 'No orders' }}</p>
        </div>
      </div>

      <!-- Customer's Orders -->
      <div :class="[
        'rounded-2xl shadow-lg overflow-hidden',
        isDarkMode ? 'bg-gray-800' : 'bg-white'
      ]">
        <div class="p-4 border-b flex justify-between items-center" :class="isDarkMode ? 'border-gray-700' : 'border-gray-200'">
          <h3 class="text-sm font-semibold uppercase tracking-wider" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Order History</h3>
          <span class="text-sm" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">{{ orders.length }} orders</span>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full">
            <thead :class="isDarkMode ? 'bg-gray-700/50' : 'bg-gray-50'">
              <tr>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Order #</th>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase hidden sm:table-cell" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Date</th>
                <th class="px-4 py-3 text-right text-xs font-medium uppercase" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Total</th>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Status</th>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase hidden md:table-cell" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Payment</th>
                <th class="px-4 py-3 text-center text-xs font-medium uppercase" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y" :class="isDarkMode ? 'divide-gray-700' : 'divide-gray-200'">
              <tr v-if="ordersLoading" class="text-center">
                <td colspan="6" class="px-4 py-8">
                  <div class="flex justify-center items-center">
                    <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600"></div>
                    <span class="ml-2 text-sm" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Loading orders...</span>
                  </div>
                </td>
              </tr>
              <tr v-else-if="orders.length === 0" class="text-center">
                <td colspan="6" class="px-4 py-8" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">
                  <svg class="w-12 h-12 mx-auto mb-2" :class="isDarkMode ? 'text-gray-600' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                  </svg>
                  <p>No orders from this customer yet</p>
                </td>
              </tr>
              <tr v-for="order in orders" :key="order.id" class="transition-colors" :class="isDarkMode ? 'hover:bg-gray-700/50' : 'hover:bg-gray-50'">
                <td class="px-4 py-3">
                  <router-link :to="`/admin/orders/${order.id}`" class="text-sm font-medium text-blue-600 dark:text-blue-400 hover:underline">
                    {{ order.order_number }}
                  </router-link>
                </td>
                <td class="px-4 py-3 hidden sm:table-cell">
                  <span class="text-sm" :class="isDarkMode ? 'text-gray-300' : 'text-gray-600'">{{ formatDate(order.created_at) }}</span>
                </td>
                <td class="px-4 py-3 text-right">
                  <span class="text-sm font-semibold" :class="isDarkMode ? 'text-white' : 'text-gray-900'">${{ parseFloat(order.total_amount).toFixed(2) }}</span>
                </td>
                <td class="px-4 py-3">
                  <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full" :class="getOrderStatusClass(order.status)">
                    {{ formatStatus(order.status) }}
                  </span>
                </td>
                <td class="px-4 py-3 hidden md:table-cell">
                  <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full" :class="getPaymentStatusClass(order.payment_status)">
                    {{ formatStatus(order.payment_status) }}
                  </span>
                </td>
                <td class="px-4 py-3 text-center">
                  <router-link :to="`/admin/orders/${order.id}`" 
                    class="p-1.5 rounded-lg transition-colors inline-block" 
                    :class="isDarkMode ? 'text-blue-400 hover:bg-blue-900/20' : 'text-blue-600 hover:bg-blue-50'"
                    title="View Order">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                  </router-link>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Error State -->
    <div v-else :class="[
      'rounded-2xl shadow-lg p-12 text-center',
      isDarkMode ? 'bg-gray-800' : 'bg-white'
    ]">
      <svg class="w-16 h-16 mx-auto mb-4" :class="isDarkMode ? 'text-gray-600' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
      </svg>
      <h3 class="text-lg font-medium mb-2" :class="isDarkMode ? 'text-white' : 'text-gray-900'">Customer Not Found</h3>
      <p class="mb-4" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">The customer you're looking for doesn't exist or has been removed.</p>
      <router-link to="/admin/customers" class="px-4 py-2 bg-gradient-to-r from-primary-500 to-primary-600 text-white rounded-lg hover:from-primary-600 hover:to-primary-700 transition-all duration-200 shadow-md inline-block">
        Return to Customers
      </router-link>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';

const route = useRoute();
const router = useRouter();

const customer = ref(null);
const orders = ref([]);
const loading = ref(false);
const ordersLoading = ref(false);
const isDarkMode = ref(false);
let darkModeObserver = null;

// Check dark mode from localStorage or system preference
const checkDarkMode = () => {
  const savedTheme = localStorage.getItem('theme');
  if (savedTheme === 'dark') {
    isDarkMode.value = true;
  } else if (savedTheme === 'light') {
    isDarkMode.value = false;
  } else {
    isDarkMode.value = window.matchMedia('(prefers-color-scheme: dark)').matches;
  }
};

// Watch for theme changes using MutationObserver
const setupDarkModeWatcher = () => {
  const htmlElement = document.documentElement;
  darkModeObserver = new MutationObserver((mutations) => {
    mutations.forEach((mutation) => {
      if (mutation.attributeName === 'class') {
        const hasDarkClass = htmlElement.classList.contains('dark');
        if (hasDarkClass !== isDarkMode.value) {
          isDarkMode.value = hasDarkClass;
        }
      }
    });
  });
  darkModeObserver.observe(htmlElement, { attributes: true });
};

const getOrderStatusClass = (status) => {
  const classes = {
    pending: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
    processing: 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
    completed: 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
    cancelled: 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
    shipped: 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400',
    delivered: 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400'
  };
  return classes[status] || 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
};

const getPaymentStatusClass = (status) => {
  const classes = {
    pending: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
    paid: 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
    failed: 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
    refunded: 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'
  };
  return classes[status] || 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
};

const formatStatus = (status) => {
  return status ? status.charAt(0).toUpperCase() + status.slice(1) : 'N/A';
};

const formatDate = (date) => {
  if (!date) return 'N/A';
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};

const getInitials = (name) => {
  if (!name) return '?';
  return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2);
};

const fetchCustomer = async () => {
  loading.value = true;
  try {
    const response = await axios.get(`/api/admin/customers/${route.params.id}`);
    customer.value = response.data.data || response.data;
    orders.value = customer.value.orders || [];
  } catch (error) {
    console.error('Error fetching customer:', error);
    showToast(error.response?.data?.message || 'Failed to load customer details', 'error');
  } finally {
    loading.value = false;
  }
};

const showToast = (message, type = 'info') => {
  const toast = document.createElement('div');
  const bgColor = type === 'success' ? '#16a34a' : type === 'error' ? '#dc2626' : '#3b82f6';
  toast.style.cssText = `
    position: fixed;
    bottom: 20px;
    right: 20px;
    padding: 12px 24px;
    border-radius: 8px;
    color: white;
    font-weight: 500;
    z-index: 9999;
    animation: slideIn 0.3s ease;
    background: ${bgColor};
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
  `;
  toast.textContent = message;
  document.body.appendChild(toast);
  
  setTimeout(() => {
    toast.style.opacity = '0';
    toast.style.transform = 'translateY(20px)';
    toast.style.transition = 'all 0.3s ease';
    setTimeout(() => {
      document.body.removeChild(toast);
    }, 300);
  }, 3000);
};

onMounted(() => {
  checkDarkMode();
  setupDarkModeWatcher();
  fetchCustomer();

  // Listen for storage changes from other tabs
  window.addEventListener('storage', (e) => {
    if (e.key === 'theme') {
      isDarkMode.value = e.newValue === 'dark';
    }
  });
});

onUnmounted(() => {
  if (darkModeObserver) {
    darkModeObserver.disconnect();
  }
});
</script>

<style scoped>
/* Custom scrollbar for dark mode */
.dark ::-webkit-scrollbar {
  width: 8px;
  height: 8px;
}

.dark ::-webkit-scrollbar-track {
  background: #1f2937;
}

.dark ::-webkit-scrollbar-thumb {
  background: #4b5563;
  border-radius: 4px;
}

.dark ::-webkit-scrollbar-thumb:hover {
  background: #6b7280;
}

@keyframes slideIn {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>