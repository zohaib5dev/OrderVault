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
      <span :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">Orders</span>
    </nav>

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
      <div>
        <h1 class="text-2xl md:text-3xl font-bold" :class="isDarkMode ? 'text-white' : 'text-gray-900'">Orders</h1>
        <p class="text-sm" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Manage and track all your orders</p>
      </div>
      <router-link
        to="/admin/orders/create"
        class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 from-primary-500 to-primary-600 text-white rounded-lg hover:from-primary-600 hover:to-primary-700 transition-all duration-200 shadow-md"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Create Order
      </router-link>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
      <div :class="[
        'rounded-2xl shadow-lg p-5 border-l-4 border-blue-500',
        isDarkMode ? 'bg-gray-800' : 'bg-white'
      ]">
        <p class="text-xs uppercase tracking-wider" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Total Orders</p>
        <p class="text-2xl font-bold" :class="isDarkMode ? 'text-white' : 'text-gray-900'">{{ stats.total }}</p>
      </div>
      <div :class="[
        'rounded-2xl shadow-lg p-5 border-l-4 border-yellow-500',
        isDarkMode ? 'bg-gray-800' : 'bg-white'
      ]">
        <p class="text-xs uppercase tracking-wider" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Pending</p>
        <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ stats.pending }}</p>
      </div>
      <div :class="[
        'rounded-2xl shadow-lg p-5 border-l-4 border-blue-500',
        isDarkMode ? 'bg-gray-800' : 'bg-white'
      ]">
        <p class="text-xs uppercase tracking-wider" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Processing</p>
        <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ stats.processing }}</p>
      </div>
      <div :class="[
        'rounded-2xl shadow-lg p-5 border-l-4 border-green-500',
        isDarkMode ? 'bg-gray-800' : 'bg-white'
      ]">
        <p class="text-xs uppercase tracking-wider" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Completed</p>
        <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ stats.completed }}</p>
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
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input
              type="text"
              v-model="searchTerm"
              placeholder="Search by order number, customer, or email..."
              :class="[
                'w-full pl-10 pr-4 py-2.5 border rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors',
                isDarkMode ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-400' : 'bg-white border-gray-200 text-gray-900 placeholder-gray-500'
              ]"
            />
          </div>
        </div>
        <div class="flex flex-wrap gap-3">
          <select
            v-model="statusFilter"
            @change="resetPage"
            :class="[
              'px-4 py-2.5 border rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors',
              isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-200 text-gray-900'
            ]"
          >
            <option value="">All Status</option>
            <option value="pending">Pending</option>
            <option value="processing">Processing</option>
            <option value="completed">Completed</option>
            <option value="cancelled">Cancelled</option>
          </select>
          <select
            v-model="paymentFilter"
            @change="resetPage"
            :class="[
              'px-4 py-2.5 border rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors',
              isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-200 text-gray-900'
            ]"
          >
            <option value="">All Payment</option>
            <option value="pending">Pending</option>
            <option value="paid">Paid</option>
            <option value="failed">Failed</option>
            <option value="refunded">Refunded</option>
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

    <!-- Orders Table / Cards -->
    <div :class="[
      'rounded-2xl shadow-lg overflow-hidden',
      isDarkMode ? 'bg-gray-800' : 'bg-white'
    ]">
      <!-- Desktop Table View -->
      <div class="hidden md:block overflow-x-auto">
        <table class="w-full">
          <thead :class="isDarkMode ? 'bg-gray-700/50' : 'bg-gray-50'">
            <tr>
              <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Order</th>
              <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Customer</th>
              <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Total</th>
              <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Status</th>
              <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider hidden lg:table-cell" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Payment</th>
              <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider hidden xl:table-cell" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Date</th>
              <th class="px-4 py-3 text-center text-xs font-medium uppercase tracking-wider" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y" :class="isDarkMode ? 'divide-gray-700' : 'divide-gray-200'">
            <tr v-if="loading" class="text-center">
              <td colspan="8" class="px-4 py-12">
                <div class="flex justify-center items-center">
                  <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                  <span class="ml-2" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Loading orders...</span>
                </div>
              </td>
            </tr>
            <tr v-else-if="orders.length === 0 && !loading" class="text-center">
              <td colspan="8" class="px-4 py-12">
                <svg class="w-16 h-16 mx-auto mb-4" :class="isDarkMode ? 'text-gray-600' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                </svg>
                <p class="text-lg font-medium" :class="isDarkMode ? 'text-gray-300' : 'text-gray-600'">No orders found</p>
                <p class="text-sm mt-1" :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'">Try adjusting your search or filter criteria</p>
              </td>
            </tr>
            <tr v-for="order in orders" :key="order.id" class="transition-colors" :class="isDarkMode ? 'hover:bg-gray-700/50' : 'hover:bg-gray-50'">
              <td class="px-4 py-3">
                <router-link :to="`/admin/orders/${order.id}`" class="text-sm font-medium hover:underline" :class="isDarkMode ? 'text-blue-400 hover:text-blue-300' : 'text-blue-600 hover:text-blue-800'">
                  {{ order.order_number }}
                </router-link>
              </td>
              <td class="px-4 py-3">
                <div class="flex items-center gap-2">
                  <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center flex-shrink-0">
                    <span class="text-xs font-semibold text-blue-600 dark:text-blue-400">{{ getInitials(order.customer.name) }}</span>
                  </div>
                  <span class="text-sm" :class="isDarkMode ? 'text-gray-300' : 'text-gray-900'">{{ order.customer.name || "N/A" }}</span>
                </div>
              </td>
              <td class="px-4 py-3 text-right">
                <span class="text-sm font-semibold" :class="isDarkMode ? 'text-white' : 'text-gray-900'">${{ parseFloat(order.total_amount).toFixed(2) }}</span>
              </td>
              <td class="px-4 py-3">
                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full cursor-pointer hover:opacity-80" :class="getStatusClass(order.status)" @click="openStatusModal(order)">
                  {{ formatStatus(order.status) }}
                </span>
              </td>
              <td class="px-4 py-3 hidden lg:table-cell">
                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full cursor-pointer hover:opacity-80" :class="getPaymentStatusClass(order.payment_status)" @click="openStatusModal(order)">
                  {{ formatStatus(order.payment_status) }}
                </span>
              </td>
              <td class="px-4 py-3 hidden xl:table-cell">
                <span class="text-sm" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">{{ formatDate(order.created_at) }}</span>
              </td>
              <td class="px-4 py-3">
                <div class="flex items-center justify-center gap-2">
                  <button @click="openStatusModal(order)" class="p-1.5 rounded-lg transition-colors" :class="isDarkMode ? 'text-blue-400 hover:bg-blue-900/20' : 'text-blue-600 hover:bg-blue-50'" title="Update Status">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                  </button>
                  <router-link :to="`/admin/orders/${order.id}`" class="p-1.5 rounded-lg transition-colors" :class="isDarkMode ? 'text-gray-400 hover:bg-gray-700' : 'text-gray-600 hover:bg-gray-50'" title="View Order">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                  </router-link>
                  <button @click="downloadOrder(order.id)" class="p-1.5 rounded-lg transition-colors" :class="isDarkMode ? 'text-green-400 hover:bg-green-900/20' : 'text-green-600 hover:bg-green-50'" title="Download Order Invoice">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                  </button>
                  <button @click="openDeleteModal(order)" class="p-1.5 rounded-lg transition-colors" :class="isDarkMode ? 'text-red-400 hover:bg-red-900/20' : 'text-red-600 hover:bg-red-50'" title="Delete Order">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Mobile Card View -->
      <div class="md:hidden divide-y" :class="isDarkMode ? 'divide-gray-700' : 'divide-gray-100'">
        <div v-if="loading" class="flex justify-center items-center py-12">
          <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
          <span class="ml-2" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Loading orders...</span>
        </div>
        <div v-else-if="orders.length === 0 && !loading" class="text-center py-12">
          <svg class="w-16 h-16 mx-auto mb-4" :class="isDarkMode ? 'text-gray-600' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
          </svg>
          <p class="text-lg font-medium" :class="isDarkMode ? 'text-gray-300' : 'text-gray-600'">No orders found</p>
          <p class="text-sm mt-1" :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'">Try adjusting your search or filter criteria</p>
        </div>
        <div v-for="order in orders" :key="order.id" class="p-4 space-y-3 transition-colors" :class="isDarkMode ? 'hover:bg-gray-700/50' : 'hover:bg-gray-50'">
          <!-- Order Header -->
          <div class="flex items-start justify-between">
            <div class="flex items-center gap-2">
              <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center flex-shrink-0">
                <span class="text-xs font-semibold text-blue-600 dark:text-blue-400">{{ getInitials(order.customer.name) }}</span>
              </div>
              <div>
                <router-link :to="`/admin/orders/${order.id}`" class="text-sm font-medium hover:underline" :class="isDarkMode ? 'text-blue-400 hover:text-blue-300' : 'text-blue-600 hover:text-blue-800'">
                  {{ order.order_number }}
                </router-link>
                <p class="text-xs" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">{{ order.customer.name || "N/A" }}</p>
              </div>
            </div>
            <span class="text-sm font-semibold" :class="isDarkMode ? 'text-white' : 'text-gray-900'">${{ parseFloat(order.total_amount).toFixed(2) }}</span>
          </div>

          <!-- Order Details -->
          <div class="grid grid-cols-2 gap-2 rounded-lg p-3" :class="isDarkMode ? 'bg-gray-700/50' : 'bg-gray-50'">
            <div>
              <p class="text-xs" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Status</p>
              <span class="inline-flex px-2 py-0.5 text-xs font-semibold rounded-full cursor-pointer hover:opacity-80" :class="getStatusClass(order.status)" @click="openStatusModal(order)">
                {{ formatStatus(order.status) }}
              </span>
            </div>
            <div>
              <p class="text-xs" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Payment</p>
              <span class="inline-flex px-2 py-0.5 text-xs font-semibold rounded-full cursor-pointer hover:opacity-80" :class="getPaymentStatusClass(order.payment_status)" @click="openStatusModal(order)">
                {{ formatStatus(order.payment_status) }}
              </span>
            </div>
            <div class="col-span-2">
              <p class="text-xs" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Date</p>
              <p class="text-xs" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">{{ formatDate(order.created_at) }}</p>
            </div>
          </div>

          <!-- Actions -->
          <div class="flex items-center justify-end gap-3 pt-2 border-t" :class="isDarkMode ? 'border-gray-700' : 'border-gray-100'">
            <button @click="openStatusModal(order)" class="p-1.5 rounded-lg transition-colors" :class="isDarkMode ? 'text-blue-400 hover:bg-blue-900/20' : 'text-blue-600 hover:bg-blue-50'" title="Update Status">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
              </svg>
            </button>
            <router-link :to="`/admin/orders/${order.id}`" class="p-1.5 rounded-lg transition-colors" :class="isDarkMode ? 'text-gray-400 hover:bg-gray-700' : 'text-gray-600 hover:bg-gray-50'" title="View Order">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
              </svg>
            </router-link>
            <button @click="downloadOrder(order.id)" class="p-1.5 rounded-lg transition-colors" :class="isDarkMode ? 'text-green-400 hover:bg-green-900/20' : 'text-green-600 hover:bg-green-50'" title="Download Order Invoice">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
              </svg>
            </button>
            <button @click="openDeleteModal(order)" class="p-1.5 rounded-lg transition-colors" :class="isDarkMode ? 'text-red-400 hover:bg-red-900/20' : 'text-red-600 hover:bg-red-50'" title="Delete Order">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
              </svg>
            </button>
          </div>
        </div>
      </div>

      <!-- Pagination -->
      <div class="px-4 py-3 border-t flex flex-col sm:flex-row justify-between items-center gap-4" :class="isDarkMode ? 'border-gray-700 bg-gray-800/50' : 'border-gray-200 bg-gray-50'">
        <div class="text-sm" :class="isDarkMode ? 'text-gray-400' : 'text-gray-700'">
          Showing <span class="font-medium" :class="isDarkMode ? 'text-white' : 'text-gray-900'">{{ pagination.from || 0 }}</span> to
          <span class="font-medium" :class="isDarkMode ? 'text-white' : 'text-gray-900'">{{ pagination.to || 0 }}</span> of
          <span class="font-medium" :class="isDarkMode ? 'text-white' : 'text-gray-900'">{{ pagination.total || 0 }}</span> results
        </div>
        <div class="flex items-center gap-2">
          <button @click="previousPage" :disabled="pagination.current_page <= 1" :class="[
            'px-3 py-1 border rounded-md text-sm transition-colors disabled:opacity-50 disabled:cursor-not-allowed',
            isDarkMode ? 'border-gray-600 text-gray-300 hover:bg-gray-700' : 'border-gray-300 text-gray-700 hover:bg-white'
          ]">
            Previous
          </button>
          <div class="flex items-center gap-1">
            <span class="text-sm" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">{{ pagination.current_page || 1 }}</span>
            <span class="text-sm" :class="isDarkMode ? 'text-gray-500' : 'text-gray-500'">of</span>
            <span class="text-sm" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">{{ pagination.last_page || 1 }}</span>
          </div>
          <button @click="nextPage" :disabled="pagination.current_page >= pagination.last_page" :class="[
            'px-3 py-1 border rounded-md text-sm transition-colors disabled:opacity-50 disabled:cursor-not-allowed',
            isDarkMode ? 'border-gray-600 text-gray-300 hover:bg-gray-700' : 'border-gray-300 text-gray-700 hover:bg-white'
          ]">
            Next
          </button>
        </div>
      </div>
    </div>

    <!-- Status Update Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" @click.self="closeModal">
      <div :class="[
        'rounded-2xl shadow-2xl max-w-md w-full p-6',
        isDarkMode ? 'bg-gray-800' : 'bg-white'
      ]">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-semibold" :class="isDarkMode ? 'text-white' : 'text-gray-900'">Update Order Status</h3>
          <button @click="closeModal" class="transition" :class="isDarkMode ? 'text-gray-400 hover:text-gray-300' : 'text-gray-400 hover:text-gray-600'">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>

        <div v-if="selectedOrder" class="space-y-4">
          <!-- Order Info -->
          <div class="p-3 rounded-lg" :class="isDarkMode ? 'bg-gray-700' : 'bg-gray-50'">
            <p class="text-sm" :class="isDarkMode ? 'text-gray-300' : 'text-gray-600'">
              <span class="font-medium" :class="isDarkMode ? 'text-white' : 'text-gray-900'">Order:</span> {{ selectedOrder.order_number }}
            </p>
            <p class="text-sm" :class="isDarkMode ? 'text-gray-300' : 'text-gray-600'">
              <span class="font-medium" :class="isDarkMode ? 'text-white' : 'text-gray-900'">Customer:</span> {{ selectedOrder.customer_name }}
            </p>
            <p class="text-sm" :class="isDarkMode ? 'text-gray-300' : 'text-gray-600'">
              <span class="font-medium" :class="isDarkMode ? 'text-white' : 'text-gray-900'">Total:</span> ${{ parseFloat(selectedOrder.total_amount).toFixed(2) }}
            </p>
          </div>

          <!-- Status Update Fields -->
          <div>
            <label class="block text-sm font-medium mb-1" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">Order Status</label>
            <select v-model="statusForm.status" :class="[
              'w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors',
              isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'
            ]">
              <option value="pending">Pending</option>
              <option value="processing">Processing</option>
              <option value="shipped">Shipped</option>
              <option value="delivered">Delivered</option>
              <option value="completed">Completed</option>
              <option value="cancelled">Cancelled</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium mb-1" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">Payment Status</label>
            <select v-model="statusForm.payment_status" :class="[
              'w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors',
              isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'
            ]">
              <option value="pending">Pending</option>
              <option value="paid">Paid</option>
              <option value="failed">Failed</option>
              <option value="refunded">Refunded</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium mb-1" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">Notes (Optional)</label>
            <textarea v-model="statusForm.notes" :class="[
              'w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors',
              isDarkMode ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-400' : 'bg-white border-gray-300 text-gray-900'
            ]" rows="2" placeholder="Add a note about this status update..."></textarea>
          </div>

          <!-- Error Message -->
          <div v-if="modalError" class="p-3 rounded-lg text-sm" :class="isDarkMode ? 'bg-red-900/20 text-red-400' : 'bg-red-100 text-red-700'">
            {{ modalError }}
          </div>

          <!-- Actions -->
          <div class="flex justify-end gap-3 pt-4 border-t" :class="isDarkMode ? 'border-gray-700' : 'border-gray-200'">
            <button @click="closeModal" :class="[
              'px-4 py-2 rounded-lg transition-colors',
              isDarkMode ? 'bg-gray-700 text-gray-300 hover:bg-gray-600' : 'bg-gray-200 text-gray-800 hover:bg-gray-300'
            ]">
              Cancel
            </button>
            <button @click="updateOrderStatus" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed" :disabled="updating">
              <span v-if="updating">Updating...</span>
              <span v-else>Update Status</span>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div v-if="showDeleteModal" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" @click.self="closeDeleteModal">
      <div :class="[
        'rounded-2xl shadow-2xl max-w-md w-full p-6 animate-slide-up',
        isDarkMode ? 'bg-gray-800' : 'bg-white'
      ]">
        <div class="flex items-center gap-3 mb-4">
          <div class="w-12 h-12 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center flex-shrink-0">
            <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
          </div>
          <div>
            <h3 class="text-lg font-semibold" :class="isDarkMode ? 'text-white' : 'text-gray-900'">Delete Order</h3>
            <p class="text-sm" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">This action cannot be undone</p>
          </div>
        </div>

        <div class="mb-6">
          <p class="text-sm" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">
            Are you sure you want to delete order <span class="font-semibold" :class="isDarkMode ? 'text-white' : 'text-gray-900'">"{{ orderToDelete?.order_number }}"</span>?
          </p>
          <p class="text-sm mt-2" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">
            This will permanently remove this order and all associated data.
          </p>
        </div>

        <div class="flex flex-col sm:flex-row justify-end gap-3">
          <button @click="closeDeleteModal" :class="[
            'px-4 py-2 rounded-lg transition-colors',
            isDarkMode ? 'bg-gray-700 text-gray-300 hover:bg-gray-600' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
          ]" :disabled="deleting">
            Cancel
          </button>
          <button @click="confirmDelete" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed" :disabled="deleting">
            <svg v-if="deleting" class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span v-if="deleting">Deleting...</span>
            <span v-else>Delete Order</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, watch } from 'vue';
import axios from 'axios';

const orders = ref([]);
const loading = ref(false);
const searchTerm = ref('');
const statusFilter = ref('');
const paymentFilter = ref('');
const perPage = ref(10);
const isDarkMode = ref(false);
let darkModeObserver = null;

const pagination = ref({
  current_page: 1,
  last_page: 1,
  from: 0,
  to: 0,
  total: 0,
});

const stats = ref({
  total: 0,
  pending: 0,
  processing: 0,
  completed: 0,
});

// Status Modal state
const showModal = ref(false);
const selectedOrder = ref(null);
const updating = ref(false);
const modalError = ref('');
const statusForm = ref({
  status: '',
  payment_status: '',
  notes: '',
});

// Delete Modal state
const showDeleteModal = ref(false);
const orderToDelete = ref(null);
const deleting = ref(false);

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

const fetchOrders = async () => {
  loading.value = true;
  try {
    const response = await axios.get('/api/admin/orders', {
      params: {
        search: searchTerm.value,
        status: statusFilter.value,
        payment_status: paymentFilter.value,
        page: pagination.value.current_page,
        per_page: perPage.value
      },
    });

    const data = response.data.orders.data || response.data;
    orders.value = data.data || data || [];

    
    pagination.value = {
      current_page: response.data.orders.current_page,
      last_page: response.data.orders.last_page,
      total: response.data.orders.total,
      from: response.data.orders.from,
      to: response.data.orders.to,
    };

    calculateStats();
  } catch (error) {
    console.error('Error fetching orders:', error);
    showToast('Failed to load orders', 'error');
  } finally {
    loading.value = false;
  }
};

const calculateStats = () => {
  const allOrders = orders.value;
  stats.value = {
    total: allOrders.length,
    pending: allOrders.filter((o) => o.status === 'pending').length,
    processing: allOrders.filter((o) => o.status === 'processing').length,
    completed: allOrders.filter((o) => o.status === 'completed').length,
  };
};

const openStatusModal = (order) => {
  selectedOrder.value = order;
  statusForm.value = {
    status: order.status || 'pending',
    payment_status: order.payment_status || 'pending',
    notes: '',
  };
  modalError.value = '';
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
  selectedOrder.value = null;
  statusForm.value = {
    status: '',
    payment_status: '',
    notes: '',
  };
  modalError.value = '';
};

const updateOrderStatus = async () => {
  if (!selectedOrder.value) return;

  updating.value = true;
  modalError.value = '';

  try {
    const response = await axios.put(`/api/admin/orders/${selectedOrder.value.id}`, {
      status: statusForm.value.status,
      payment_status: statusForm.value.payment_status,
      notes: statusForm.value.notes,
    });

    if (response.data.success) {
      showToast('Order status updated successfully', 'success');
      closeModal();
      await fetchOrders();
    } else {
      modalError.value = response.data.message || 'Failed to update order status';
    }
  } catch (error) {
    console.error('Error updating order status:', error);
    modalError.value = error.response?.data?.message || 'An error occurred while updating the order';
  } finally {
    updating.value = false;
  }
};

const openDeleteModal = (order) => {
  orderToDelete.value = order;
  showDeleteModal.value = true;
};

const closeDeleteModal = () => {
  showDeleteModal.value = false;
  orderToDelete.value = null;
  deleting.value = false;
};

const confirmDelete = async () => {
  if (!orderToDelete.value) return;

  deleting.value = true;
  try {
    await axios.delete(`/api/admin/orders/${orderToDelete.value.id}`);
    showToast('Order deleted successfully', 'success');
    closeDeleteModal();
    await fetchOrders();
  } catch (error) {
    console.error('Error deleting order:', error);
    showToast(error.response?.data?.message || 'Failed to delete order', 'error');
    deleting.value = false;
  }
};

const getStatusClass = (status) => {
  const classes = {
    pending: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
    processing: 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
    shipped: 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400',
    delivered: 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
    completed: 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
    cancelled: 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
    failed: 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
    refunded: 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
    paid: 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
  };
  return classes[status] || 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
};

const getPaymentStatusClass = (status) => {
  const classes = {
    pending: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
    paid: 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
    failed: 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
    refunded: 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
  };
  return classes[status] || 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
};

const formatStatus = (status) => {
  return status ? status.charAt(0).toUpperCase() + status.slice(1) : 'N/A';
};

const formatDate = (date) => {
  if (!date) return 'N/A';
  return new Date(date).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric',
  });
};

const getInitials = (name) => {
  if (!name) return '?';
  return name
    .split(' ')
    .map((n) => n[0])
    .join('')
    .toUpperCase()
    .slice(0, 2);
};

const downloadOrder = async (id) => {
  try {
    const response = await axios.get(`/api/admin/orders/${id}/download`, {
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

const previousPage = () => {
  if (pagination.value.current_page > 1) {
    pagination.value.current_page--;
    fetchOrders();
  }
};

const nextPage = () => {
  if (pagination.value.current_page < pagination.value.last_page) {
    pagination.value.current_page++;
    fetchOrders();
  }
};

const resetPage = () => {
  pagination.value.current_page = 1;
  fetchOrders();
};

watch([searchTerm, statusFilter, paymentFilter, perPage], () => {
  resetPage();
});

onMounted(() => {
  checkDarkMode();
  setupDarkModeWatcher();
  fetchOrders();

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

.animate-slide-up {
  animation: slideUp 0.3s ease-out;
}

@keyframes slideUp {
  from {
    transform: translateY(20px);
    opacity: 0;
  }
  to {
    transform: translateY(0);
    opacity: 1;
  }
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