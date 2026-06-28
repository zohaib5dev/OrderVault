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
      <router-link to="/admin/orders/all" :class="isDarkMode ? 'text-gray-400 hover:text-gray-200' : 'text-gray-500 hover:text-gray-700'">
        Orders
      </router-link>
      <span :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'">/</span>
      <span class="truncate max-w-[150px] sm:max-w-xs" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">
        {{ order?.order_number || 'Order Details' }}
      </span>
    </nav>

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
      <div class="flex items-center gap-3">
        <router-link to="/admin/orders/all" class="p-2 rounded-lg transition-colors" :class="isDarkMode ? 'hover:bg-gray-700 text-gray-400' : 'hover:bg-gray-100 text-gray-600'" aria-label="Back to orders">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
          </svg>
        </router-link>
        <div>
          <h1 class="text-2xl md:text-3xl font-bold" :class="isDarkMode ? 'text-white' : 'text-gray-900'">Order Details</h1>
          <p class="text-sm" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">{{ order?.order_number }}</p>
        </div>
      </div>
      <div class="flex flex-wrap gap-2 w-full sm:w-auto">
        <button @click="downloadOrder(order.id)" class="px-4 py-2 bg-blue-600 from-green-500 to-green-600 text-white rounded-lg hover:from-green-600 hover:to-green-700 transition-all duration-200 shadow-md flex items-center gap-2 text-sm">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
          </svg>
          Invoice
        </button>
        <button v-if="canUpdateStatus" @click="showStatusModal = true" class="px-4 py-2 bg-blue-600 from-primary-500 to-primary-600 text-white rounded-lg hover:from-primary-600 hover:to-primary-700 transition-all duration-200 shadow-md flex items-center gap-2 text-sm">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
          </svg>
          Update Status
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
        <span class="mt-4" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Loading order details...</span>
      </div>
    </div>

    <!-- Order Content -->
    <div v-else-if="order" class="space-y-6">
      <!-- Status Banner -->
      <div :class="[
        'rounded-2xl shadow-lg p-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4',
        isDarkMode ? 'bg-gray-800' : 'bg-white'
      ]">
        <div class="flex flex-wrap items-center gap-3">
          <span class="px-3 py-1 text-sm font-semibold rounded-full" :class="getStatusClass(order.status)">
            {{ formatStatus(order.status) }}
          </span>
          <span class="text-sm hidden sm:inline" :class="isDarkMode ? 'text-gray-600' : 'text-gray-300'">•</span>
          <span class="text-sm flex items-center gap-1" :class="isDarkMode ? 'text-gray-400' : 'text-gray-600'">
            Payment: 
            <span class="font-medium" :class="getPaymentStatusClass(order.payment_status)">
              {{ formatStatus(order.payment_status) }}
            </span>
          </span>
          <span class="text-sm hidden sm:inline" :class="isDarkMode ? 'text-gray-600' : 'text-gray-300'">•</span>
          <span class="text-sm flex items-center gap-1" :class="isDarkMode ? 'text-gray-400' : 'text-gray-600'">
            Fulfillment: 
            <span class="font-medium" :class="getFulfillmentStatusClass(order.fulfillment_status)">
              {{ formatStatus(order.fulfillment_status) }}
            </span>
          </span>
        </div>
        <div class="text-sm flex items-center gap-2" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
          </svg>
          {{ formatDate(order.created_at) }}
        </div>
      </div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div :class="[
          'rounded-2xl shadow-lg p-5 border-l-4 border-blue-500',
          isDarkMode ? 'bg-gray-800' : 'bg-white'
        ]">
          <p class="text-sm" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Total Amount</p>
          <p class="text-2xl font-bold" :class="isDarkMode ? 'text-white' : 'text-gray-900'">${{ parseFloat(order.total_amount).toFixed(2) }}</p>
        </div>
        <div :class="[
          'rounded-2xl shadow-lg p-5 border-l-4 border-green-500',
          isDarkMode ? 'bg-gray-800' : 'bg-white'
        ]">
          <p class="text-sm" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Subtotal</p>
          <p class="text-xl font-semibold" :class="isDarkMode ? 'text-white' : 'text-gray-900'">${{ parseFloat(order.subtotal).toFixed(2) }}</p>
        </div>
        <div :class="[
          'rounded-2xl shadow-lg p-5 border-l-4 border-red-500',
          isDarkMode ? 'bg-gray-800' : 'bg-white'
        ]">
          <p class="text-sm" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Discount</p>
          <p class="text-xl font-semibold text-green-600 dark:text-green-400">-${{ parseFloat(order.discount_amount).toFixed(2) }}</p>
        </div>
        <div :class="[
          'rounded-2xl shadow-lg p-5 border-l-4 border-purple-500',
          isDarkMode ? 'bg-gray-800' : 'bg-white'
        ]">
          <p class="text-sm" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Tax</p>
          <p class="text-xl font-semibold" :class="isDarkMode ? 'text-white' : 'text-gray-900'">${{ parseFloat(order.tax_amount).toFixed(2) }}</p>
        </div>
      </div>

      <!-- Main Content Grid -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column - Customer & Order Info -->
        <div class="lg:col-span-1 space-y-6">
          <!-- Customer Card -->
          <div :class="[
            'rounded-2xl shadow-lg p-6',
            isDarkMode ? 'bg-gray-800' : 'bg-white'
          ]">
            <h3 class="text-sm font-semibold uppercase tracking-wider mb-4" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Customer</h3>
            <div class="space-y-3">
              <div class="flex items-start gap-3">
                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center flex-shrink-0">
                  <span class="text-blue-600 dark:text-blue-400 font-semibold">{{ getInitials(order.customer?.name) }}</span>
                </div>
                <div class="min-w-0">
                  <p class="font-medium truncate" :class="isDarkMode ? 'text-white' : 'text-gray-900'">{{ order.customer?.name }}</p>
                  <p class="text-sm truncate" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">{{ order.customer?.email }}</p>
                  <p v-if="order.customer?.phone" class="text-sm" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">{{ order.customer?.phone }}</p>
                </div>
              </div>
              <div class="pt-3 border-t" :class="isDarkMode ? 'border-gray-700' : 'border-gray-100'">
                <p class="text-xs" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Customer since</p>
                <p class="text-sm" :class="isDarkMode ? 'text-white' : 'text-gray-900'">{{ formatDate(order.customer?.created_at) }}</p>
              </div>
            </div>
          </div>

          <!-- Order Info Card -->
          <div :class="[
            'rounded-2xl shadow-lg p-6',
            isDarkMode ? 'bg-gray-800' : 'bg-white'
          ]">
            <h3 class="text-sm font-semibold uppercase tracking-wider mb-4" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Order Information</h3>
            <dl class="space-y-3">
              <div>
                <dt class="text-xs" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Order Number</dt>
                <dd class="text-sm font-medium break-all" :class="isDarkMode ? 'text-white' : 'text-gray-900'">{{ order.order_number }}</dd>
              </div>
              <div>
                <dt class="text-xs" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Payment Method</dt>
                <dd class="text-sm" :class="isDarkMode ? 'text-gray-300' : 'text-gray-900'">{{ formatPaymentMethod(order.payment_method) }}</dd>
              </div>
              <div>
                <dt class="text-xs" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Order Date</dt>
                <dd class="text-sm" :class="isDarkMode ? 'text-white' : 'text-gray-900'">{{ formatDate(order.created_at) }}</dd>
              </div>
              <div v-if="order.notes">
                <dt class="text-xs" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Notes</dt>
                <dd class="text-sm mt-1 p-2 rounded break-words" :class="isDarkMode ? 'bg-gray-700 text-gray-300' : 'bg-gray-50 text-gray-700'">{{ order.notes }}</dd>
              </div>
            </dl>
          </div>

          <!-- Shipping & Billing -->
          <div :class="[
            'rounded-2xl shadow-lg p-6',
            isDarkMode ? 'bg-gray-800' : 'bg-white'
          ]">
            <h3 class="text-sm font-semibold uppercase tracking-wider mb-4" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Addresses</h3>
            <div class="space-y-4">
              <div>
                <p class="text-xs font-medium" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Shipping Address</p>
                <p class="text-sm mt-1 break-words" :class="isDarkMode ? 'text-gray-300' : 'text-gray-900'">{{ order.shipping_address || 'N/A' }}</p>
              </div>
              <div class="pt-3 border-t" :class="isDarkMode ? 'border-gray-700' : 'border-gray-100'">
                <p class="text-xs font-medium" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Billing Address</p>
                <p class="text-sm mt-1 break-words" :class="isDarkMode ? 'text-gray-300' : 'text-gray-900'">{{ order.billing_address || 'N/A' }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Right Column - Order Items -->
        <div class="lg:col-span-2 space-y-6">
          <div :class="[
            'rounded-2xl shadow-lg overflow-hidden',
            isDarkMode ? 'bg-gray-800' : 'bg-white'
          ]">
            <div class="p-4 border-b" :class="isDarkMode ? 'border-gray-700' : 'border-gray-100'">
              <h3 class="text-sm font-semibold uppercase tracking-wider" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Order Items</h3>
            </div>
            
            <!-- Desktop Table View -->
            <div class="hidden md:block overflow-x-auto">
              <table class="w-full min-w-[600px]">
                <thead :class="isDarkMode ? 'bg-gray-700/50' : 'bg-gray-50'">
                  <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Product</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">SKU</th>
                    <th class="px-4 py-3 text-right text-xs font-medium uppercase" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Price</th>
                    <th class="px-4 py-3 text-right text-xs font-medium uppercase" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Qty</th>
                    <th class="px-4 py-3 text-right text-xs font-medium uppercase" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Discount</th>
                    <th class="px-4 py-3 text-right text-xs font-medium uppercase" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Total</th>
                  </tr>
                </thead>
                <tbody class="divide-y" :class="isDarkMode ? 'divide-gray-700' : 'divide-gray-100'">
                  <tr v-for="item in order.items" :key="item.id" class="transition-colors" :class="isDarkMode ? 'hover:bg-gray-700/50' : 'hover:bg-gray-50'">
                    <td class="px-4 py-3">
                      <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded flex items-center justify-center flex-shrink-0" :class="isDarkMode ? 'bg-gray-700' : 'bg-gray-100'">
                          <svg class="w-5 h-5" :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                          </svg>
                        </div>
                        <div class="min-w-0">
                          <p class="font-medium truncate" :class="isDarkMode ? 'text-white' : 'text-gray-900'">{{ item.product_name }}</p>
                          <p class="text-xs truncate" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">{{ item.product?.category?.name || 'Uncategorized' }}</p>
                        </div>
                      </div>
                    </td>
                    <td class="px-4 py-3 text-sm" :class="isDarkMode ? 'text-gray-400' : 'text-gray-600'">{{ item.product_sku }}</td>
                    <td class="px-4 py-3 text-sm text-right" :class="isDarkMode ? 'text-white' : 'text-gray-900'">${{ parseFloat(item.unit_price).toFixed(2) }}</td>
                    <td class="px-4 py-3 text-sm text-right" :class="isDarkMode ? 'text-white' : 'text-gray-900'">{{ item.quantity }}</td>
                    <td class="px-4 py-3 text-sm text-right" :class="isDarkMode ? 'text-white' : 'text-gray-900'">${{ parseFloat(item.discount_amount).toFixed(2) }}</td>
                    <td class="px-4 py-3 text-sm font-semibold text-right" :class="isDarkMode ? 'text-white' : 'text-gray-900'">${{ parseFloat(item.total_price).toFixed(2) }}</td>
                  </tr>
                </tbody>
                <tfoot :class="isDarkMode ? 'bg-gray-700/50' : 'bg-gray-50'">
                  <tr>
                    <td colspan="4" class="px-4 py-3"></td>
                    <td class="px-4 py-3 text-sm font-medium text-right" :class="isDarkMode ? 'text-gray-400' : 'text-gray-700'">Subtotal</td>
                    <td class="px-4 py-3 text-sm font-semibold text-right" :class="isDarkMode ? 'text-white' : 'text-gray-900'">${{ parseFloat(order.subtotal).toFixed(2) }}</td>
                  </tr>
                  <tr>
                    <td colspan="4" class="px-4 py-3"></td>
                    <td class="px-4 py-3 text-sm font-medium text-right" :class="isDarkMode ? 'text-gray-400' : 'text-gray-700'">Discount</td>
                    <td class="px-4 py-3 text-sm font-semibold text-right text-green-600 dark:text-green-400">-${{ parseFloat(order.discount_amount).toFixed(2) }}</td>
                  </tr>
                  <tr>
                    <td colspan="4" class="px-4 py-3"></td>
                    <td class="px-4 py-3 text-sm font-medium text-right" :class="isDarkMode ? 'text-gray-400' : 'text-gray-700'">Tax</td>
                    <td class="px-4 py-3 text-sm font-semibold text-right" :class="isDarkMode ? 'text-white' : 'text-gray-900'">${{ parseFloat(order.tax_amount).toFixed(2) }}</td>
                  </tr>
                  <tr v-if="parseFloat(order.shipping_cost) > 0">
                    <td colspan="4" class="px-4 py-3"></td>
                    <td class="px-4 py-3 text-sm font-medium text-right" :class="isDarkMode ? 'text-gray-400' : 'text-gray-700'">Shipping</td>
                    <td class="px-4 py-3 text-sm font-semibold text-right" :class="isDarkMode ? 'text-white' : 'text-gray-900'">${{ parseFloat(order.shipping_cost).toFixed(2) }}</td>
                  </tr>
                  <tr class="border-t-2" :class="isDarkMode ? 'border-gray-600' : 'border-gray-200'">
                    <td colspan="4" class="px-4 py-3"></td>
                    <td class="px-4 py-3 text-base font-bold text-right" :class="isDarkMode ? 'text-white' : 'text-gray-900'">Total</td>
                    <td class="px-4 py-3 text-lg font-bold text-right text-blue-600 dark:text-blue-400">${{ parseFloat(order.total_amount).toFixed(2) }}</td>
                  </tr>
                </tfoot>
              </table>
            </div>

            <!-- Mobile Card View -->
            <div class="md:hidden divide-y" :class="isDarkMode ? 'divide-gray-700' : 'divide-gray-100'">
              <div v-for="item in order.items" :key="item.id" class="p-4 space-y-2">
                <div class="flex items-start gap-3">
                  <div class="w-10 h-10 rounded flex items-center justify-center flex-shrink-0" :class="isDarkMode ? 'bg-gray-700' : 'bg-gray-100'">
                    <svg class="w-5 h-5" :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                  </div>
                  <div class="flex-1 min-w-0">
                    <p class="font-medium" :class="isDarkMode ? 'text-white' : 'text-gray-900'">{{ item.product_name }}</p>
                    <p class="text-xs" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">{{ item.product?.category?.name || 'Uncategorized' }}</p>
                    <p class="text-xs mt-1" :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'">SKU: {{ item.product_sku }}</p>
                  </div>
                </div>
                <div class="grid grid-cols-3 gap-2 text-sm rounded-lg p-3" :class="isDarkMode ? 'bg-gray-700' : 'bg-gray-50'">
                  <div>
                    <p class="text-xs" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Price</p>
                    <p class="font-medium" :class="isDarkMode ? 'text-white' : 'text-gray-900'">${{ parseFloat(item.unit_price).toFixed(2) }}</p>
                  </div>
                  <div class="text-center">
                    <p class="text-xs" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Qty</p>
                    <p class="font-medium" :class="isDarkMode ? 'text-white' : 'text-gray-900'">{{ item.quantity }}</p>
                  </div>
                  <div class="text-right">
                    <p class="text-xs" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Total</p>
                    <p class="font-semibold" :class="isDarkMode ? 'text-white' : 'text-gray-900'">${{ parseFloat(item.total_price).toFixed(2) }}</p>
                  </div>
                </div>
              </div>
              
              <!-- Mobile Totals -->
              <div class="p-4 space-y-2" :class="isDarkMode ? 'bg-gray-700' : 'bg-gray-50'">
                <div class="flex justify-between text-sm">
                  <span :class="isDarkMode ? 'text-gray-400' : 'text-gray-600'">Subtotal</span>
                  <span class="font-medium" :class="isDarkMode ? 'text-white' : 'text-gray-900'">${{ parseFloat(order.subtotal).toFixed(2) }}</span>
                </div>
                <div class="flex justify-between text-sm">
                  <span :class="isDarkMode ? 'text-gray-400' : 'text-gray-600'">Discount</span>
                  <span class="font-medium text-green-600 dark:text-green-400">-${{ parseFloat(order.discount_amount).toFixed(2) }}</span>
                </div>
                <div class="flex justify-between text-sm">
                  <span :class="isDarkMode ? 'text-gray-400' : 'text-gray-600'">Tax</span>
                  <span class="font-medium" :class="isDarkMode ? 'text-white' : 'text-gray-900'">${{ parseFloat(order.tax_amount).toFixed(2) }}</span>
                </div>
                <div v-if="parseFloat(order.shipping_cost) > 0" class="flex justify-between text-sm">
                  <span :class="isDarkMode ? 'text-gray-400' : 'text-gray-600'">Shipping</span>
                  <span class="font-medium" :class="isDarkMode ? 'text-white' : 'text-gray-900'">${{ parseFloat(order.shipping_cost).toFixed(2) }}</span>
                </div>
                <div class="flex justify-between text-base font-bold pt-2 border-t-2" :class="isDarkMode ? 'border-gray-600 text-white' : 'border-gray-200 text-gray-900'">
                  <span>Total</span>
                  <span class="text-blue-600 dark:text-blue-400">${{ parseFloat(order.total_amount).toFixed(2) }}</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Timeline / Activity -->
          <div :class="[
            'rounded-2xl shadow-lg p-6',
            isDarkMode ? 'bg-gray-800' : 'bg-white'
          ]">
            <h3 class="text-sm font-semibold uppercase tracking-wider mb-4" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Order Timeline</h3>
            <div class="space-y-4">
              <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center flex-shrink-0">
                  <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                  </svg>
                </div>
                <div class="min-w-0">
                  <p class="text-sm font-medium" :class="isDarkMode ? 'text-white' : 'text-gray-900'">Order Created</p>
                  <p class="text-xs" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">{{ formatDate(order.created_at) }}</p>
                </div>
              </div>
              <div v-if="order.paid_at" class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center flex-shrink-0">
                  <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                </div>
                <div class="min-w-0">
                  <p class="text-sm font-medium" :class="isDarkMode ? 'text-white' : 'text-gray-900'">Payment Received</p>
                  <p class="text-xs" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">{{ formatDate(order.paid_at) }}</p>
                </div>
              </div>
              <div v-if="order.shipped_at" class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-full bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center flex-shrink-0">
                  <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                  </svg>
                </div>
                <div class="min-w-0">
                  <p class="text-sm font-medium" :class="isDarkMode ? 'text-white' : 'text-gray-900'">Order Shipped</p>
                  <p class="text-xs" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">{{ formatDate(order.shipped_at) }}</p>
                </div>
              </div>
              <div v-if="order.delivered_at" class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center flex-shrink-0">
                  <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                  </svg>
                </div>
                <div class="min-w-0">
                  <p class="text-sm font-medium" :class="isDarkMode ? 'text-white' : 'text-gray-900'">Order Delivered</p>
                  <p class="text-xs" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">{{ formatDate(order.delivered_at) }}</p>
                </div>
              </div>
            </div>
          </div>
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
      <h3 class="text-lg font-medium mb-2" :class="isDarkMode ? 'text-white' : 'text-gray-900'">Order Not Found</h3>
      <p class="mb-4" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">The order you're looking for doesn't exist or has been removed.</p>
      <router-link to="/admin/orders/all" class="px-4 py-2 bg-blue-600 from-primary-500 to-primary-600 text-white rounded-lg hover:from-primary-600 hover:to-primary-700 transition-all duration-200 shadow-md inline-block">
        Return to Orders
      </router-link>
    </div>

    <!-- Status Update Modal -->
    <div v-if="showStatusModal" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" @click.self="showStatusModal = false">
      <div :class="[
        'rounded-2xl shadow-2xl max-w-md w-full p-6 max-h-[90vh] overflow-y-auto',
        isDarkMode ? 'bg-gray-800' : 'bg-white'
      ]">
        <h3 class="text-lg font-semibold mb-4" :class="isDarkMode ? 'text-white' : 'text-gray-900'">Update Order Status</h3>
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium mb-1" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">Status</label>
            <select v-model="newStatus" :class="[
              'w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors',
              isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'
            ]">
              <option value="pending">Pending</option>
              <option value="processing">Processing</option>
              <option value="completed">Completed</option>
              <option value="cancelled">Cancelled</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium mb-1" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">Payment Status</label>
            <select v-model="newPaymentStatus" :class="[
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
            <label class="block text-sm font-medium mb-1" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">Fulfillment Status</label>
            <select v-model="newFulfillmentStatus" :class="[
              'w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors',
              isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'
            ]">
              <option value="pending">Pending</option>
              <option value="processing">Processing</option>
              <option value="shipped">Shipped</option>
              <option value="delivered">Delivered</option>
            </select>
          </div>
        </div>
        <div class="flex flex-col sm:flex-row justify-end gap-3 mt-6">
          <button @click="showStatusModal = false" :class="[
            'px-4 py-2 rounded-lg transition-colors',
            isDarkMode ? 'bg-gray-700 text-gray-300 hover:bg-gray-600' : 'bg-gray-200 text-gray-800 hover:bg-gray-300'
          ]">
            Cancel
          </button>
          <button @click="updateStatus" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
            Update
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';

const route = useRoute();
const router = useRouter();

const order = ref(null);
const loading = ref(true);
const showStatusModal = ref(false);
const newStatus = ref('');
const newPaymentStatus = ref('');
const newFulfillmentStatus = ref('');
const isDarkMode = ref(false);
let darkModeObserver = null;

const canUpdateStatus = ref(true);
const canDelete = ref(true);

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

const getStatusClass = (status) => {
  const classes = {
    pending: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
    processing: 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
    completed: 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
    cancelled: 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
    shipped: 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400',
    delivered: 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
    paid: 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
    failed: 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
    refunded: 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'
  };
  return classes[status] || 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
};

const getPaymentStatusClass = (status) => {
  const classes = {
    pending: 'text-yellow-600 dark:text-yellow-400',
    paid: 'text-green-600 dark:text-green-400',
    failed: 'text-red-600 dark:text-red-400',
    refunded: 'text-gray-600 dark:text-gray-400'
  };
  return classes[status] || 'text-gray-600 dark:text-gray-400';
};

const getFulfillmentStatusClass = (status) => {
  const classes = {
    pending: 'text-yellow-600 dark:text-yellow-400',
    processing: 'text-blue-600 dark:text-blue-400',
    shipped: 'text-purple-600 dark:text-purple-400',
    delivered: 'text-green-600 dark:text-green-400'
  };
  return classes[status] || 'text-gray-600 dark:text-gray-400';
};

const formatStatus = (status) => {
  return status ? status.charAt(0).toUpperCase() + status.slice(1) : 'N/A';
};

const formatPaymentMethod = (method) => {
  const methods = {
    cash: 'Cash',
    credit_card: 'Credit Card',
    bank_transfer: 'Bank Transfer',
    paypal: 'PayPal',
    stripe: 'Stripe'
  };
  return methods[method] || method || 'N/A';
};

const formatDate = (date) => {
  if (!date) return 'N/A';
  return new Date(date).toLocaleString('en-US', {
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

const fetchOrder = async () => {
  loading.value = true;
  try {
    const response = await axios.get(`/api/admin/orders/${route.params.id}`);
    order.value = response.data;
    
    newStatus.value = order.value.status;
    newPaymentStatus.value = order.value.payment_status;
    newFulfillmentStatus.value = order.value.fulfillment_status;
  } catch (error) {
    console.error('Error fetching order:', error);
    showToast('Failed to load order details', 'error');
  } finally {
    loading.value = false;
  }
};

const updateStatus = async () => {
  try {
    const response = await axios.put(`/api/admin/orders/${route.params.id}/status`, {
      status: newStatus.value,
      payment_status: newPaymentStatus.value,
      fulfillment_status: newFulfillmentStatus.value
    });
    
    if (response.data.success) {
      showToast('Order status updated successfully', 'success');
      showStatusModal.value = false;
      fetchOrder();
    } else {
      showToast(response.data.message || 'Failed to update status', 'error');
    }
  } catch (error) {
    console.error('Error updating status:', error);
    showToast(error.response?.data?.message || 'Failed to update status', 'error');
  }
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

onMounted(() => {
  checkDarkMode();
  setupDarkModeWatcher();
  fetchOrder();

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

/* Print styles */
@media print {
  button {
    display: none !important;
  }
  .shadow-lg {
    box-shadow: none !important;
  }
  .border {
    border-color: #e5e7eb !important;
  }
}
</style>