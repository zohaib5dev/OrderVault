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
      <span :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">Customers</span>
    </nav>

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
      <div>
        <h1 class="text-2xl md:text-3xl font-bold" :class="isDarkMode ? 'text-white' : 'text-gray-900'">Customers</h1>
        <p class="text-sm" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Manage and track all your customers</p>
      </div>
      <router-link
        to="/admin/customers/create"
        class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 from-primary-500 to-primary-600 text-white rounded-lg hover:from-primary-600 hover:to-primary-700 transition-all duration-200 shadow-md"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Add Customer
      </router-link>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
      <div :class="[
        'rounded-2xl shadow-lg p-5 border-l-4 border-blue-500',
        isDarkMode ? 'bg-gray-800' : 'bg-white'
      ]">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-xs uppercase tracking-wider" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Total Customers</p>
            <p class="text-2xl font-bold" :class="isDarkMode ? 'text-white' : 'text-gray-900'">{{ stats.total }}</p>
          </div>
          <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center">
            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
          </div>
        </div>
      </div>

      <div :class="[
        'rounded-2xl shadow-lg p-5 border-l-4 border-green-500',
        isDarkMode ? 'bg-gray-800' : 'bg-white'
      ]">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-xs uppercase tracking-wider" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Active Customers</p>
            <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ stats.active }}</p>
          </div>
          <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center">
            <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
        </div>
      </div>

      <div :class="[
        'rounded-2xl shadow-lg p-5 border-l-4 border-gray-500',
        isDarkMode ? 'bg-gray-800' : 'bg-white'
      ]">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-xs uppercase tracking-wider" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Inactive Customers</p>
            <p class="text-2xl font-bold" :class="isDarkMode ? 'text-gray-300' : 'text-gray-600'">{{ stats.inactive }}</p>
          </div>
          <div class="w-10 h-10 bg-gray-100 dark:bg-gray-700 rounded-xl flex items-center justify-center">
            <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
            </svg>
          </div>
        </div>
      </div>

 
    </div>

    <!-- Filters and Search -->
    <div :class="[
      'rounded-2xl shadow-lg',
      isDarkMode ? 'bg-gray-800' : 'bg-white'
    ]">
      <div class="p-4 flex flex-col sm:flex-row gap-4">
        <div class="flex-1">
          <div class="relative">
            <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4" :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <input
              type="text"
              v-model="searchTerm"
              placeholder="Search by name, email, or phone..."
              :class="[
                'w-full pl-10 pr-4 py-2.5 border rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors',
                isDarkMode ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-400' : 'bg-white border-gray-200 text-gray-900 placeholder-gray-500'
              ]"
            />
          </div>
        </div>
        <div class="flex gap-3 flex-wrap">
          <select
            v-model="statusFilter"
            @change="resetPage"
            :class="[
              'px-4 py-2.5 border rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors',
              isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-200 text-gray-900'
            ]"
          >
            <option value="">All Status</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
          </select>
          <select
            v-model="sortBy"
            @change="resetPage"
            :class="[
              'px-4 py-2.5 border rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors',
              isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-200 text-gray-900'
            ]"
          >
            <option value="name">Sort by Name</option>
            <option value="newest">Sort by Newest</option>
            <option value="oldest">Sort by Oldest</option>
            <option value="orders">Sort by Orders</option>
          </select>
          <select
            v-model="perPage"
            @change="resetPage"
            :class="[
              'px-4 py-2.5 border rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors',
              isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-200 text-gray-900'
            ]"
          >
            <option value="10">10</option>
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="100">100</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Customers Table -->
    <div :class="[
      'rounded-2xl shadow-lg overflow-hidden',
      isDarkMode ? 'bg-gray-800' : 'bg-white'
    ]">
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead :class="isDarkMode ? 'bg-gray-700/50' : 'bg-gray-50'">
            <tr>
      
              <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">
                Customer
              </th>
              <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider hidden sm:table-cell" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">
                Email
              </th>
              <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider hidden md:table-cell" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">
                Phone
              </th>
              <th class="px-4 py-3 text-center text-xs font-medium uppercase tracking-wider hidden md:table-cell" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">
                Orders
              </th>
              <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider hidden lg:table-cell" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">
                Total Spent
              </th>
              <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">
                Status
              </th>
              <th class="px-4 py-3 text-center text-xs font-medium uppercase tracking-wider" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">
                Actions
              </th>
            </tr>
          </thead>
          <tbody class="divide-y" :class="isDarkMode ? 'divide-gray-700' : 'divide-gray-200'">
            <tr v-if="loading" class="text-center">
              <td colspan="8" class="px-4 py-12">
                <div class="flex justify-center items-center">
                  <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                  <span class="ml-2" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Loading customers...</span>
                </div>
              </td>
            </tr>
            <tr v-else-if="customers.length === 0 && !loading" class="text-center">
              <td colspan="8" class="px-4 py-12">
                <svg class="w-16 h-16 mx-auto mb-4" :class="isDarkMode ? 'text-gray-600' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <p class="text-lg font-medium" :class="isDarkMode ? 'text-gray-300' : 'text-gray-600'">No customers found</p>
                <p class="text-sm mt-1" :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'">
                  Try adjusting your search or filter criteria
                </p>
              </td>
            </tr>
            <tr
              v-for="customer in customers"
              :key="customer.id"
              class="transition-colors"
              :class="isDarkMode ? 'hover:bg-gray-700/50' : 'hover:bg-gray-50'"
            >
            
              <td class="px-4 py-3">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center flex-shrink-0 text-white font-semibold text-sm">
                    {{ getInitials(customer.name) }}
                  </div>
                  <div>
                    <router-link
                      :to="`/admin/customers/${customer.id}`"
                      class="text-sm font-medium hover:underline"
                      :class="isDarkMode ? 'text-white hover:text-blue-400' : 'text-gray-900 hover:text-blue-600'"
                    >
                      {{ customer.name }}
                    </router-link>
                    <p class="text-xs sm:hidden" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">{{ customer.email }}</p>
                  </div>
                </div>
              </td>
              <td class="px-4 py-3 hidden sm:table-cell">
                <span class="text-sm" :class="isDarkMode ? 'text-gray-300' : 'text-gray-600'">{{ customer.email }}</span>
              </td>
              <td class="px-4 py-3 hidden md:table-cell">
                <span class="text-sm" :class="isDarkMode ? 'text-gray-300' : 'text-gray-600'">{{ customer.phone || 'N/A' }}</span>
              </td>
              <td class="px-4 py-3 text-center hidden md:table-cell">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">
                  {{ customer.orders_count || 0 }}
                </span>
              </td>
              <td class="px-4 py-3 text-right hidden lg:table-cell">
                <span class="text-sm font-medium" :class="isDarkMode ? 'text-white' : 'text-gray-900'">
                  {{ formatCurrency(customer.total_spent || 0) }}
                </span>
              </td>
              <td class="px-4 py-3">
                <span
                  @click="toggleStatus(customer)"
                  class="inline-flex px-2 py-1 text-xs font-semibold rounded-full cursor-pointer hover:opacity-80 transition-opacity"
                  :class="customer.status === 'active' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'"
                >
                  {{ customer.status || 'inactive' }}
                </span>
              </td>
              <td class="px-4 py-3">
                <div class="flex items-center justify-center gap-1">
                  <router-link
                    :to="`/admin/customers/${customer.id}`"
                    class="p-1.5 rounded-lg transition-colors"
                    :class="isDarkMode ? 'text-blue-400 hover:bg-blue-900/20' : 'text-blue-600 hover:bg-blue-50'"
                    title="View Customer"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                  </router-link>
                  <router-link
                    :to="`/admin/customers/${customer.id}/edit`"
                    class="p-1.5 rounded-lg transition-colors"
                    :class="isDarkMode ? 'text-indigo-400 hover:bg-indigo-900/20' : 'text-indigo-600 hover:bg-indigo-50'"
                    title="Edit Customer"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                  </router-link>
                  <button
                    @click="deleteCustomer(customer)"
                    class="p-1.5 rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                    :class="isDarkMode ? 'text-red-400 hover:bg-red-900/20' : 'text-red-600 hover:bg-red-50'"
                    title="Delete Customer"
                    :disabled="customer.orders_count > 0"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Bulk Actions -->
      <div v-if="selectedIds.length > 0" class="px-4 py-3 border-t flex flex-wrap items-center justify-between gap-2" :class="isDarkMode ? 'border-gray-700 bg-yellow-900/20' : 'border-gray-200 bg-yellow-50'">
        <span class="text-sm" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">
          {{ selectedIds.length }} customer(s) selected
        </span>
        <button
          @click="bulkDelete"
          class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors text-sm flex items-center gap-2"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
          </svg>
          Delete Selected
        </button>
      </div>

      <!-- Pagination -->
      <div class="px-4 py-3 border-t flex flex-col sm:flex-row justify-between items-center gap-4" :class="isDarkMode ? 'border-gray-700 bg-gray-800/50' : 'border-gray-200 bg-gray-50'">
        <div class="text-sm" :class="isDarkMode ? 'text-gray-400' : 'text-gray-700'">
          Showing <span class="font-medium" :class="isDarkMode ? 'text-white' : 'text-gray-900'">{{ pagination.from || 0 }}</span> to
          <span class="font-medium" :class="isDarkMode ? 'text-white' : 'text-gray-900'">{{ pagination.to || 0 }}</span> of
          <span class="font-medium" :class="isDarkMode ? 'text-white' : 'text-gray-900'">{{ pagination.total || 0 }}</span> results
        </div>
        <div class="flex items-center gap-2">
          <button
            @click="previousPage"
            :disabled="pagination.current_page <= 1"
            :class="[
              'px-3 py-1 border rounded-md text-sm transition-colors disabled:opacity-50 disabled:cursor-not-allowed',
              isDarkMode ? 'border-gray-600 text-gray-300 hover:bg-gray-700' : 'border-gray-300 text-gray-700 hover:bg-white'
            ]"
          >
            Previous
          </button>
          <div class="flex items-center gap-1">
            <span class="text-sm" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">{{ pagination.current_page || 1 }}</span>
            <span class="text-sm" :class="isDarkMode ? 'text-gray-500' : 'text-gray-500'">of</span>
            <span class="text-sm" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">{{ pagination.last_page || 1 }}</span>
          </div>
          <button
            @click="nextPage"
            :disabled="pagination.current_page >= pagination.last_page"
            :class="[
              'px-3 py-1 border rounded-md text-sm transition-colors disabled:opacity-50 disabled:cursor-not-allowed',
              isDarkMode ? 'border-gray-600 text-gray-300 hover:bg-gray-700' : 'border-gray-300 text-gray-700 hover:bg-white'
            ]"
          >
            Next
          </button>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div
      v-if="showDeleteModal"
      class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
      @click.self="closeDeleteModal"
    >
      <div :class="[
        'rounded-2xl shadow-2xl max-w-md w-full p-6',
        isDarkMode ? 'bg-gray-800' : 'bg-white'
      ]">
        <div class="flex items-center gap-3 mb-4">
          <div class="p-2 bg-red-100 dark:bg-red-900/30 rounded-full">
            <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
          </div>
          <div>
            <h3 class="text-lg font-semibold" :class="isDarkMode ? 'text-white' : 'text-gray-900'">Delete Customer</h3>
            <p class="text-sm" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">This action cannot be undone.</p>
          </div>
        </div>

        <p class="mb-4" :class="isDarkMode ? 'text-gray-300' : 'text-gray-600'">
          Are you sure you want to delete <strong :class="isDarkMode ? 'text-white' : 'text-gray-900'">{{ deleteCustomerName }}</strong>?
          {{ deleteCustomerOrders > 0 ? `This customer has ${deleteCustomerOrders} order(s).` : '' }}
        </p>

        <div class="flex justify-end gap-3">
          <button
            @click="closeDeleteModal"
            :class="[
              'px-4 py-2 rounded-lg transition-colors',
              isDarkMode ? 'bg-gray-700 text-gray-300 hover:bg-gray-600' : 'bg-gray-200 text-gray-800 hover:bg-gray-300'
            ]"
          >
            Cancel
          </button>
          <button
            @click="confirmDelete"
            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
            :disabled="deleteCustomerOrders > 0"
          >
            Delete Customer
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

export default {
  setup() {
    const router = useRouter();

    // Data
    const customers = ref([]);
    const loading = ref(false);
    const searchTerm = ref('');
    const statusFilter = ref('');
    const sortBy = ref('name');
    const perPage = ref(10);
    const selectedIds = ref([]);
    const selectAll = ref(false);
    const isDarkMode = ref(false);
    let darkModeObserver = null;

    // Pagination
    const pagination = ref({
      current_page: 1,
      last_page: 1,
      from: 0,
      to: 0,
      total: 0,
    });

    // Stats
    const stats = ref({
      total: 0,
      active: 0,
      inactive: 0,
      with_orders: 0,
    });

    // Delete Modal
    const showDeleteModal = ref(false);
    const deleteCustomerId = ref(null);
    const deleteCustomerName = ref('');
    const deleteCustomerOrders = ref(0);

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

    // Methods
    const fetchCustomers = async () => {
      loading.value = true;
      try {
        const response = await axios.get('/api/admin/customers', {
          params: {
            search: searchTerm.value,
            status: statusFilter.value,
            sort: sortBy.value,
            page: pagination.value.current_page,
            per_page: perPage.value,
          },
        });

        const data = response.data.data || response.data;
        
        if (data.data) {
          customers.value = data.data;
          pagination.value = {
            current_page: data.current_page || 1,
            last_page: data.last_page || 1,
            from: data.from || 0,
            to: data.to || 0,
            total: data.total || 0,
          };
        } else {
          customers.value = data;
        }

          stats.value = {
            total: response.data.stats.total_customers,
            active: response.data.stats.active_customers,
            inactive: response.data.stats.inactive_customers,
          };

        selectedIds.value = [];
        selectAll.value = false;

      } catch (error) {
        console.error('Error fetching customers:', error);
      } finally {
        loading.value = false;
      }
    };

    const toggleStatus = async (customer) => {
      try {
        const newStatus = customer.status === 'active' ? false : true;
        await axios.put(`/api/admin/customers/${customer.id}/status`, {
          is_active: newStatus
        });
        
        customer.status = newStatus ? 'active' : 'inactive';
      } catch (error) {
        console.error('Error toggling status:', error);
        customer.status = customer.status === 'active' ? 'inactive' : 'active';
      }
    };

    const deleteCustomer = (customer) => {
      deleteCustomerId.value = customer.id;
      deleteCustomerName.value = customer.name;
      deleteCustomerOrders.value = customer.orders_count || 0;
      showDeleteModal.value = true;
    };

    const confirmDelete = async () => {
      try {
        await axios.delete(`/api/admin/customers/${deleteCustomerId.value}`);
        closeDeleteModal();
        await fetchCustomers();
      } catch (error) {
        console.error('Error deleting customer:', error);
      }
    };

    const closeDeleteModal = () => {
      showDeleteModal.value = false;
      deleteCustomerId.value = null;
      deleteCustomerName.value = '';
      deleteCustomerOrders.value = 0;
    };

    const bulkDelete = async () => {
      if (selectedIds.value.length === 0) return;
      
      if (confirm(`Are you sure you want to delete ${selectedIds.value.length} customers?`)) {
        try {
          await axios.post('/api/admin/customers/bulk-delete', {
            ids: selectedIds.value
          });
          
          selectedIds.value = [];
          selectAll.value = false;
          await fetchCustomers();
        } catch (error) {
          console.error('Error bulk deleting:', error);
        }
      }
    };

    const toggleSelectAll = () => {
      if (selectAll.value) {
        selectedIds.value = customers.value.map(c => c.id);
      } else {
        selectedIds.value = [];
      }
    };

    const getInitials = (name) => {
      if (!name) return '?';
      return name
        .split(' ')
        .map(n => n[0])
        .join('')
        .toUpperCase()
        .slice(0, 2);
    };

    const formatCurrency = (amount) => {
      if (!amount) return '$0.00';
      return `$${parseFloat(amount).toFixed(2)}`;
    };

    const previousPage = () => {
      if (pagination.value.current_page > 1) {
        pagination.value.current_page--;
        fetchCustomers();
      }
    };

    const nextPage = () => {
      if (pagination.value.current_page < pagination.value.last_page) {
        pagination.value.current_page++;
        fetchCustomers();
      }
    };

    const resetPage = () => {
      pagination.value.current_page = 1;
      fetchCustomers();
    };

    // Watchers
    watch([searchTerm, statusFilter, sortBy, perPage], () => {
      resetPage();
    });

    // Lifecycle
    onMounted(() => {
      checkDarkMode();
      setupDarkModeWatcher();
      fetchCustomers();

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

    return {
      customers,
      loading,
      searchTerm,
      statusFilter,
      sortBy,
      perPage,
      pagination,
      stats,
      selectedIds,
      selectAll,
      isDarkMode,
      showDeleteModal,
      deleteCustomerName,
      deleteCustomerOrders,
      fetchCustomers,
      toggleStatus,
      deleteCustomer,
      confirmDelete,
      closeDeleteModal,
      bulkDelete,
      toggleSelectAll,
      getInitials,
      formatCurrency,
      previousPage,
      nextPage,
      resetPage,
    };
  },
};
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

/* Modal animation */
.fixed {
  animation: fadeIn 0.2s ease-out;
}

@keyframes fadeIn {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

.bg-white.rounded-lg, .bg-gray-800.rounded-2xl {
  animation: slideIn 0.3s ease-out;
}

@keyframes slideIn {
  from {
    transform: translateY(-20px);
    opacity: 0;
  }
  to {
    transform: translateY(0);
    opacity: 1;
  }
}

/* Status badge hover effect */
.cursor-pointer {
  cursor: pointer;
}

 

/* Disabled button styles */
button:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

/* Remove number input arrows */
input[type="number"]::-webkit-inner-spin-button,
input[type="number"]::-webkit-outer-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

input[type="number"] {
  -moz-appearance: textfield;
}
</style>