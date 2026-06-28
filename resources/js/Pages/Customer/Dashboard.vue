<template>
  <div class="space-y-6 pb-8">
  

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
      <div>
        <h1 class="text-2xl md:text-3xl font-bold" :class="isDarkMode ? 'text-white' : 'text-gray-900'">
         Welcome back, {{ authStore.user.name }}
        </h1>
        <p class="text-sm" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">
          Welcome back, {{ customerName }}! Here's your order overview.
        </p>
      </div>
      <div class="flex gap-2">
        <button @click="refreshData" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-200 shadow-md text-sm">
          <svg class="w-4 h-4" :class="{ 'animate-spin': loading }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
          </svg>
          Refresh
        </button>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" :class="[
      'rounded-2xl shadow-lg p-12 text-center',
      isDarkMode ? 'bg-gray-800' : 'bg-white'
    ]">
      <div class="flex flex-col items-center justify-center">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
        <p class="mt-4" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Loading dashboard data...</p>
      </div>
    </div>

    <div v-else>
      <!-- Stats Cards -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Orders -->
        <div :class="[
          'rounded-2xl shadow-lg p-5 border-l-4 border-blue-500',
          isDarkMode ? 'bg-gray-800' : 'bg-white'
        ]">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Total Orders</p>
              <p class="text-2xl font-bold mt-1" :class="isDarkMode ? 'text-white' : 'text-gray-900'">{{ stats.total_orders }}</p>
              <p class="text-xs mt-1 flex items-center text-green-600 dark:text-green-400">
                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd"/>
                </svg>
                {{ stats.orders_growth || 0 }}% from last month
              </p>
            </div>
            <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center">
              <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
              </svg>
            </div>
          </div>
        </div>

        <!-- Pending Orders -->
        <div :class="[
          'rounded-2xl shadow-lg p-5 border-l-4 border-yellow-500',
          isDarkMode ? 'bg-gray-800' : 'bg-white'
        ]">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Pending Orders</p>
              <p class="text-2xl font-bold mt-1" :class="isDarkMode ? 'text-white' : 'text-gray-900'">{{ stats.pending_orders }}</p>
              <p class="text-xs mt-1 flex items-center text-yellow-600 dark:text-yellow-400">
                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd"/>
                </svg>
                {{ stats.pending_growth || 0 }}% from last month
              </p>
            </div>
            <div class="w-10 h-10 bg-yellow-100 dark:bg-yellow-900/30 rounded-xl flex items-center justify-center">
              <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
          </div>
        </div>

        <!-- Completed Orders -->
        <div :class="[
          'rounded-2xl shadow-lg p-5 border-l-4 border-green-500',
          isDarkMode ? 'bg-gray-800' : 'bg-white'
        ]">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Completed Orders</p>
              <p class="text-2xl font-bold mt-1" :class="isDarkMode ? 'text-white' : 'text-gray-900'">{{ stats.completed_orders }}</p>
              <p class="text-xs mt-1 flex items-center text-green-600 dark:text-green-400">
                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd"/>
                </svg>
                {{ stats.completed_growth || 0 }}% from last month
              </p>
            </div>
            <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center">
              <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
          </div>
        </div>

        <!-- Total Spent -->
        <div :class="[
          'rounded-2xl shadow-lg p-5 border-l-4 border-purple-500',
          isDarkMode ? 'bg-gray-800' : 'bg-white'
        ]">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Total Spent</p>
              <p class="text-2xl font-bold mt-1" :class="isDarkMode ? 'text-white' : 'text-gray-900'">${{ formatNumber(stats.total_spent) }}</p>
              <p class="text-xs mt-1 flex items-center text-green-600 dark:text-green-400">
                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd"/>
                </svg>
                {{ stats.spent_growth || 0 }}% from last month
              </p>
            </div>
            <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center">
              <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v1m0 1v1m0 1v1m0 1v1m0 1v1"/>
              </svg>
            </div>
          </div>
        </div>
      </div>

   

      <!-- Filters Section -->
      <div :class="[
        'rounded-2xl shadow-lg p-4 mt-3 sm:p-6 mb-3',
        isDarkMode ? 'bg-gray-800' : 'bg-white'
      ]">
        <div class="flex flex-col sm:flex-row gap-3 sm:gap-4">
          <!-- Search -->
          <div class="flex-1">
            <div class="relative">
              <input
                v-model="filters.search"
                type="text"
                placeholder="Search orders..."
                :class="[
                  'w-full pl-10 pr-4 py-2.5 rounded-lg border transition-colors text-sm',
                  isDarkMode 
                    ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-400 focus:border-blue-500' 
                    : 'bg-white border-gray-300 text-gray-900 placeholder-gray-400 focus:border-blue-500'
                ]"
              />
              <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
              </svg>
            </div>
          </div>

          <!-- Status Filter -->
          <div class="w-full sm:w-48">
            <select
              v-model="filters.status"
              :class="[
                'w-full px-4 py-2.5 rounded-lg border transition-colors text-sm appearance-none',
                isDarkMode 
                  ? 'bg-gray-700 border-gray-600 text-white focus:border-blue-500' 
                  : 'bg-white border-gray-300 text-gray-900 focus:border-blue-500'
              ]"
            >
              <option value="">All Status</option>
              <option v-for="status in statusOptions" :key="status.value" :value="status.value">
                {{ status.label }}
              </option>
            </select>
          </div>

          <!-- Payment Status Filter -->
          <div class="w-full sm:w-48">
            <select
              v-model="filters.payment_status"
              :class="[
                'w-full px-4 py-2.5 rounded-lg border transition-colors text-sm appearance-none',
                isDarkMode 
                  ? 'bg-gray-700 border-gray-600 text-white focus:border-blue-500' 
                  : 'bg-white border-gray-300 text-gray-900 focus:border-blue-500'
              ]"
            >
              <option value="">All Payments</option>
              <option v-for="status in paymentStatusOptions" :key="status.value" :value="status.value">
                {{ status.label }}
              </option>
            </select>
          </div>

          <!-- Clear Filters -->
          <button
            v-if="hasActiveFilters"
            @click="clearFilters"
            :class="[
              'px-4 py-2.5 rounded-lg text-sm transition-colors whitespace-nowrap',
              isDarkMode ? 'bg-gray-700 text-gray-300 hover:bg-gray-600' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
            ]"
          >
            Clear Filters
          </button>
        </div>
      </div>

      <!-- Orders Table -->
      <div :class="[
        'rounded-2xl shadow-lg overflow-hidden',
        isDarkMode ? 'bg-gray-800' : 'bg-white'
      ]">
        <div class="overflow-x-auto">
          <table class="w-full min-w-[700px]">
            <thead>
              <tr :class="[
                'text-left text-xs uppercase border-b',
                isDarkMode ? 'text-gray-400 border-gray-700 bg-gray-700/30' : 'text-gray-500 border-gray-100 bg-gray-50'
              ]">
                <th class="px-4 sm:px-6 py-3 font-semibold">Order #</th>
                <th class="px-4 sm:px-6 py-3 font-semibold hidden sm:table-cell">Date</th>
                <th class="px-4 sm:px-6 py-3 font-semibold text-right">Total</th>
                <th class="px-4 sm:px-6 py-3 font-semibold hidden md:table-cell">Vendor</th>
                <th class="px-4 sm:px-6 py-3 font-semibold">Status</th>
                <th class="px-4 sm:px-6 py-3 font-semibold hidden lg:table-cell">Payment</th>
                <th class="px-4 sm:px-6 py-3 font-semibold text-right">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="filteredOrders.length === 0" class="text-center">
                <td colspan="7" class="px-4 sm:px-6 py-8 sm:py-12" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">
                  <svg class="w-12 h-12 sm:w-16 sm:h-16 mx-auto mb-3" :class="isDarkMode ? 'text-gray-600' : 'text-gray-300'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                  </svg>
                  <p class="text-sm sm:text-base font-medium">No orders found</p>
                  <p class="text-xs sm:text-sm mt-1">Try adjusting your filters</p>
                </td>
              </tr>
              <tr 
                v-for="order in paginatedOrders" 
                :key="order.id" 
                :class="[
                  'border-b transition-colors',
                  isDarkMode ? 'border-gray-700 hover:bg-gray-700/30' : 'border-gray-100 hover:bg-gray-50'
                ]"
              >
                <td class="px-4 sm:px-6 py-3">
                  <span class="text-sm font-medium" :class="isDarkMode ? 'text-white' : 'text-gray-900'">
                    #{{ order.order_number || order.id }}
                  </span>
                </td>
                <td class="px-4 sm:px-6 py-3 hidden sm:table-cell">
                  <span :class="isDarkMode ? 'text-gray-300' : 'text-gray-600'" class="text-sm">
                    {{ formatDate(order.created_at) }}
                  </span>
                </td>
                <td class="px-4 sm:px-6 py-3 text-right">
                  <span class="text-sm font-semibold" :class="isDarkMode ? 'text-white' : 'text-gray-900'">
                    ${{ formatNumber(order.total_amount) }}
                  </span>
                </td>
                <td class="px-4 sm:px-6 py-3 hidden md:table-cell">
                  <span :class="isDarkMode ? 'text-gray-300' : 'text-gray-600'" class="text-sm">
                    {{ order.vendor_name || 'N/A' }}
                  </span>
                </td>
                <td class="px-4 sm:px-6 py-3">
                  <span class="text-xs px-2 py-1 rounded-full font-medium whitespace-nowrap" :class="getStatusClass(order.status)">
                    {{ formatStatus(order.status) }}
                  </span>
                </td>
                <td class="px-4 sm:px-6 py-3 hidden lg:table-cell">
                  <span class="text-xs px-2 py-1 rounded-full font-medium whitespace-nowrap" :class="getPaymentStatusClass(order.payment_status)">
                    {{ formatStatus(order.payment_status) }}
                  </span>
                </td>
                <td class="px-4 sm:px-6 py-3 text-right">
                  <div class="flex items-center justify-end gap-1.5 sm:gap-2">
                    <!-- View Order Button -->
                    <router-link 
                      :to="`/customer/orders/${order.id}`"
                      class="p-1.5 sm:p-2 rounded-lg transition-colors"
                      :class="isDarkMode ? 'hover:bg-gray-700 text-gray-400 hover:text-white' : 'hover:bg-gray-100 text-gray-500 hover:text-gray-700'"
                      title="View Order"
                    >
                      <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                      </svg>
                    </router-link>

                    <!-- Payment Button (if pending) -->
                    <router-link 
                      v-if="order.payment_status === 'pending' || order.payment_status === 'unpaid'"
                      :to="`/customer/orders/${order.id}/payment`"
                      class="p-1.5 sm:p-2 rounded-lg transition-colors"
                      :class="isDarkMode ? 'hover:bg-gray-700 text-green-400 hover:text-green-300' : 'hover:bg-gray-100 text-green-600 hover:text-green-700'"
                       title="Make Payment"
                    >
                     <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                      </svg>
                    </router-link>

                    <!-- Invoice Button -->
                    <button
                      @click="downloadInvoice(order.id)"
                      class="p-1.5 sm:p-2 rounded-lg transition-colors"
                      :class="isDarkMode ? 'hover:bg-gray-700 text-blue-400 hover:text-blue-300' : 'hover:bg-gray-100 text-blue-600 hover:text-blue-700'"
                      title="Download Invoice"
                    >
                      <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                      </svg>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div :class="[
          'px-4 sm:px-6 py-3 border-t flex flex-col sm:flex-row items-center justify-between gap-3',
          isDarkMode ? 'border-gray-700 bg-gray-700/30' : 'border-gray-100 bg-gray-50/50'
        ]">
          <span :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'" class="text-sm">
            Showing {{ startIndex + 1 }} - {{ Math.min(endIndex, filteredOrders.length) }} of {{ filteredOrders.length }} orders
          </span>
          <div class="flex items-center gap-2">
            <button
              @click="prevPage"
              :disabled="currentPage === 1"
              :class="[
                'px-3 py-1 rounded-lg text-sm transition-colors disabled:opacity-50 disabled:cursor-not-allowed',
                isDarkMode 
                  ? 'bg-gray-700 text-gray-300 hover:bg-gray-600 disabled:hover:bg-gray-700' 
                  : 'bg-gray-100 text-gray-700 hover:bg-gray-200 disabled:hover:bg-gray-100'
              ]"
            >
              Previous
            </button>
            <span :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'" class="text-sm px-3">
              Page {{ currentPage }} of {{ totalPages }}
            </span>
            <button
              @click="nextPage"
              :disabled="currentPage === totalPages"
              :class="[
                'px-3 py-1 rounded-lg text-sm transition-colors disabled:opacity-50 disabled:cursor-not-allowed',
                isDarkMode 
                  ? 'bg-gray-700 text-gray-300 hover:bg-gray-600 disabled:hover:bg-gray-700' 
                  : 'bg-gray-100 text-gray-700 hover:bg-gray-200 disabled:hover:bg-gray-100'
              ]"
            >
              Next
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';
import { useAuthStore } from '../../store';
import axios from 'axios';

const authStore = useAuthStore();
const loading = ref(false);
const isDarkMode = ref(false);
let darkModeObserver = null;

const orders = ref([]);
const stats = ref({
  total_orders: 0,
  pending_orders: 0,
  completed_orders: 0,
  total_spent: 0,
  orders_growth: 0,
  pending_growth: 0,
  completed_growth: 0,
  spent_growth: 0,
  total_vendors: 0,
  total_products: 0,
  active_subscriptions: 0,
  plan_purchases: 0
});

// Filters
const filters = ref({
  search: '',
  status: '',
  payment_status: ''
});

// Pagination
const currentPage = ref(1);
const perPage = ref(10);

// Status options
const statusOptions = [
  { value: 'pending', label: 'Pending' },
  { value: 'processing', label: 'Processing' },
  { value: 'shipped', label: 'Shipped' },
  { value: 'delivered', label: 'Delivered' },
  { value: 'completed', label: 'Completed' },
  { value: 'cancelled', label: 'Cancelled' }
];

const paymentStatusOptions = [
  { value: 'pending', label: 'Pending' },
  { value: 'paid', label: 'Paid' },
  { value: 'unpaid', label: 'Unpaid' },
  { value: 'failed', label: 'Failed' },
  { value: 'refunded', label: 'Refunded' }
];

const customerName = computed(() => {
  return authStore.user?.name || 'Customer';
});

const hasActiveFilters = computed(() => {
  return filters.value.search || filters.value.status || filters.value.payment_status;
});

const filteredOrders = computed(() => {
  let result = [...orders.value];

  if (filters.value.search) {
    const search = filters.value.search.toLowerCase();
    result = result.filter(order => 
      (order.order_number && order.order_number.toLowerCase().includes(search)) ||
      (order.vendor_name && order.vendor_name.toLowerCase().includes(search)) ||
      (order.id && String(order.id).includes(search))
    );
  }

  if (filters.value.status) {
    result = result.filter(order => order.status === filters.value.status);
  }

  if (filters.value.payment_status) {
    result = result.filter(order => order.payment_status === filters.value.payment_status);
  }

  result.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));

  return result;
});

const totalPages = computed(() => {
  return Math.ceil(filteredOrders.value.length / perPage.value) || 1;
});

const paginatedOrders = computed(() => {
  const start = (currentPage.value - 1) * perPage.value;
  const end = start + perPage.value;
  return filteredOrders.value.slice(start, end);
});

const startIndex = computed(() => {
  return (currentPage.value - 1) * perPage.value;
});

const endIndex = computed(() => {
  return startIndex.value + perPage.value;
});

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

const getStatusClass = (status) => {
  const classes = {
    pending: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
    processing: 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
    shipped: 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400',
    delivered: 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
    completed: 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
    cancelled: 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400'
  };
  return classes[status?.toLowerCase()] || 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
};

const getPaymentStatusClass = (status) => {
  const classes = {
    pending: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
    paid: 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
    unpaid: 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
    failed: 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
    refunded: 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'
  };
  return classes[status?.toLowerCase()] || 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
};

const formatStatus = (status) => {
  if (!status) return 'N/A';
  return status.charAt(0).toUpperCase() + status.slice(1).toLowerCase();
};

const formatNumber = (value) => {
  if (!value) return '0.00';
  return Number(value).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
};

const formatDate = (date) => {
  if (!date) return 'N/A';
  const d = new Date(date);
  return d.toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric'
  });
};

const clearFilters = () => {
  filters.value.search = '';
  filters.value.status = '';
  filters.value.payment_status = '';
  currentPage.value = 1;
};

const prevPage = () => {
  if (currentPage.value > 1) {
    currentPage.value--;
  }
};

const nextPage = () => {
  if (currentPage.value < totalPages.value) {
    currentPage.value++;
  }
};

const makePayment = (order) => {
  showToast(`Redirecting to payment for order #${order.order_number || order.id}`, 'info');
};

const downloadInvoice = async (id) => {
  try {
    const response = await axios.get(`/api/customer/orders/${id}/download`, {
      responseType: 'blob'
    });
    const url = window.URL.createObjectURL(new Blob([response.data]));
    const link = document.createElement('a');
    link.href = url;
    link.setAttribute('download', `invoice-${id}.pdf`);
    document.body.appendChild(link);
    link.click();
    link.remove();
    showToast('Invoice downloaded successfully', 'success');
  } catch (error) {
    console.error('Error downloading invoice:', error);
    showToast('Failed to download invoice', 'error');
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

const refreshData = () => {
  fetchDashboardData();
  showToast('Dashboard refreshed', 'success');
};

const fetchDashboardData = async () => {
  loading.value = true;
  try {
    const response = await axios.get('/api/customer/dashboard');
    if (response.data.success) {
      const data = response.data.data;
      orders.value = data.orders || [];
      stats.value = {
        ...stats.value,
        ...data.stats
      };
    }
  } catch (error) {
    console.error('Error fetching dashboard data:', error);
    showToast('Failed to load dashboard data', 'error');
  } finally {
    loading.value = false;
  }
};

// Watch for filter changes to reset page
watch([() => filters.value.search, () => filters.value.status, () => filters.value.payment_status], () => {
  currentPage.value = 1;
});

onMounted(() => {
  checkDarkMode();
  setupDarkModeWatcher();
  fetchDashboardData();

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

 