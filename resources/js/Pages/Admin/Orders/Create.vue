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
      <router-link to="/admin/orders" :class="isDarkMode ? 'text-gray-400 hover:text-gray-200' : 'text-gray-500 hover:text-gray-700'">
        Orders
      </router-link>
      <span :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'">/</span>
      <span :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">Create Order</span>
    </nav>

    <!-- Header -->
    <div class="flex items-center gap-4">
      <router-link to="/admin/orders" class="p-2 rounded-lg transition-colors" :class="isDarkMode ? 'hover:bg-gray-700 text-gray-400' : 'hover:bg-gray-100 text-gray-600'" aria-label="Back to orders">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
      </router-link>
      <div>
        <h1 class="text-2xl md:text-3xl font-bold" :class="isDarkMode ? 'text-white' : 'text-gray-900'">Create New Order</h1>
        <p class="text-sm" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Add a new order to your system</p>
      </div>
    </div>

    <!-- Main Form -->
    <div :class="[
      'rounded-2xl shadow-lg p-4 sm:p-6',
      isDarkMode ? 'bg-gray-800' : 'bg-white'
    ]">
      <form @submit.prevent="submitOrder">
        <!-- Order Header Information -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
          <!-- Customer Selection -->
          <div class="md:col-span-2">
            <label class="block text-sm font-medium mb-1" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">Customer *</label>
            <div class="flex flex-col sm:flex-row gap-2">
              <select v-model="form.customer_id" @change="handleCustomerSelect" 
                :class="[
                  'flex-1 border rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors',
                  isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'
                ]" required>
                <option value="">Select Customer</option>
                <option v-for="customer in customers" :key="customer.id" :value="customer.id">
                  {{ customer.name }} ({{ customer.email }})
                </option>
              </select>
              <button type="button" @click="openCustomerModal" 
                class="px-3 py-2.5 bg-blue-600 from-green-500 to-green-600 text-white rounded-xl hover:from-green-600 hover:to-green-700 transition-all duration-200 shadow-md whitespace-nowrap">
                + New
              </button>
            </div>
            <p v-if="errors.customer_id" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ errors.customer_id }}</p>
          </div>

          <!-- Order Date -->
          <div>
            <label class="block text-sm font-medium mb-1" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">Order Date</label>
            <input type="date" v-model="form.order_date" 
              :class="[
                'w-full border rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors',
                isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'
              ]">
          </div>

          <!-- Due Date -->
          <div>
            <label class="block text-sm font-medium mb-1" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">Due Date</label>
            <input type="date" v-model="form.due_date" 
              :class="[
                'w-full border rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors',
                isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'
              ]"
              :min="new Date().toISOString().split('T')[0]">
          </div>
        </div>

        <!-- Products Section -->
        <div class="mb-6">
          <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 mb-4">
            <h2 class="text-lg font-semibold" :class="isDarkMode ? 'text-white' : 'text-gray-800'">Products</h2>
            <span class="text-sm px-3 py-1 rounded-full" :class="isDarkMode ? 'bg-gray-700 text-gray-300' : 'bg-gray-100 text-gray-600'">
              {{ form.items.length }} products selected
            </span>
          </div>

          <!-- Product Filters -->
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mb-4">
            <div class="sm:col-span-2">
              <input type="text" v-model="filters.search" 
                placeholder="Search by name or SKU..." 
                :class="[
                  'w-full border rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors',
                  isDarkMode ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-400' : 'bg-white border-gray-300 text-gray-900'
                ]"
                @input="debouncedSearch">
            </div>
            <div>
              <select v-model="filters.category" @change="fetchProducts" 
                :class="[
                  'w-full border rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors',
                  isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'
                ]">
                <option value="">All Categories</option>
                <option v-for="category in categories" :key="category.id" :value="category.id">
                  {{ category.name }}
                </option>
              </select>
            </div>
            <div>
              <select v-model="filters.sort_by" @change="fetchProducts" 
                :class="[
                  'w-full border rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors',
                  isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'
                ]">
                <option value="name">Sort by Name</option>
                <option value="price_asc">Price: Low to High</option>
                <option value="price_desc">Price: High to Low</option>
                <option value="created_at">Newest First</option>
              </select>
            </div>
          </div>

          <!-- Products Table -->
          <div class="overflow-x-auto border rounded-xl" :class="isDarkMode ? 'border-gray-700' : 'border-gray-200'">
            <table class="min-w-full divide-y" :class="isDarkMode ? 'divide-gray-700' : 'divide-gray-200'">
              <thead :class="isDarkMode ? 'bg-gray-700/50' : 'bg-gray-50'">
                <tr>
                  <th class="px-3 py-3 text-left text-xs font-medium uppercase tracking-wider" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Product</th>
                  <th class="px-3 py-3 text-left text-xs font-medium uppercase tracking-wider hidden sm:table-cell" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">SKU</th>
                  <th class="px-3 py-3 text-left text-xs font-medium uppercase tracking-wider hidden md:table-cell" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Category</th>
                  <th class="px-3 py-3 text-left text-xs font-medium uppercase tracking-wider" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Price</th>
                  <th class="px-3 py-3 text-left text-xs font-medium uppercase tracking-wider hidden lg:table-cell" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Stock</th>
                  <th class="px-3 py-3 text-left text-xs font-medium uppercase tracking-wider" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Qty</th>
                  <th class="px-3 py-3 text-left text-xs font-medium uppercase tracking-wider hidden sm:table-cell" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Discount</th>
                  <th class="px-3 py-3 text-left text-xs font-medium uppercase tracking-wider" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Total</th>
                </tr>
              </thead>
              <tbody class="divide-y" :class="isDarkMode ? 'divide-gray-700 bg-gray-800' : 'divide-gray-200 bg-white'">
                <tr v-if="loadingProducts" class="text-center">
                  <td colspan="8" class="px-3 py-8">
                    <div class="flex justify-center items-center">
                      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                      <span class="ml-2" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Loading products...</span>
                    </div>
                  </td>
                </tr>
                <tr v-else-if="paginatedProducts.length === 0" class="text-center">
                  <td colspan="8" class="px-3 py-8" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">No products found matching your criteria.</td>
                </tr>
                <tr v-for="product in paginatedProducts" :key="product.id" 
                  :class="{'bg-blue-50 dark:bg-blue-900/20': getProductQuantity(product.id) > 0}">
                  <td class="px-3 py-3">
                    <div class="text-sm font-medium" :class="isDarkMode ? 'text-white' : 'text-gray-900'">{{ product.name }}</div>
                    <div class="text-xs sm:hidden" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">SKU: {{ product.sku }}</div>
                  </td>
                  <td class="px-3 py-3 hidden sm:table-cell">
                    <div class="text-sm" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">{{ product.sku }}</div>
                  </td>
                  <td class="px-3 py-3 hidden md:table-cell">
                    <div class="text-sm" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">{{ product.category?.name || 'Uncategorized' }}</div>
                  </td>
                  <td class="px-3 py-3">
                    <div class="text-sm font-semibold" :class="isDarkMode ? 'text-white' : 'text-gray-900'">${{ parseFloat(product.sale_price).toFixed(2) }}</div>
                  </td>
                  <td class="px-3 py-3 hidden lg:table-cell">
                    <span class="text-sm" :class="product.stock_quantity > 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'">
                      {{ product.stock_quantity > 0 ? product.stock_quantity + ' in stock' : 'Out of stock' }}
                    </span>
                  </td>
                  <td class="px-3 py-3">
                    <input type="number" min="0" 
                      v-model="product.input_quantity" 
                      @input="updateProductQuantity(product)"
                      :class="[
                        'w-16 sm:w-20 border rounded-xl px-2 py-1 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors',
                        isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'
                      ]">
                  </td>
                  <td class="px-3 py-3 hidden sm:table-cell">
                    <div class="flex items-center">
                      <input type="number" min="0" max="100" step="0.01" 
                        v-model="product.input_discount" 
                        @input="updateProductDiscount(product)"
                        :class="[
                          'w-14 sm:w-16 border rounded-xl px-2 py-1 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors',
                          isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'
                        ]">
                      <span class="ml-1 text-sm" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">%</span>
                    </div>
                  </td>
                  <td class="px-3 py-3">
                    <span class="text-sm font-semibold" :class="getProductQuantity(product.id) > 0 ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500'">
                      ${{ calculateProductTotal(product).toFixed(2) }}
                    </span>
                    <div class="sm:hidden text-xs mt-1" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">
                      <span v-if="parseFloat(product.input_discount) > 0">
                        -{{ product.input_discount }}%
                      </span>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <div class="flex flex-col sm:flex-row justify-between items-center gap-3 mt-4">
            <div class="text-sm" :class="isDarkMode ? 'text-gray-400' : 'text-gray-700'">
              Showing {{ (currentPage - 1) * perPage + 1 }} to {{ Math.min(currentPage * perPage, totalProducts) }} of {{ totalProducts }} products
            </div>
            <div class="flex space-x-2">
              <button @click="changePage(currentPage - 1)" 
                :disabled="currentPage <= 1"
                :class="[
                  'px-3 py-1 border rounded-md text-sm transition-colors disabled:opacity-50 disabled:cursor-not-allowed',
                  isDarkMode ? 'border-gray-600 text-gray-300 hover:bg-gray-700' : 'border-gray-300 text-gray-700 hover:bg-gray-100'
                ]">
                Previous
              </button>
              <span class="px-3 py-1 text-sm" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">{{ currentPage }} / {{ totalPages }}</span>
              <button @click="changePage(currentPage + 1)" 
                :disabled="currentPage >= totalPages"
                :class="[
                  'px-3 py-1 border rounded-md text-sm transition-colors disabled:opacity-50 disabled:cursor-not-allowed',
                  isDarkMode ? 'border-gray-600 text-gray-300 hover:bg-gray-700' : 'border-gray-300 text-gray-700 hover:bg-gray-100'
                ]">
                Next
              </button>
            </div>
          </div>
        </div>

        <!-- Addresses and Payment -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
          <!-- Shipping Address -->
          <div>
            <label class="block text-sm font-medium mb-1" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">Shipping Address *</label>
            <textarea v-model="form.shipping_address" 
              :class="[
                'w-full border rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors',
                isDarkMode ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-400' : 'bg-white border-gray-300 text-gray-900'
              ]" 
              rows="3" required placeholder="Enter shipping address"></textarea>
            <p v-if="errors.shipping_address" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ errors.shipping_address }}</p>
          </div>

          <!-- Billing Address -->
          <div>
            <label class="block text-sm font-medium mb-1" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">Billing Address</label>
            <textarea v-model="form.billing_address" 
              :class="[
                'w-full border rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors',
                isDarkMode ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-400' : 'bg-white border-gray-300 text-gray-900'
              ]" 
              rows="3" placeholder="Enter billing address"></textarea>
            <div class="mt-2">
              <input type="checkbox" v-model="form.same_as_shipping" @change="copyShippingAddress" 
                :class="[
                  'h-4 w-4 rounded focus:ring-2 focus:ring-blue-500',
                  isDarkMode ? 'bg-gray-700 border-gray-500' : 'bg-white border-gray-300'
                ]">
              <label class="ml-2 text-sm" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">Same as shipping address</label>
            </div>
          </div>

          <!-- Payment Method -->
          <div>
            <label class="block text-sm font-medium mb-1" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">Payment Method *</label>
            <select v-model="form.payment_method" 
              :class="[
                'w-full border rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors',
                isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'
              ]" required>
              <option value="">Select Payment Method</option>
              <option value="stripe">Stripe</option>
              <option value="cash">Cash</option>
              <option value="bank_transfer">Bank Transfer</option>
              <option value="credit_card">Credit Card</option>
              <option value="paypal">PayPal</option>
            </select>
            <p v-if="errors.payment_method" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ errors.payment_method }}</p>
          </div>

          <!-- Payment Status -->
          <div>
            <label class="block text-sm font-medium mb-1" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">Payment Status</label>
            <select v-model="form.payment_status" 
              :class="[
                'w-full border rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors',
                isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'
              ]">
              <option value="pending">Pending</option>
              <option value="paid">Paid</option>
              <option value="partial">Partial</option>
            </select>
          </div>

          <!-- Order Notes -->
          <div class="lg:col-span-2">
            <label class="block text-sm font-medium mb-1" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">Order Notes</label>
            <textarea v-model="form.notes" 
              :class="[
                'w-full border rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors',
                isDarkMode ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-400' : 'bg-white border-gray-300 text-gray-900'
              ]" 
              rows="2" placeholder="Any additional notes about this order"></textarea>
          </div>
        </div>

        <!-- Order Totals -->
        <div class="border-t pt-4 mb-6" :class="isDarkMode ? 'border-gray-700' : 'border-gray-200'">
          <div class="flex flex-col items-end">
            <div class="w-full md:w-80 lg:w-96 space-y-2">
              <div class="flex justify-between py-1">
                <span class="font-medium" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">Subtotal:</span>
                <span class="font-medium" :class="isDarkMode ? 'text-white' : 'text-gray-900'">${{ subtotal.toFixed(2) }}</span>
              </div>
              <div class="flex justify-between py-1">
                <span class="font-medium text-green-600 dark:text-green-400">Total Discount:</span>
                <span class="font-medium text-green-600 dark:text-green-400">-${{ totalDiscount.toFixed(2) }}</span>
              </div>
              
              <!-- Shipping Cost -->
              <div class="flex justify-between items-center py-1">
                <span class="font-medium" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">Shipping Cost:</span>
                <div class="flex items-center">
                  <span class="mr-1" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">$</span>
                  <input type="number" min="0" step="0.01" 
                    v-model="form.shipping_cost" 
                    @input="updateTotals"
                    :class="[
                      'w-24 border rounded-xl px-2 py-1 text-right text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors',
                      isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'
                    ]">
                </div>
              </div>

              <!-- Tax Amount -->
              <div class="flex justify-between py-1">
                <span class="font-medium" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">
                  Tax 
                  <span v-if="form.tax_type === 'percentage'">({{ form.tax_rate }}%)</span>
                  <span v-else-if="form.tax_type === 'fixed'">(Fixed)</span>
                </span>
                <span class="font-medium text-amber-600 dark:text-amber-400">${{ form.tax_amount.toFixed(2) }}</span>
              </div>

              <!-- Grand Total -->
              <div class="flex justify-between py-2 border-t-2" :class="isDarkMode ? 'border-gray-600' : 'border-gray-300'">
                <span class="font-bold text-lg" :class="isDarkMode ? 'text-white' : 'text-gray-900'">Grand Total:</span>
                <span class="font-bold text-xl text-blue-600 dark:text-blue-400">${{ grandTotal.toFixed(2) }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Form Actions -->
        <div class="flex flex-col sm:flex-row justify-end gap-3 border-t pt-6" :class="isDarkMode ? 'border-gray-700' : 'border-gray-200'">
          <router-link to="/admin/orders" 
            :class="[
              'px-6 py-2.5 rounded-xl transition-colors text-center',
              isDarkMode ? 'bg-gray-700 text-gray-300 hover:bg-gray-600' : 'bg-gray-200 text-gray-800 hover:bg-gray-300'
            ]">
            Cancel
          </router-link>
          <button type="submit" 
            class="px-6 py-2.5 bg-blue-600 from-primary-500 to-primary-600 text-white rounded-xl hover:from-primary-600 hover:to-primary-700 transition-all duration-200 shadow-md disabled:opacity-50 disabled:cursor-not-allowed"
            :disabled="loading || form.items.length === 0">
            <span v-if="loading">Creating Order...</span>
            <span v-else>Create Order ({{ form.items.length }} items)</span>
          </button>
        </div>
      </form>
    </div>

    <!-- Customer Modal -->
    <CustomerModal 
      v-if="showCustomerModal" 
      @close="closeCustomerModal" 
      @created="customerCreated" 
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import CustomerModal from '../../../components/Modals/CustomerModal.vue';

const router = useRouter();
const loading = ref(false);
const loadingProducts = ref(false);
const showCustomerModal = ref(false);
const isDarkMode = ref(false);
let darkModeObserver = null;

const customers = ref([]);
const allProducts = ref([]);
const categories = ref([]);
const errors = ref({});

// Pagination
const currentPage = ref(1);
const perPage = ref(10);
const totalProducts = ref(0);

// Filters
const filters = ref({
  search: '',
  category: '',
  sort_by: 'name'
});

let searchTimeout = null;

// Form Data
const form = ref({
  customer_id: '',
  due_date: '',
  order_date: new Date().toISOString().split('T')[0],
  shipping_address: '',
  billing_address: '',
  same_as_shipping: false,
  payment_method: '',
  payment_status: 'pending',
  notes: '',
  items: [],
  shipping_cost: 0,
  tax_rate: 0,
  tax_type: 'percentage',
  tax_amount: 0
});

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

// Computed Properties
const paginatedProducts = computed(() => {
  return allProducts.value;
});

const totalPages = computed(() => {
  return Math.ceil(totalProducts.value / perPage.value);
});

const subtotal = computed(() => {
  return form.value.items.reduce((sum, item) => {
    return sum + (item.price * item.quantity);
  }, 0);
});

const totalDiscount = computed(() => {
  return form.value.items.reduce((sum, item) => {
    const itemTotal = item.price * item.quantity;
    const discountAmount = (itemTotal * item.discount) / 100;
    return sum + discountAmount;
  }, 0);
});

const grandTotal = computed(() => {
  return subtotal.value - totalDiscount.value + 
         parseFloat(form.value.shipping_cost || 0) + 
         parseFloat(form.value.tax_amount || 0);
});

// Methods
const handleCustomerSelect = () => {
  if (form.value.customer_id) {
    const customer = customers.value.find(c => c.id === parseInt(form.value.customer_id));
    if (customer) {
      if (customer.address) {
        form.value.shipping_address = customer.address;
        form.value.billing_address = customer.address;
        form.value.same_as_shipping = true;
      } else {
          form.value.shipping_address = null;
        form.value.billing_address = null;
        form.value.same_as_shipping = false;
      }
      if (customer.tax_rate !== undefined && customer.tax_rate !== null) {
        form.value.tax_rate = parseFloat(customer.tax_rate);
      }
      if (customer.tax_type) {
        form.value.tax_type = customer.tax_type;
      }
      updateTaxAmount();
    }
  }
};

const updateTaxAmount = () => {
  const taxableAmount = subtotal.value - totalDiscount.value;
  
  if (form.value.tax_type === 'percentage') {
    form.value.tax_amount = (taxableAmount * (form.value.tax_rate || 0)) / 100;
  } else if (form.value.tax_type === 'fixed') {
    form.value.tax_amount = parseFloat(form.value.tax_rate || 0);
  } else {
    form.value.tax_amount = 0;
  }
};

const updateTotals = () => {
  updateTaxAmount();
};

const getProductQuantity = (productId) => {
  const item = form.value.items.find(item => item.product_id === productId);
  return item ? item.quantity : 0;
};

const calculateProductTotal = (product) => {
  const quantity = parseInt(product.input_quantity) || 0;
  const discount = parseFloat(product.input_discount) || 0;
  const price = parseFloat(product.sale_price);
  const total = price * quantity;
  const discountAmount = (total * discount) / 100;
  return total - discountAmount;
};

const updateProductQuantity = (product) => {
  const quantity = parseInt(product.input_quantity) || 0;
  const discount = parseFloat(product.input_discount) || 0;
  
  if (quantity > 0) {
    const existingItem = form.value.items.find(item => item.product_id === product.id);
    if (existingItem) {
      existingItem.quantity = quantity;
      existingItem.discount = discount;
    } else {
      form.value.items.push({
        product_id: product.id,
        name: product.name,
        sku: product.sku,
        price: parseFloat(product.sale_price),
        quantity: quantity,
        discount: discount
      });
    }
  } else {
    const index = form.value.items.findIndex(item => item.product_id === product.id);
    if (index > -1) {
      form.value.items.splice(index, 1);
    }
  }
  updateTaxAmount();
};

const updateProductDiscount = (product) => {
  const quantity = parseInt(product.input_quantity) || 0;
  const discount = parseFloat(product.input_discount) || 0;
  
  if (quantity > 0) {
    const existingItem = form.value.items.find(item => item.product_id === product.id);
    if (existingItem) {
      existingItem.discount = discount;
      updateTaxAmount();
    }
  }
};

const debouncedSearch = () => {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    currentPage.value = 1;
    fetchProducts();
  }, 300);
};

const fetchCategories = async () => {
  try {
    const response = await axios.get('/api/admin/categories/active');
    categories.value = response.data.data || response.data || [];
  } catch (error) {
    console.error('Error loading categories:', error);
  }
};

const fetchProducts = async () => {
  loadingProducts.value = true;
  try {
    const params = {
      page: currentPage.value,
      per_page: perPage.value,
      search: filters.value.search,
      category_id: filters.value.category,
      sort_by: filters.value.sort_by,
      is_active: true,
      my: true,
    };

    const response = await axios.get('/api/admin/products', { params });
    const data = response.data.data || response.data;
    
    if (response.data.tax) {
      form.value.tax_rate = parseFloat(response.data.tax.rate) || 0;
      form.value.tax_type = response.data.tax.type || 'percentage';
    } else if (data.tax) {
      form.value.tax_rate = parseFloat(data.tax.rate) || 0;
      form.value.tax_type = data.tax.type || 'percentage';
    }
    
    if (data.data) {
      allProducts.value = data.data.map(product => {
        const existingItem = form.value.items.find(item => item.product_id === product.id);
        return {
          ...product,
          input_quantity: existingItem ? existingItem.quantity : 0,
          input_discount: existingItem ? existingItem.discount : 0
        };
      });
      totalProducts.value = data.total || data.data.length;
    } else {
      allProducts.value = (data || []).map(product => {
        const existingItem = form.value.items.find(item => item.product_id === product.id);
        return {
          ...product,
          input_quantity: existingItem ? existingItem.quantity : 0,
          input_discount: existingItem ? existingItem.discount : 0
        };
      });
      totalProducts.value = allProducts.value.length;
    }
    updateTaxAmount();
  } catch (error) {
    console.error('Error loading products:', error);
    showToast('Failed to load products', 'error');
  } finally {
    loadingProducts.value = false;
  }
};

const changePage = (page) => {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page;
    fetchProducts();
  }
};

const loadCustomers = async () => {
  try {
    const response = await axios.get('/api/admin/customers');
    customers.value = response.data.data.data || response.data || [];
  } catch (error) {
    console.error('Error loading customers:', error);
    showToast('Failed to load customers', 'error');
  }
};

const openCustomerModal = () => {
  showCustomerModal.value = true;
};

const closeCustomerModal = () => {
  showCustomerModal.value = false;
};

const copyShippingAddress = () => {
  if (form.value.same_as_shipping) {
    form.value.billing_address = form.value.shipping_address;
  }
};

const customerCreated = (customer) => {
  customers.value.push(customer);
  form.value.customer_id = customer.id;
  showCustomerModal.value = false;
  if (customer.address) {
    form.value.shipping_address = customer.address;
    form.value.billing_address = customer.address;
    form.value.same_as_shipping = true;
  }
  if (customer.tax_rate !== undefined && customer.tax_rate !== null) {
    form.value.tax_rate = parseFloat(customer.tax_rate);
  }
  if (customer.tax_type) {
    form.value.tax_type = customer.tax_type;
  }
  updateTaxAmount();
  showToast('Customer created and selected!', 'success');
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

const submitOrder = async () => {
  loading.value = true;
  errors.value = {};

  try {
    if (!form.value.customer_id) {
      errors.value.customer_id = 'Please select a customer';
      loading.value = false;
      return;
    }

    if (!form.value.shipping_address) {
      errors.value.shipping_address = 'Shipping address is required';
      loading.value = false;
      return;
    }

    if (!form.value.payment_method) {
      errors.value.payment_method = 'Payment method is required';
      loading.value = false;
      return;
    }

    if (form.value.items.length === 0) {
      showToast('Please add at least one product (set quantity > 0)', 'error');
      loading.value = false;
      return;
    }

    const orderData = {
      customer_id: form.value.customer_id,
      due_date: form.value.due_date || null,
      order_date: form.value.order_date,
      shipping_address: form.value.shipping_address,
      billing_address: form.value.billing_address || form.value.shipping_address,
      payment_method: form.value.payment_method,
      payment_status: form.value.payment_status,
      notes: form.value.notes,
      items: form.value.items.map(item => ({
        product_id: item.product_id,
        quantity: item.quantity,
        price: item.price,
        discount: item.discount
      })),
      subtotal: subtotal.value,
      discount_total: totalDiscount.value,
      shipping_cost: parseFloat(form.value.shipping_cost) || 0,
      tax_rate: form.value.tax_rate || 0,
      tax_type: form.value.tax_type || 'percentage',
      tax_amount: form.value.tax_amount || 0,
      total: grandTotal.value
    };

    const response = await axios.post('/api/admin/orders', orderData);

    if (response.data.success) {
      showToast('Order created successfully!', 'success');
      router.push(`/admin/orders/${response.data.data.id}`);
    } else {
      showToast(response.data.message || 'Failed to create order', 'error');
    }
  } catch (error) {
    console.error('Error creating order:', error);
    if (error.response?.data?.errors) {
      errors.value = error.response.data.errors;
      showToast('Please fix the validation errors', 'error');
    } else {
      showToast(error.response?.data?.message || 'Failed to create order', 'error');
    }
  } finally {
    loading.value = false;
  }
};

// Watchers
watch(() => form.value.shipping_address, (newVal) => {
  if (form.value.same_as_shipping) {
    form.value.billing_address = newVal;
  }
});

watch([subtotal, totalDiscount], () => {
  updateTaxAmount();
});

// Lifecycle
onMounted(() => {
  checkDarkMode();
  setupDarkModeWatcher();
  loadCustomers();
  fetchCategories();
  fetchProducts();

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
input[type="number"]::-webkit-inner-spin-button,
input[type="number"]::-webkit-outer-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

input[type="number"] {
  -moz-appearance: textfield;
}

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