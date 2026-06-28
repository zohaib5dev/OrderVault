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
      <span :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">Products</span>
    </nav>

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
      <div>
        <h1 class="text-2xl md:text-3xl font-bold" :class="isDarkMode ? 'text-white' : 'text-gray-900'">Products</h1>
        <p class="text-sm" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Manage your product inventory</p>
      </div>
      <router-link 
        to="/admin/products/create" 
        class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 from-primary-500 to-primary-600 text-white rounded-lg hover:from-primary-600 hover:to-primary-700 transition-all duration-200 shadow-md"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Add Product
      </router-link>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
      <div :class="[
        'rounded-2xl shadow-lg p-5 border-l-4 border-blue-500',
        isDarkMode ? 'bg-gray-800' : 'bg-white'
      ]">
        <p class="text-xs uppercase tracking-wider" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Total Products</p>
        <p class="text-2xl font-bold" :class="isDarkMode ? 'text-white' : 'text-gray-900'">{{ products.length }}</p>
      </div>
      <div :class="[
        'rounded-2xl shadow-lg p-5 border-l-4 border-green-500',
        isDarkMode ? 'bg-gray-800' : 'bg-white'
      ]">
        <p class="text-xs uppercase tracking-wider" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Active</p>
        <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ products.filter(p => p.is_active).length }}</p>
      </div>
      <div :class="[
        'rounded-2xl shadow-lg p-5 border-l-4 border-red-500',
        isDarkMode ? 'bg-gray-800' : 'bg-white'
      ]">
        <p class="text-xs uppercase tracking-wider" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Inactive</p>
        <p class="text-2xl font-bold text-red-600 dark:text-red-400">{{ products.filter(p => !p.is_active).length }}</p>
      </div>
      <div :class="[
        'rounded-2xl shadow-lg p-5 border-l-4 border-purple-500',
        isDarkMode ? 'bg-gray-800' : 'bg-white'
      ]">
        <p class="text-xs uppercase tracking-wider" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Total Value</p>
        <p class="text-2xl font-bold" :class="isDarkMode ? 'text-white' : 'text-gray-900'">${{ totalValue.toFixed(2) }}</p>
      </div>
    </div>

    <!-- Search -->
    <div :class="[
      'rounded-2xl shadow-lg',
      isDarkMode ? 'bg-gray-800' : 'bg-white'
    ]">
      <div class="p-4">
        <div class="relative">
          <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4" :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
          </svg>
          <input 
            type="text" 
            v-model="searchTerm" 
            placeholder="Search products by name..."
            :class="[
              'w-full pl-10 pr-4 py-2.5 border rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors',
              isDarkMode ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-400' : 'bg-white border-gray-200 text-gray-900 placeholder-gray-500'
            ]"
          />
        </div>
      </div>
    </div>

    <!-- Products Table / Cards -->
    <div :class="[
      'rounded-2xl shadow-lg overflow-hidden',
      isDarkMode ? 'bg-gray-800' : 'bg-white'
    ]">
      <!-- Desktop Table View -->
      <div class="hidden md:block overflow-x-auto">
        <table class="w-full">
          <thead :class="isDarkMode ? 'bg-gray-700/50' : 'bg-gray-50'">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Product</th>
              <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Price</th>
              <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Stock</th>
              <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Status</th>
              <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y" :class="isDarkMode ? 'divide-gray-700' : 'divide-gray-200'">
            <tr v-if="loading" class="text-center">
              <td colspan="5" class="px-6 py-12">
                <div class="flex justify-center items-center">
                  <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                  <span class="ml-2" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Loading products...</span>
                </div>
              </td>
            </tr>
            <tr v-else-if="filteredProducts.length === 0" class="text-center">
              <td colspan="5" class="px-6 py-12">
                <svg class="w-16 h-16 mx-auto mb-4" :class="isDarkMode ? 'text-gray-600' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                <p class="text-lg font-medium" :class="isDarkMode ? 'text-gray-300' : 'text-gray-600'">No products found</p>
                <p class="text-sm mt-1" :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'">Try adjusting your search or add a new product</p>
              </td>
            </tr>
            <tr v-for="product in filteredProducts" :key="product.id" class="transition-colors" :class="isDarkMode ? 'hover:bg-gray-700/50' : 'hover:bg-gray-50'">
              <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0" :class="isDarkMode ? 'bg-gray-700' : 'bg-gray-100'">
                    <svg class="w-5 h-5" :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                  </div>
                  <div>
                    <p class="text-sm font-medium" :class="isDarkMode ? 'text-white' : 'text-gray-900'">{{ product.name }}</p>
                    <p class="text-xs" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">{{ product.sku || 'No SKU' }}</p>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 text-sm font-semibold" :class="isDarkMode ? 'text-white' : 'text-gray-900'">${{ parseFloat(product.price).toFixed(2) }}</td>
              <td class="px-6 py-4">
                <span class="text-sm" :class="isDarkMode ? 'text-gray-300' : 'text-gray-900'">{{ product.stock_quantity }}</span>
                <span v-if="product.stock_quantity <= 5" class="ml-2 text-xs text-red-600 dark:text-red-400">(Low)</span>
              </td>
              <td class="px-6 py-4">
                <span class="px-2 py-1 text-xs font-semibold rounded-full" :class="product.is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400'">
                  {{ product.is_active ? 'Active' : 'Inactive' }}
                </span>
              </td>
              <td class="px-6 py-4">
                <div class="flex items-center justify-end gap-2">
                  <router-link :to="`/admin/products/${product.id}`" 
                    class="p-1.5 rounded-lg transition-colors"
                    :class="isDarkMode ? 'text-blue-400 hover:bg-blue-900/20' : 'text-blue-600 hover:bg-blue-50'"
                    title="View Product">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                  </router-link>
                  <router-link :to="`/admin/products/${product.id}/edit`" 
                    class="p-1.5 rounded-lg transition-colors"
                    :class="isDarkMode ? 'text-yellow-400 hover:bg-yellow-900/20' : 'text-yellow-600 hover:bg-yellow-50'"
                    title="Edit Product">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                  </router-link>
                  <button @click="openDeleteModal(product)" 
                    class="p-1.5 rounded-lg transition-colors"
                    :class="isDarkMode ? 'text-red-400 hover:bg-red-900/20' : 'text-red-600 hover:bg-red-50'"
                    title="Delete Product">
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
          <span class="ml-2" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Loading products...</span>
        </div>
        <div v-else-if="filteredProducts.length === 0" class="text-center py-12">
          <svg class="w-16 h-16 mx-auto mb-4" :class="isDarkMode ? 'text-gray-600' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
          </svg>
          <p class="text-lg font-medium" :class="isDarkMode ? 'text-gray-300' : 'text-gray-600'">No products found</p>
          <p class="text-sm mt-1" :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'">Try adjusting your search or add a new product</p>
        </div>
        <div v-for="product in filteredProducts" :key="product.id" class="p-4 space-y-3 transition-colors" :class="isDarkMode ? 'hover:bg-gray-700/50' : 'hover:bg-gray-50'">
          <!-- Product Header -->
          <div class="flex items-start justify-between">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0" :class="isDarkMode ? 'bg-gray-700' : 'bg-gray-100'">
                <svg class="w-5 h-5" :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
              </div>
              <div>
                <p class="text-sm font-medium" :class="isDarkMode ? 'text-white' : 'text-gray-900'">{{ product.name }}</p>
                <p class="text-xs" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">{{ product.sku || 'No SKU' }}</p>
              </div>
            </div>
            <span class="text-sm font-semibold" :class="isDarkMode ? 'text-white' : 'text-gray-900'">${{ parseFloat(product.price).toFixed(2) }}</span>
          </div>

          <!-- Product Details -->
          <div class="grid grid-cols-2 gap-2 rounded-lg p-3" :class="isDarkMode ? 'bg-gray-700/50' : 'bg-gray-50'">
            <div>
              <p class="text-xs" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Stock</p>
              <p class="text-sm font-medium" :class="isDarkMode ? 'text-white' : 'text-gray-900'">
                {{ product.stock_quantity }}
                <span v-if="product.stock_quantity <= 5" class="text-xs text-red-600 dark:text-red-400">(Low)</span>
              </p>
            </div>
            <div>
              <p class="text-xs" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Status</p>
              <span class="inline-flex px-2 py-0.5 text-xs font-semibold rounded-full" :class="product.is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400'">
                {{ product.is_active ? 'Active' : 'Inactive' }}
              </span>
            </div>
          </div>

          <!-- Actions -->
          <div class="flex items-center justify-end gap-3 pt-2 border-t" :class="isDarkMode ? 'border-gray-700' : 'border-gray-100'">
            <router-link :to="`/admin/products/${product.id}`" 
              class="p-1.5 rounded-lg transition-colors"
              :class="isDarkMode ? 'text-blue-400 hover:bg-blue-900/20' : 'text-blue-600 hover:bg-blue-50'"
              title="View Product">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
              </svg>
            </router-link>
            <router-link :to="`/admin/products/${product.id}/edit`" 
              class="p-1.5 rounded-lg transition-colors"
              :class="isDarkMode ? 'text-yellow-400 hover:bg-yellow-900/20' : 'text-yellow-600 hover:bg-yellow-50'"
              title="Edit Product">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
              </svg>
            </router-link>
            <button @click="openDeleteModal(product)" 
              class="p-1.5 rounded-lg transition-colors"
              :class="isDarkMode ? 'text-red-400 hover:bg-red-900/20' : 'text-red-600 hover:bg-red-50'"
              title="Delete Product">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
              </svg>
            </button>
          </div>
        </div>
      </div>

      <!-- Results Count -->
      <div class="px-6 py-3 border-t text-sm" :class="[isDarkMode ? 'border-gray-700 bg-gray-800/50 text-gray-400' : 'border-gray-200 bg-gray-50 text-gray-500']">
        Showing <span class="font-medium" :class="isDarkMode ? 'text-white' : 'text-gray-700'">{{ filteredProducts.length }}</span> of <span class="font-medium" :class="isDarkMode ? 'text-white' : 'text-gray-700'">{{ products.length }}</span> products
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
            <h3 class="text-lg font-semibold" :class="isDarkMode ? 'text-white' : 'text-gray-900'">Delete Product</h3>
            <p class="text-sm" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">This action cannot be undone</p>
          </div>
        </div>

        <div class="mb-6">
          <p class="text-sm" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">
            Are you sure you want to delete <span class="font-semibold" :class="isDarkMode ? 'text-white' : 'text-gray-900'">"{{ productToDelete?.name }}"</span>?
          </p>
          <p class="text-sm mt-2" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">
            This will permanently remove this product from your inventory.
          </p>
        </div>

        <div class="flex flex-col sm:flex-row justify-end gap-3">
          <button 
            @click="closeDeleteModal" 
            :class="[
              'px-4 py-2 rounded-lg transition-colors',
              isDarkMode ? 'bg-gray-700 text-gray-300 hover:bg-gray-600' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
            ]"
            :disabled="deleting"
          >
            Cancel
          </button>
          <button 
            @click="confirmDelete" 
            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
            :disabled="deleting"
          >
            <svg v-if="deleting" class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span v-if="deleting">Deleting...</span>
            <span v-else>Delete Product</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import axios from 'axios';

const products = ref([]);
const searchTerm = ref('');
const loading = ref(false);
const showDeleteModal = ref(false);
const productToDelete = ref(null);
const deleting = ref(false);
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

const totalValue = computed(() => {
  return products.value.reduce((sum, p) => sum + (parseFloat(p.price) * p.stock_quantity), 0);
});

const filteredProducts = computed(() => {
  if (!searchTerm.value.trim()) return products.value;
  const term = searchTerm.value.toLowerCase().trim();
  return products.value.filter(p => 
    p.name.toLowerCase().includes(term) ||
    (p.sku && p.sku.toLowerCase().includes(term))
  );
});

const fetchProducts = async () => {
  loading.value = true;
  try {
    const response = await axios.get('/api/admin/products', {
      params: { search: searchTerm.value, my: true }
    });
    products.value = response.data.data || [];
  } catch (error) {
    console.error('Error fetching products:', error);
    showToast('Failed to load products', 'error');
  } finally {
    loading.value = false;
  }
};

const openDeleteModal = (product) => {
  productToDelete.value = product;
  showDeleteModal.value = true;
};

const closeDeleteModal = () => {
  showDeleteModal.value = false;
  productToDelete.value = null;
  deleting.value = false;
};

const confirmDelete = async () => {
  if (!productToDelete.value) return;
  
  deleting.value = true;
  try {
    await axios.delete(`/api/admin/products/${productToDelete.value.id}`);
    showToast('Product deleted successfully', 'success');
    closeDeleteModal();
    await fetchProducts();
  } catch (error) {
    console.error('Error deleting product:', error);
    showToast(error.response?.data?.message || 'Failed to delete product', 'error');
    deleting.value = false;
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