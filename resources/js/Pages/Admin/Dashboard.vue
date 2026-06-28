<template>
  <div class="space-y-6 pb-8">
  

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
      <div>
        <h1 class="text-2xl md:text-3xl font-bold" :class="isDarkMode ? 'text-white' : 'text-gray-900'">Welcome back, {{ authStore.user?.name }}</h1>
        <p class="text-sm" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Overview of your entire marketplace</p>
      </div>
      <div class="flex gap-2">
        <button @click="refreshData" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-primary-500 to-primary-600 text-white rounded-lg hover:from-primary-600 hover:to-primary-700 transition-all duration-200 shadow-md text-sm">
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
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
     

        <!-- All Orders -->
        <div :class="[
          'rounded-2xl shadow-lg p-5 border-l-4 border-green-500',
          isDarkMode ? 'bg-gray-800' : 'bg-white'
        ]">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">All Orders</p>
              <p class="text-2xl font-bold mt-1" :class="isDarkMode ? 'text-white' : 'text-gray-900'">{{ stats.total_orders }}</p>
              <p class="text-xs mt-1 flex items-center text-green-600 dark:text-green-400">
                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd"/>
                </svg>
                {{ stats.orders_growth || 0 }}% from last month
              </p>
            </div>
            <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center">
              <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
              </svg>
            </div>
          </div>
        </div>

   <!-- Total Revenue -->
        <div :class="[
          'rounded-2xl shadow-lg p-5 border-l-4 border-blue-500',
          isDarkMode ? 'bg-gray-800' : 'bg-white'
        ]">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">All Orders Revenue</p>
              <p class="text-2xl font-bold mt-1" :class="isDarkMode ? 'text-white' : 'text-gray-900'">${{ formatNumber(stats.total_revenue) }}</p>
              <p class="text-xs mt-1 flex items-center text-green-600 dark:text-green-400">
                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd"/>
                </svg>
                {{ stats.revenue_growth || 0 }}% from last month
              </p>
            </div>
            <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center">
              <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v1m0 1v1m0 1v1m0 1v1m0 1v1"/>
              </svg>
            </div>
          </div>
        </div>

        <!-- My Orders -->
        <div :class="[
          'rounded-2xl shadow-lg p-5 border-l-4 border-purple-500',
          isDarkMode ? 'bg-gray-800' : 'bg-white'
        ]">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">My Orders</p>
              <p class="text-2xl font-bold mt-1" :class="isDarkMode ? 'text-white' : 'text-gray-900'">{{ stats.my_orders || 0 }}</p>
              <p class="text-xs mt-1 flex items-center text-green-600 dark:text-green-400">
                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
                {{ stats.myOrders_growth || 0 }} % from last month
              </p>
            </div>
            <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center">
              <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
              </svg>
            </div>
          </div>
        </div>

        <!-- Total Revenue -->
        <div :class="[
          'rounded-2xl shadow-lg p-5 border-l-4 border-blue-500',
          isDarkMode ? 'bg-gray-800' : 'bg-white'
        ]">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">My Orders Revenue</p>
              <p class="text-2xl font-bold mt-1" :class="isDarkMode ? 'text-white' : 'text-gray-900'">${{ formatNumber(stats.total_myOrders_revenue) }}</p>
              <p class="text-xs mt-1 flex items-center text-green-600 dark:text-green-400">
                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd"/>
                </svg>
                {{ stats.revenue_myOrders_growth || 0 }}% from last month
              </p>
            </div>
            <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center">
              <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v1m0 1v1m0 1v1m0 1v1m0 1v1"/>
              </svg>
            </div>
          </div>
        </div>

        <!-- Plan Purchases -->
        <div :class="[
          'rounded-2xl shadow-lg p-5 border-l-4 border-orange-500',
          isDarkMode ? 'bg-gray-800' : 'bg-white'
        ]">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Plan Purchases</p>
              <p class="text-2xl font-bold mt-1" :class="isDarkMode ? 'text-white' : 'text-gray-900'">{{ stats.plan_purchases || 0 }}</p>
              <p class="text-xs mt-1 flex items-center text-green-600 dark:text-green-400">
                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd"/>
                </svg>
                ${{ formatNumber(stats.plan_revenue || 0) }} total
              </p>
            </div>
            <div class="w-10 h-10 bg-orange-100 dark:bg-orange-900/30 rounded-xl flex items-center justify-center">
              <svg class="w-5 h-5 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
              </svg>
            </div>
          </div>
        </div>
      </div>

      <!-- Quick Stats Row -->
      <div class="grid grid-cols-2 my-3 sm:grid-cols-4 gap-4">
        <div :class="[
          'rounded-2xl shadow-lg p-3 text-center',
          isDarkMode ? 'bg-gray-800' : 'bg-white'
        ]">
          <p class="text-xs" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Vendors</p>
          <p class="text-xl font-bold" :class="isDarkMode ? 'text-white' : 'text-gray-900'">{{ stats.total_vendors }}</p>
        </div>
        <div :class="[
          'rounded-2xl shadow-lg p-3 text-center',
          isDarkMode ? 'bg-gray-800' : 'bg-white'
        ]">
          <p class="text-xs" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Products</p>
          <p class="text-xl font-bold" :class="isDarkMode ? 'text-white' : 'text-gray-900'">{{ stats.total_products }}</p>
        </div>
        <div :class="[
          'rounded-2xl shadow-lg p-3 text-center',
          isDarkMode ? 'bg-gray-800' : 'bg-white'
        ]">
          <p class="text-xs" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Customers</p>
          <p class="text-xl font-bold" :class="isDarkMode ? 'text-white' : 'text-gray-900'">{{ stats.total_customers }}</p>
        </div>
        <div :class="[
          'rounded-2xl shadow-lg p-3 text-center',
          isDarkMode ? 'bg-gray-800' : 'bg-white'
        ]">
          <p class="text-xs" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Active Subscriptions</p>
          <p class="text-xl font-bold" :class="isDarkMode ? 'text-white' : 'text-gray-900'">{{ stats.active_subscriptions || 0 }}</p>
        </div>
      </div>

      <!-- Recent Activity -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Orders -->
        <div :class="[
          'rounded-2xl shadow-lg p-4',
          isDarkMode ? 'bg-gray-800' : 'bg-white'
        ]">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-semibold" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">Recent Orders</h3>
            <router-link to="/admin/orders/all" class="text-xs font-medium text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
              View All →
            </router-link>
          </div>
          <div class="space-y-3 max-h-80 overflow-y-auto">
            <div
              v-for="order in recentOrders"
              :key="order.id"
              class="flex items-center justify-between p-2 rounded-lg transition-colors"
              :class="isDarkMode ? 'hover:bg-gray-700' : 'hover:bg-gray-50'"
            >
              <div class="flex-1 min-w-0">
                <router-link
                  :to="`/admin/orders/${order.id}`"
                  class="text-sm font-medium hover:underline truncate block"
                  :class="isDarkMode ? 'text-blue-400' : 'text-blue-600'"
                >
                  #{{ order.order_number }}
                </router-link>
                <p class="text-xs truncate" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">{{ order.customer_name || 'Guest' }}</p>
                <p class="text-xs" :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'">{{ formatDate(order.created_at) }}</p>
              </div>
              <div class="text-right ml-4 flex-shrink-0">
                <p class="text-sm font-semibold" :class="isDarkMode ? 'text-white' : 'text-gray-900'">
                  ${{ formatNumber(order.total_amount) }}
                </p>
                <span
                  class="text-xs px-2 py-1 rounded-full inline-block"
                  :class="getStatusClass(order.status)"
                >
                  {{ formatStatus(order.status) }}
                </span>
              </div>
            </div>
            <div v-if="recentOrders.length === 0" class="text-center py-8" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">
              <svg class="w-12 h-12 mx-auto mb-2" :class="isDarkMode ? 'text-gray-600' : 'text-gray-300'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
              </svg>
              <p class="text-sm">No recent orders</p>
            </div>
          </div>
        </div>

        <!-- My Orders -->
        <div :class="[
          'rounded-2xl shadow-lg p-4',
          isDarkMode ? 'bg-gray-800' : 'bg-white'
        ]">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-semibold" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">My Orders</h3>
            <router-link to="/admin/orders" class="text-xs font-medium text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
              View All →
            </router-link>
          </div>
          <div class="space-y-3 max-h-80 overflow-y-auto">
            <div
              v-for="order in myOrders"
              :key="order.id"
              class="flex items-center justify-between p-2 rounded-lg transition-colors"
              :class="isDarkMode ? 'hover:bg-gray-700' : 'hover:bg-gray-50'"
            >
              <div class="flex-1 min-w-0">
                <router-link
                  :to="`/admin/orders/${order.id}`"
                  class="text-sm font-medium hover:underline truncate block"
                  :class="isDarkMode ? 'text-blue-400' : 'text-blue-600'"
                >
                  #{{ order.order_number }}
                </router-link>
                <p class="text-xs truncate" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">{{ order.vendor_name || 'N/A' }}</p>
                <p class="text-xs" :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'">{{ formatDate(order.created_at) }}</p>
              </div>
              <div class="text-right ml-4 flex-shrink-0">
                <p class="text-sm font-semibold" :class="isDarkMode ? 'text-white' : 'text-gray-900'">
                  ${{ formatNumber(order.total_amount) }}
                </p>
                <span
                  class="text-xs px-2 py-1 rounded-full inline-block"
                  :class="getStatusClass(order.status)"
                >
                  {{ formatStatus(order.status) }}
                </span>
              </div>
            </div>
            <div v-if="myOrders.length === 0" class="text-center py-8" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">
              <svg class="w-12 h-12 mx-auto mb-2" :class="isDarkMode ? 'text-gray-600' : 'text-gray-300'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
              </svg>
              <p class="text-sm">No orders assigned to you</p>
            </div>
          </div>
        </div>

        <!-- Recent Vendors -->
        <div :class="[
          'rounded-2xl shadow-lg p-4',
          isDarkMode ? 'bg-gray-800' : 'bg-white'
        ]">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-semibold" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">Recent Vendors</h3>
            <router-link to="/admin/vendors" class="text-xs font-medium text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
              View All →
            </router-link>
          </div>
          <div class="space-y-3 max-h-80 overflow-y-auto">
            <div
              v-for="vendor in recentVendors"
              :key="vendor.id"
              class="flex items-center justify-between p-2 rounded-lg transition-colors"
              :class="isDarkMode ? 'hover:bg-gray-700' : 'hover:bg-gray-50'"
            >
              <div class="flex-1 min-w-0">
                <router-link
                  :to="`/admin/vendors/${vendor.id}`"
                  class="text-sm font-medium hover:underline truncate block"
                  :class="isDarkMode ? 'text-blue-400' : 'text-blue-600'"
                >
                  {{ vendor.business_name || vendor.name }}
                </router-link>
                <p class="text-xs truncate" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">{{ vendor.email }}</p>
                <p class="text-xs" :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'">{{ formatDate(vendor.created_at) }}</p>
              </div>
              <div class="text-right ml-4 flex-shrink-0">
                <span
                  class="text-xs px-2 py-1 rounded-full inline-block"
                  :class="vendor.is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'"
                >
                  {{ vendor.is_active ? 'Active' : 'Inactive' }}
                </span>
              </div>
            </div>
            <div v-if="recentVendors.length === 0" class="text-center py-8" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">
              <svg class="w-12 h-12 mx-auto mb-2" :class="isDarkMode ? 'text-gray-600' : 'text-gray-300'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
              </svg>
              <p class="text-sm">No recent vendors</p>
            </div>
          </div>
        </div>

        <!-- Recent Plan Purchases -->
        <div :class="[
          'rounded-2xl shadow-lg p-4',
          isDarkMode ? 'bg-gray-800' : 'bg-white'
        ]">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-semibold" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">Recent Plan Purchases</h3>
            <router-link to="/admin/vendors/invoices" class="text-xs font-medium text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
              View All →
            </router-link>
          </div>
          <div class="space-y-3 max-h-80 overflow-y-auto">
            <div
              v-for="purchase in recentPlanPurchases"
              :key="purchase.id"
              class="flex items-center justify-between p-2 rounded-lg transition-colors"
              :class="isDarkMode ? 'hover:bg-gray-700' : 'hover:bg-gray-50'"
            >
              <div class="flex-1 min-w-0">
                <p class="text-sm font-medium truncate" :class="isDarkMode ? 'text-white' : 'text-gray-900'">
                  {{ purchase.plan_name }}
                </p>
                <p class="text-xs truncate" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">{{ purchase.vendor_name }}</p>
                <p class="text-xs" :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'">{{ formatDate(purchase.purchased_at) }}</p>
              </div>
              <div class="text-right ml-4 flex-shrink-0">
                <p class="text-sm font-semibold" :class="isDarkMode ? 'text-white' : 'text-gray-900'">
                  ${{ formatNumber(purchase.amount) }}
                </p>
                <span
                  class="text-xs px-2 py-1 rounded-full inline-block"
                  :class="purchase.type === 'subscription' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400' : 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400'"
                >
                  {{ purchase.type || 'One-time' }}
                </span>
              </div>
            </div>
            <div v-if="recentPlanPurchases.length === 0" class="text-center py-8" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">
              <svg class="w-12 h-12 mx-auto mb-2" :class="isDarkMode ? 'text-gray-600' : 'text-gray-300'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
              </svg>
              <p class="text-sm">No recent plan purchases</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from "vue";
import { useAuthStore } from '../../store';
import axios from "axios";

const loading = ref(false);
const isDarkMode = ref(false);
let darkModeObserver = null;

const authStore = useAuthStore();

const stats = ref({
  total_revenue: 0,
  total_orders: 0,
  total_vendors: 0,
  total_products: 0,
  total_customers: 0,
  my_orders: 0,
  my_orders_pending: 0,
  plan_purchases: 0,
  plan_revenue: 0,
  active_subscriptions: 0,
  revenue_growth: 0,
  orders_growth: 0,
});
const recentOrders = ref([]);
const myOrders = ref([]);
const recentVendors = ref([]);
const recentPlanPurchases = ref([]);

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

const getStatusClass = (status) => {
  const classes = {
    pending: "bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400",
    processing: "bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400",
    shipped: "bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400",
    delivered: "bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400",
    completed: "bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400",
    cancelled: "bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400",
    failed: "bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400",
  };
  return classes[status?.toLowerCase()] || "bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300";
};

const formatStatus = (status) => {
  if (!status) return "N/A";
  return status.charAt(0).toUpperCase() + status.slice(1).toLowerCase();
};

const formatNumber = (value) => {
  if (!value) return "0.00";
  return Number(value)
    .toFixed(2)
    .replace(/\B(?=(\d{3})+(?!\d))/g, ",");
};

const formatDate = (date) => {
  if (!date) return "N/A";
  const d = new Date(date);
  return d.toLocaleDateString("en-US", {
    month: "short",
    day: "numeric",
    year: "numeric",
  });
};

const fetchDashboardData = async () => {
  loading.value = true;
  try {
    const response = await axios.get("/api/admin/dashboard");
    if (response.data.success) {
      stats.value = response.data.data.stats || stats.value;
      recentOrders.value = response.data.data.recent_orders || [];
      myOrders.value = response.data.data.my_orders || [];
      recentVendors.value = response.data.data.recent_vendors || [];
      recentPlanPurchases.value = response.data.data.recent_plan_purchases || [];
    }
  } catch (error) {
    console.error("Error fetching dashboard data:", error);
    showToast("Failed to load dashboard data", "error");
  } finally {
    loading.value = false;
  }
};

const refreshData = () => {
  fetchDashboardData();
  showToast("Dashboard refreshed", "success");
};

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

<style scoped>
.overflow-y-auto::-webkit-scrollbar {
  width: 4px;
}

.overflow-y-auto::-webkit-scrollbar-track {
  background: transparent;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
  background: #d1d5db;
  border-radius: 2px;
}

.dark .overflow-y-auto::-webkit-scrollbar-thumb {
  background: #4b5563;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
  background: #9ca3af;
}

.dark .overflow-y-auto::-webkit-scrollbar-thumb:hover {
  background: #6b7280;
}

.animate-spin {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
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