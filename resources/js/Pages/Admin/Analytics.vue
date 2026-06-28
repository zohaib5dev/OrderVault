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
      <span :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">My Orders Analytics</span>
    </nav>

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
      <div>
        <h1 class="text-2xl md:text-3xl font-bold" :class="isDarkMode ? 'text-white' : 'text-gray-900'">My Orders Analytics</h1>
        <p class="text-sm" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Track your store performance and growth</p>
      </div>
      <div class="flex flex-wrap gap-2 w-full sm:w-auto">
        <select v-model="dateRange" @change="fetchAnalytics" 
          :class="[
            'px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors',
            isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'
          ]">
          <option value="7">Last 7 Days</option>
          <option value="30">Last 30 Days</option>
          <option value="90">Last 90 Days</option>
          <option value="365">Last Year</option>
        </select>
        <button @click="exportReport" 
          class="px-4 py-2 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-lg hover:from-green-600 hover:to-green-700 transition-all duration-200 shadow-md flex items-center gap-2">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
          </svg>
          Export Report
        </button>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" :class="[
      'rounded-2xl shadow-lg p-12 text-center',
      isDarkMode ? 'bg-gray-800' : 'bg-white'
    ]">
      <div class="flex flex-col items-center justify-center">
        <div class="w-12 h-12 border-4 border-primary-500 border-t-transparent rounded-full animate-spin"></div>
        <p class="mt-4" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Loading analytics data...</p>
      </div>
    </div>

    <!-- Analytics Content -->
    <div v-else-if="analytics" class="space-y-6">
      <!-- Key Metrics Cards -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div :class="[
          'rounded-2xl shadow-lg p-5 border-l-4 border-blue-500',
          isDarkMode ? 'bg-gray-800' : 'bg-white'
        ]">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Total Revenue</p>
              <p class="text-2xl font-bold" :class="isDarkMode ? 'text-white' : 'text-gray-900'">${{ formatNumber(analytics.metrics.total_revenue) }}</p>
              <p class="text-xs" :class="analytics.metrics.revenue_growth >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'">
                {{ analytics.metrics.revenue_growth >= 0 ? '↑' : '↓' }} {{ Math.abs(analytics.metrics.revenue_growth) }}% from last period
              </p>
            </div>
            <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center">
              <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v1m0 1v1m0 1v1m0 1v1m0 1v1"/>
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
              <p class="text-sm" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Total Orders</p>
              <p class="text-2xl font-bold" :class="isDarkMode ? 'text-white' : 'text-gray-900'">{{ analytics.metrics.total_orders }}</p>
              <p class="text-xs" :class="analytics.metrics.orders_growth >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'">
                {{ analytics.metrics.orders_growth >= 0 ? '↑' : '↓' }} {{ Math.abs(analytics.metrics.orders_growth) }}% from last period
              </p>
            </div>
            <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center">
              <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
              </svg>
            </div>
          </div>
        </div>

        <div :class="[
          'rounded-2xl shadow-lg p-5 border-l-4 border-purple-500',
          isDarkMode ? 'bg-gray-800' : 'bg-white'
        ]">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Average Order Value</p>
              <p class="text-2xl font-bold" :class="isDarkMode ? 'text-white' : 'text-gray-900'">${{ formatNumber(analytics.metrics.average_order_value) }}</p>
              <p class="text-xs" :class="analytics.metrics.aov_growth >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'">
                {{ analytics.metrics.aov_growth >= 0 ? '↑' : '↓' }} {{ Math.abs(analytics.metrics.aov_growth) }}% from last period
              </p>
            </div>
            <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center">
              <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-6 3v-3m-6 3h18M5 17h14M5 7h14M5 7l-1 10m14-10l1 10"/>
              </svg>
            </div>
          </div>
        </div>

        <div :class="[
          'rounded-2xl shadow-lg p-5 border-l-4 border-orange-500',
          isDarkMode ? 'bg-gray-800' : 'bg-white'
        ]">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Conversion Rate</p>
              <p class="text-2xl font-bold" :class="isDarkMode ? 'text-white' : 'text-gray-900'">{{ analytics.metrics.conversion_rate }}%</p>
              <p class="text-xs" :class="analytics.metrics.conversion_growth >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'">
                {{ analytics.metrics.conversion_growth >= 0 ? '↑' : '↓' }} {{ Math.abs(analytics.metrics.conversion_growth) }}% from last period
              </p>
            </div>
            <div class="w-10 h-10 bg-orange-100 dark:bg-orange-900/30 rounded-xl flex items-center justify-center">
              <svg class="w-5 h-5 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
              </svg>
            </div>
          </div>
        </div>
      </div>

      <!-- Charts Grid -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Revenue Chart -->
        <div :class="[
          'rounded-2xl shadow-lg p-4',
          isDarkMode ? 'bg-gray-800' : 'bg-white'
        ]">
          <h3 class="text-sm font-semibold mb-4" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">Revenue Overview</h3>
          <div class="h-64">
            <canvas ref="revenueChartRef"></canvas>
          </div>
        </div>

        <!-- Orders Chart -->
        <div :class="[
          'rounded-2xl shadow-lg p-4',
          isDarkMode ? 'bg-gray-800' : 'bg-white'
        ]">
          <h3 class="text-sm font-semibold mb-4" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">Orders Overview</h3>
          <div class="h-64">
            <canvas ref="ordersChartRef"></canvas>
          </div>
        </div>

        <!-- Top Products -->
        <div :class="[
          'rounded-2xl shadow-lg p-4',
          isDarkMode ? 'bg-gray-800' : 'bg-white'
        ]">
          <h3 class="text-sm font-semibold mb-4" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">Top Selling Products</h3>
          <div class="space-y-3">
            <div v-for="(product, index) in analytics.top_products" :key="index" class="flex items-center gap-3">
              <span class="text-sm font-medium w-6" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">{{ index + 1 }}</span>
              <div class="flex-1">
                <div class="flex justify-between text-sm">
                  <span class="font-medium" :class="isDarkMode ? 'text-white' : 'text-gray-900'">{{ product.name }}</span>
                  <span :class="isDarkMode ? 'text-gray-400' : 'text-gray-600'">${{ formatNumber(product.revenue) }}</span>
                </div>
                <div class="w-full rounded-full h-2 mt-1" :class="isDarkMode ? 'bg-gray-700' : 'bg-gray-200'">
                  <div class="bg-blue-600 dark:bg-blue-400 rounded-full h-2" :style="{ width: product.percentage + '%' }"></div>
                </div>
              </div>
            </div>
            <div v-if="analytics.top_products.length === 0" class="text-center py-4" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">
              No product data available
            </div>
          </div>
        </div>

        <!-- Order Status Distribution -->
        <div :class="[
          'rounded-2xl shadow-lg p-4',
          isDarkMode ? 'bg-gray-800' : 'bg-white'
        ]">
          <h3 class="text-sm font-semibold mb-4" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">Order Status Distribution</h3>
          <div class="space-y-3">
            <div v-for="status in analytics.order_status_distribution" :key="status.status" class="flex items-center gap-3">
              <span class="text-sm font-medium w-24" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">{{ status.label }}</span>
              <div class="flex-1">
                <div class="flex justify-between text-sm">
                  <span :class="isDarkMode ? 'text-gray-400' : 'text-gray-600'">{{ status.count }} orders</span>
                  <span :class="isDarkMode ? 'text-gray-400' : 'text-gray-600'">{{ status.percentage }}%</span>
                </div>
                <div class="w-full rounded-full h-2 mt-1" :class="isDarkMode ? 'bg-gray-700' : 'bg-gray-200'">
                  <div class="rounded-full h-2" :style="{ width: status.percentage + '%', backgroundColor: status.color }"></div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Recent Orders -->
        <div :class="[
          'rounded-2xl shadow-lg p-4 lg:col-span-2 overflow-hidden',
          isDarkMode ? 'bg-gray-800' : 'bg-white'
        ]">
          <h3 class="text-sm font-semibold mb-4" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">Recent Orders</h3>
          <div class="overflow-x-auto">
            <table class="w-full">
              <thead>
                <tr class="text-left text-xs uppercase border-b" :class="[isDarkMode ? 'text-gray-400 border-gray-700' : 'text-gray-500 border-gray-200']">
                  <th class="pb-2">Order #</th>
                  <th class="pb-2">Customer</th>
                  <th class="pb-2 text-right">Total</th>
                  <th class="pb-2">Status</th>
                  <th class="pb-2">Date</th>
                </tr>
              </thead>
              <tbody>
                <tr v-if="analytics.recent_orders.length === 0" class="text-center">
                  <td colspan="5" class="py-4" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">No recent orders</td>
                </tr>
                <tr v-for="order in analytics.recent_orders" :key="order.id" class="border-b" :class="isDarkMode ? 'border-gray-700 hover:bg-gray-700/50' : 'border-gray-100 hover:bg-gray-50'">
                  <td class="py-2">
                    <router-link :to="`/admin/orders/${order.id}`" class="text-blue-600 dark:text-blue-400 hover:underline">
                      {{ order.order_number }}
                    </router-link>
                  </td>
                  <td class="py-2" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">{{ order.customer_name }}</td>
                  <td class="py-2 text-right font-semibold" :class="isDarkMode ? 'text-white' : 'text-gray-900'">${{ formatNumber(order.total_amount) }}</td>
                  <td class="py-2">
                    <span class="text-xs px-2 py-1 rounded-full" :class="getOrderStatusClass(order.status)">
                      {{ formatStatus(order.status) }}
                    </span>
                  </td>
                  <td class="py-2 text-sm" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">{{ formatDate(order.created_at) }}</td>
                </tr>
              </tbody>
            </table>
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
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
      </svg>
      <h3 class="text-lg font-medium mb-2" :class="isDarkMode ? 'text-white' : 'text-gray-900'">No Analytics Data</h3>
      <p class="mb-4" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Start making sales to see your analytics here.</p>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, nextTick } from 'vue';
import axios from 'axios';
import { Chart, registerables } from 'chart.js';

Chart.register(...registerables);

const loading = ref(false);
const analytics = ref(null);
const dateRange = ref(30);
const isDarkMode = ref(false);
let darkModeObserver = null;

let revenueChart = null;
let ordersChart = null;
const revenueChartRef = ref(null);
const ordersChartRef = ref(null);

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
          // Re-render charts when theme changes
          setTimeout(() => renderCharts(), 100);
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

const formatStatus = (status) => {
  return status ? status.charAt(0).toUpperCase() + status.slice(1) : 'N/A';
};

const formatDate = (date) => {
  if (!date) return 'N/A';
  return new Date(date).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric'
  });
};

const formatNumber = (value) => {
  return Number(value).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
};

const fetchAnalytics = async () => {
  loading.value = true;
  try {
    const response = await axios.get('/api/admin/analytics', {
      params: { days: dateRange.value }
    });
    
    analytics.value = response.data.data;
    
    // Render charts after data is loaded
    await nextTick();
    setTimeout(() => {
      renderCharts();
    }, 200);
  } catch (error) {
    console.error('Error fetching analytics:', error);
  } finally {
    loading.value = false;
  }
};

const getChartColors = () => {
  return {
    revenue: isDarkMode.value ? '#60A5FA' : '#3B82F6',
    revenueBg: isDarkMode.value ? 'rgba(96, 165, 250, 0.1)' : 'rgba(59, 130, 246, 0.1)',
    orders: isDarkMode.value ? '#34D399' : '#10B981',
    ordersBg: isDarkMode.value ? 'rgba(52, 211, 153, 0.8)' : 'rgba(16, 185, 129, 0.8)',
    gridColor: isDarkMode.value ? 'rgba(255, 255, 255, 0.05)' : 'rgba(0, 0, 0, 0.05)',
    textColor: isDarkMode.value ? '#9CA3AF' : '#6B7280',
  };
};

const renderCharts = () => {
  if (!analytics.value) return;

  // Destroy existing charts
  if (revenueChart) {
    revenueChart.destroy();
    revenueChart = null;
  }
  if (ordersChart) {
    ordersChart.destroy();
    ordersChart = null;
  }

  const colors = getChartColors();

  // Revenue Chart
  if (revenueChartRef.value) {
    const ctx = revenueChartRef.value.getContext('2d');
    revenueChart = new Chart(ctx, {
      type: 'line',
      data: {
        labels: analytics.value.revenue_data.map(d => formatDate(d.date)),
        datasets: [{
          label: 'Revenue',
          data: analytics.value.revenue_data.map(d => d.revenue),
          borderColor: colors.revenue,
          backgroundColor: colors.revenueBg,
          fill: true,
          tension: 0.4
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            grid: {
              color: colors.gridColor
            },
            ticks: {
              color: colors.textColor,
              callback: function(value) {
                return '$' + value;
              }
            }
          },
          x: {
            grid: {
              color: colors.gridColor
            },
            ticks: {
              color: colors.textColor
            }
          }
        }
      }
    });
  }

  // Orders Chart
  if (ordersChartRef.value) {
    const ctx = ordersChartRef.value.getContext('2d');
    ordersChart = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: analytics.value.orders_data.map(d => formatDate(d.date)),
        datasets: [{
          label: 'Orders',
          data: analytics.value.orders_data.map(d => d.orders),
          backgroundColor: colors.ordersBg,
          borderColor: colors.orders,
          borderWidth: 1
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            grid: {
              color: colors.gridColor
            },
            ticks: {
              color: colors.textColor,
              stepSize: 1
            }
          },
          x: {
            grid: {
              color: colors.gridColor
            },
            ticks: {
              color: colors.textColor
            }
          }
        }
      }
    });
  }
};

const exportReport = () => {
  alert('Export functionality coming soon');
};

onMounted(() => {
  checkDarkMode();
  setupDarkModeWatcher();
  fetchAnalytics();

  // Listen for storage changes from other tabs
  window.addEventListener('storage', (e) => {
    if (e.key === 'theme') {
      isDarkMode.value = e.newValue === 'dark';
      setTimeout(() => renderCharts(), 200);
    }
  });
});

onUnmounted(() => {
  if (darkModeObserver) {
    darkModeObserver.disconnect();
  }
  if (revenueChart) {
    revenueChart.destroy();
    revenueChart = null;
  }
  if (ordersChart) {
    ordersChart.destroy();
    ordersChart = null;
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
</style>