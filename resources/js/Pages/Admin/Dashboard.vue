<template>
    <div class="dashboard-page" :class="{ 'dark-mode': isDarkMode }">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <div>
                <h1 :class="isDarkMode ? 'text-white' : 'text-gray-800'" class="text-2xl font-bold">Welcome back, {{ authStore.user?.name }}</h1>
                <p :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'" class="text-sm mt-1">Welcome back! Here's what's happening with your store.</p>
            </div>
            <button 
                @click="refreshData" 
                :class="[
                    'w-full sm:w-auto px-4 py-2 rounded-lg transition-colors flex items-center justify-center gap-2 text-sm',
                    isDarkMode ? 'bg-gray-700 text-gray-300 hover:bg-gray-600' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                ]"
            >
                <svg class="w-4 h-4" :class="{ 'animate-spin': loading }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Refresh
            </button>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="flex justify-center items-center py-12">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
        </div>

        <div v-else>
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 mb-8">
                <div :class="[
                    'rounded-xl shadow-sm border p-5 transition-all duration-300 hover:shadow-md hover:-translate-y-1',
                    isDarkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-100'
                ]">
                    <div class="flex items-center justify-between">
                        <div>
                            <p :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'" class="text-sm font-medium">Total Orders</p>
                            <p :class="isDarkMode ? 'text-white' : 'text-gray-900'" class="text-2xl font-bold mt-1">{{ stats.total_orders || 0 }}</p>
                        </div>
                        <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-2 flex items-center gap-2 text-xs">
                        <span :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'">Today:</span>
                        <span :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'" class="font-medium">{{ stats.today_orders || 0 }}</span>
                        <span :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'">• This month:</span>
                        <span :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'" class="font-medium">{{ stats.total_orders || 0 }}</span>
                    </div>
                </div>

                <div :class="[
                    'rounded-xl shadow-sm border p-5 transition-all duration-300 hover:shadow-md hover:-translate-y-1',
                    isDarkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-100'
                ]">
                    <div class="flex items-center justify-between">
                        <div>
                            <p :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'" class="text-sm font-medium">Revenue</p>
                            <p class="text-2xl font-bold text-green-600 dark:text-green-400 mt-1">${{ formatNumber(stats.total_revenue || 0) }}</p>
                        </div>
                        <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-2 flex items-center gap-2 text-xs">
                        <span :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'">Today:</span>
                        <span :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'" class="font-medium">${{ formatNumber(stats.today_revenue || 0) }}</span>
                        <span :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'">• This month:</span>
                        <span :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'" class="font-medium">${{ formatNumber(stats.this_month_revenue || 0) }}</span>
                    </div>
                </div>

                <div :class="[
                    'rounded-xl shadow-sm border p-5 transition-all duration-300 hover:shadow-md hover:-translate-y-1',
                    isDarkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-100'
                ]">
                    <div class="flex items-center justify-between">
                        <div>
                            <p :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'" class="text-sm font-medium">Pending Orders</p>
                            <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400 mt-1">{{ stats.pending_orders || 0 }}</p>
                        </div>
                        <div class="w-10 h-10 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-2 flex items-center gap-2 text-xs">
                        <span :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'">Processing:</span>
                        <span class="font-medium text-blue-600 dark:text-blue-400">{{ stats.processing_orders || 0 }}</span>
                        <span :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'">• Completed:</span>
                        <span class="font-medium text-green-600 dark:text-green-400">{{ stats.completed_orders || 0 }}</span>
                    </div>
                </div>

                <div :class="[
                    'rounded-xl shadow-sm border p-5 transition-all duration-300 hover:shadow-md hover:-translate-y-1',
                    isDarkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-100'
                ]">
                    <div class="flex items-center justify-between">
                        <div>
                            <p :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'" class="text-sm font-medium">Products</p>
                            <p class="text-2xl font-bold text-purple-600 dark:text-purple-400 mt-1">{{ stats.total_products || 0 }}</p>
                        </div>
                        <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-2 flex items-center gap-2 text-xs">
                        <span :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'">Active:</span>
                        <span class="font-medium text-green-600 dark:text-green-400">{{ stats.active_products || 0 }}</span>
                        <span :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'">• Low stock:</span>
                        <span class="font-medium text-red-600 dark:text-red-400">{{ stats.low_stock_products || 0 }}</span>
                    </div>
                </div>
            </div>

           

            <!-- Charts Row -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Weekly Sales Chart -->
                <div :class="[
                    'rounded-xl shadow-sm border p-2 lg:p-6',
                    isDarkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-100'
                ]">
                    <h3 :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'" class="text-sm font-semibold uppercase tracking-wider mb-4">Last 7 Days Sales</h3>
                    <div class="h-48 flex items-end gap-2">
                        <div v-for="day in chartData.sales_last_7_days" :key="day.date" class="flex-1 flex flex-col items-center gap-1">
                            <div class="w-full flex flex-col items-center">
                                <span :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'" class="text-xs font-medium mb-1">${{ formatNumber(day.total) }}</span>
                                <div class="w-full bg-blue-100 dark:bg-blue-900/30 rounded-t-lg transition-all duration-500" 
                                     :style="{ height: getBarHeight(day.total, chartData.sales_last_7_days) + 'px' }">
                                    <div class="w-full h-full bg-blue-500 dark:bg-blue-400 rounded-t-lg opacity-75"></div>
                                </div>
                            </div>
                            <span :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'" class="text-xs">{{ day.day }}</span>
                        </div>
                    </div>
                    <div class="mt-3 flex justify-between text-xs" :class="isDarkMode ? 'text-gray-400' : 'text-gray-400'">
                        <span>Total: ${{ formatNumber(getWeeklyTotal) }}</span>
                        <span>{{ getWeeklyOrders }} orders</span>
                    </div>
                </div>

                <!-- Monthly Sales Chart -->
                <div :class="[
                    'rounded-xl shadow-sm border p-2 lg:p-6',
                    isDarkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-100'
                ]">
                    <h3 :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'" class="text-sm font-semibold uppercase tracking-wider mb-4">Last 6 Months Sales</h3>
                    <div class="h-48 flex items-end gap-2">
                        <div v-for="month in chartData.sales_last_6_months" :key="month.month" class="flex-1 flex flex-col items-center gap-1">
                            <div class="w-full flex flex-col items-center">
                                <span :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'" class="text-xs font-medium mb-1">${{ formatNumber(month.total) }}</span>
                                <div class="w-full bg-green-100 dark:bg-green-900/30 rounded-t-lg transition-all duration-500" 
                                     :style="{ height: getBarHeight(month.total, chartData.sales_last_6_months) + 'px' }">
                                    <div class="w-full h-full bg-green-500 dark:bg-green-400 rounded-t-lg opacity-75"></div>
                                </div>
                            </div>
                            <span :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'" class="text-xs">{{ month.month_name.split(' ')[0] }}</span>
                        </div>
                    </div>
                    <div class="mt-3 flex justify-between text-xs" :class="isDarkMode ? 'text-gray-400' : 'text-gray-400'">
                        <span>Total: ${{ formatNumber(getMonthlyTotal) }}</span>
                        <span>{{ getMonthlyOrders }} orders</span>
                    </div>
                </div>
            </div>

            <!-- Status Distribution & Top Products -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <!-- Order Status Distribution -->
                <div :class="[
                    'rounded-xl shadow-sm border p-6',
                    isDarkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-100'
                ]">
                    <h3 :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'" class="text-sm font-semibold uppercase tracking-wider mb-4">Order Status</h3>
                    <div class="space-y-3">
                        <div v-for="status in chartData.orders_by_status" :key="status.status" class="flex items-center gap-3">
                            <span class="w-3 h-3 rounded-full" :class="getStatusDotColor(status.status)"></span>
                            <span :class="isDarkMode ? 'text-gray-300' : 'text-gray-600'" class="text-sm flex-1">{{ status.label }}</span>
                            <span :class="isDarkMode ? 'text-white' : 'text-gray-900'" class="text-sm font-semibold">{{ status.count }}</span>
                            <span :class="isDarkMode ? 'text-gray-400' : 'text-gray-400'" class="text-xs">{{ getStatusPercentage(status.count) }}%</span>
                        </div>
                    </div>
                </div>

                <!-- Top Products -->
                <div :class="[
                    'rounded-xl shadow-sm border p-6 lg:col-span-2',
                    isDarkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-100'
                ]">
                    <h3 :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'" class="text-sm font-semibold uppercase tracking-wider mb-4">Top Products</h3>
                    <div v-if="chartData.top_products.length > 0">
                        <div v-for="product in chartData.top_products" :key="product.product_id" :class="[
                            'flex items-center justify-between py-3 border-b last:border-0',
                            isDarkMode ? 'border-gray-700' : 'border-gray-100'
                        ]">
                            <div>
                                <p :class="isDarkMode ? 'text-white' : 'text-gray-900'" class="text-sm font-medium">{{ product.product_name }}</p>
                                <p :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'" class="text-xs">Product #{{ product.product_id }}</p>
                            </div>
                            <div class="text-right">
                                <p :class="isDarkMode ? 'text-white' : 'text-gray-900'" class="text-sm font-semibold">${{ formatNumber(product.total_revenue) }}</p>
                                <p :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'" class="text-xs">{{ product.total_quantity }} units sold</p>
                            </div>
                        </div>
                    </div>
                    <div v-else class="text-center py-4 text-sm" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">
                        No products sold yet
                    </div>
                </div>
            </div>

            <!-- Recent Orders -->
            <div :class="[
                'rounded-xl shadow-sm border overflow-hidden',
                isDarkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-100'
            ]">
                <div :class="[
                    'px-6 py-4 border-b flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2',
                    isDarkMode ? 'border-gray-700 bg-gray-700/50' : 'border-gray-100 bg-gray-50/50'
                ]">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5" :class="isDarkMode ? 'text-gray-400' : 'text-gray-600'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                        <h2 :class="isDarkMode ? 'text-white' : 'text-gray-900'" class="text-lg font-semibold">Recent Orders</h2>
                        <span :class="isDarkMode ? 'bg-gray-700 text-gray-300' : 'bg-gray-100 text-gray-500'" class="text-xs px-2 py-1 rounded-full">{{ recentOrders.length }} orders</span>
                    </div>
                    <router-link to="/admin/orders" class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-medium flex items-center gap-1 transition-colors">
                        View All
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </router-link>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[600px]">
                        <thead>
                            <tr :class="[
                                'text-left text-xs uppercase border-b',
                                isDarkMode ? 'text-gray-400 border-gray-700 bg-gray-700/30' : 'text-gray-500 border-gray-100 bg-gray-50'
                            ]">
                                <th class="px-6 py-3 font-semibold">Order #</th>
                                <th class="px-6 py-3 font-semibold">Customer</th>
                                <th class="px-6 py-3 text-right font-semibold">Total</th>
                                <th class="px-6 py-3 font-semibold">Status</th>
                                <th class="px-6 py-3 font-semibold hidden sm:table-cell">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="recentOrders.length === 0" class="text-center">
                                <td colspan="5" class="px-6 py-8" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">
                                    <svg class="w-12 h-12 mx-auto mb-2" :class="isDarkMode ? 'text-gray-600' : 'text-gray-300'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                    </svg>
                                    <p class="font-medium">No orders yet</p>
                                    <p class="text-xs mt-1">Orders will appear here once you start selling</p>
                                </td>
                            </tr>
                            <tr 
                                v-for="order in recentOrders" 
                                :key="order.id" 
                                :class="[
                                    'border-b transition-colors',
                                    isDarkMode ? 'border-gray-700 hover:bg-gray-700/30' : 'border-gray-100 hover:bg-gray-50'
                                ]"
                            >
                                <td class="px-6 py-3">
                                    <router-link :to="`/admin/orders/${order.id}`" class="text-sm font-medium text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 hover:underline">
                                        {{ order.order_number }}
                                    </router-link>
                                </td>
                                <td class="px-6 py-3">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center flex-shrink-0">
                                            <span class="text-xs font-semibold text-blue-600 dark:text-blue-400">{{ getInitials(order.customer_name) }}</span>
                                        </div>
                                        <div>
                                            <span :class="isDarkMode ? 'text-white' : 'text-gray-900'" class="text-sm">{{ order.customer_name || 'N/A' }}</span>
                                            <span :class="isDarkMode ? 'text-gray-400' : 'text-gray-400'" class="text-xs block">{{ order.customer_email }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-3 text-right">
                                    <span :class="isDarkMode ? 'text-white' : 'text-gray-900'" class="text-sm font-semibold">${{ formatNumber(order.total_amount) }}</span>
                                </td>
                                <td class="px-6 py-3">
                                    <span class="text-xs px-2 py-1 rounded-full font-medium" :class="getStatusClass(order.status)">
                                        {{ formatStatus(order.status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-3 hidden sm:table-cell">
                                    <div>
                                        <span :class="isDarkMode ? 'text-gray-300' : 'text-gray-500'" class="text-sm">{{ formatDate(order.created_at) }}</span>
                                        <span :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'" class="text-xs block">{{ order.created_at_human }}</span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Quick Stats Footer -->
                <div v-if="recentOrders.length > 0" :class="[
                    'px-6 py-3 border-t flex flex-col sm:flex-row items-center justify-between gap-2',
                    isDarkMode ? 'border-gray-700 bg-gray-700/30' : 'border-gray-100 bg-gray-50/50'
                ]">
                    <span :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'" class="text-sm">
                        Showing <span class="font-medium">{{ recentOrders.length }}</span> most recent orders
                    </span>
                    <div class="flex items-center gap-4 text-sm">
                        <span :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">
                            <span class="font-medium text-green-600 dark:text-green-400">{{ getCompletedCount }}</span> completed
                        </span>
                        <span :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">
                            <span class="font-medium text-yellow-600 dark:text-yellow-400">{{ getPendingCount }}</span> pending
                        </span>
                        <span :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">
                            <span class="font-medium text-blue-600 dark:text-blue-400">{{ getProcessingCount }}</span> processing
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import axios from 'axios';
import { useAuthStore } from '../../store';

export default {
    setup() {
        const stats = ref({});
        const recentOrders = ref([]);
        const chartData = ref({
            sales_last_7_days: [],
            sales_last_6_months: [],
            orders_by_status: [],
            top_products: []
        });
        const loading = ref(false);
        const isDarkMode = ref(false);
        let darkModeObserver = null;

        const authStore = useAuthStore();

        const getStatusClass = (status) => {
            const classes = {
                pending: 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300',
                processing: 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300',
                completed: 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300',
                cancelled: 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300',
                shipped: 'bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-300',
                delivered: 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300',
                paid: 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300',
                failed: 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300',
                refunded: 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300'
            };
            return classes[status?.toLowerCase()] || 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300';
        };

        const getStatusDotColor = (status) => {
            const colors = {
                pending: 'bg-yellow-500 dark:bg-yellow-400',
                processing: 'bg-blue-500 dark:bg-blue-400',
                completed: 'bg-green-500 dark:bg-green-400',
                cancelled: 'bg-red-500 dark:bg-red-400',
                shipped: 'bg-purple-500 dark:bg-purple-400',
                delivered: 'bg-green-500 dark:bg-green-400'
            };
            return colors[status?.toLowerCase()] || 'bg-gray-500 dark:bg-gray-400';
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

        const getInitials = (name) => {
            if (!name) return '?';
            return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2);
        };

        const getBarHeight = (value, data) => {
            const max = Math.max(...data.map(d => parseFloat(d.total || 0)));
            if (max === 0) return 10;
            const percentage = (parseFloat(value || 0) / max) * 100;
            return Math.max(10, (percentage / 100) * 120);
        };

        const getCompletedCount = computed(() => {
            return recentOrders.value.filter(o => o.status === 'completed' || o.status === 'delivered').length;
        });

        const getPendingCount = computed(() => {
            return recentOrders.value.filter(o => o.status === 'pending').length;
        });

        const getProcessingCount = computed(() => {
            return recentOrders.value.filter(o => o.status === 'processing').length;
        });

        const getWeeklyTotal = computed(() => {
            return chartData.value.sales_last_7_days.reduce((sum, d) => sum + parseFloat(d.total || 0), 0);
        });

        const getWeeklyOrders = computed(() => {
            return chartData.value.sales_last_7_days.reduce((sum, d) => sum + (d.orders || 0), 0);
        });

        const getMonthlyTotal = computed(() => {
            return chartData.value.sales_last_6_months.reduce((sum, d) => sum + parseFloat(d.total || 0), 0);
        });

        const getMonthlyOrders = computed(() => {
            return chartData.value.sales_last_6_months.reduce((sum, d) => sum + (d.orders || 0), 0);
        });

        const getStatusPercentage = (count) => {
            const total = stats.value.total_orders || 0;
            if (total === 0) return 0;
            return Math.round((count / total) * 100);
        };

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

        const fetchDashboardData = async () => {
            loading.value = true;
            try {
                const response = await axios.get('/api/admin/dashboard');
                if (response.data.success) {
                    const data = response.data.data;
                    stats.value = data.stats || {};
                    recentOrders.value = data.recent_orders || [];
                    chartData.value = data.charts || {
                        sales_last_7_days: [],
                        sales_last_6_months: [],
                        orders_by_status: [],
                        top_products: []
                    };
                }
            } catch (error) {
                console.error('Error fetching dashboard data:', error);
                showToast('Failed to load dashboard data', 'error');
            } finally {
                loading.value = false;
            }
        };

        const refreshData = () => {
            fetchDashboardData();
            showToast('Dashboard refreshed', 'success');
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
                max-width: 90vw;
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

        return {
            stats,
            recentOrders,
            chartData,
            loading,
            isDarkMode,
            getStatusClass,
            getStatusDotColor,
            formatStatus,
            formatNumber,
            formatDate,
            getInitials,
            getBarHeight,
            getCompletedCount,
            getPendingCount,
            getProcessingCount,
            getWeeklyTotal,
            getWeeklyOrders,
            getMonthlyTotal,
            getMonthlyOrders,
            getStatusPercentage,
            refreshData,
            authStore
        };
    }
};
</script>

<style scoped>
.dashboard-page {
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 1rem;
}

@media (max-width: 640px) {
    .dashboard-page {
        padding: 0 0.5rem;
    }
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
        transform: translateY(-20px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

/* Custom scrollbar */
.overflow-x-auto::-webkit-scrollbar {
    height: 4px;
}

.overflow-x-auto::-webkit-scrollbar-track {
    background: transparent;
}

.overflow-x-auto::-webkit-scrollbar-thumb {
    background: #d1d5db;
    border-radius: 2px;
}

.overflow-x-auto::-webkit-scrollbar-thumb:hover {
    background: #9ca3af;
}

/* Dark mode scrollbar */
.dark-mode .overflow-x-auto::-webkit-scrollbar-thumb {
    background: #4b5563;
}

.dark-mode .overflow-x-auto::-webkit-scrollbar-thumb:hover {
    background: #6b7280;
}

/* Hover effect for stat cards */
.bg-white.rounded-xl,
.bg-gray-800.rounded-xl {
    transition: all 0.3s ease;
}

.bg-white.rounded-xl:hover,
.bg-gray-800.rounded-xl:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 40px rgba(0,0,0,0.08);
}

.dark-mode .bg-gray-800.rounded-xl:hover {
    box-shadow: 0 10px 40px rgba(0,0,0,0.3);
}

/* Chart bar animations */
.bg-blue-100, .bg-green-100 {
    transition: height 0.8s ease;
}

/* Smooth transitions */
.transition-all {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 300ms;
}
</style>