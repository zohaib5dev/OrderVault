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
      <router-link to="/admin/products" :class="isDarkMode ? 'text-gray-400 hover:text-gray-200' : 'text-gray-500 hover:text-gray-700'">
        Products
      </router-link>
      <span :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'">/</span>
      <span class="truncate max-w-[150px] sm:max-w-xs" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">
        {{ product?.name || 'Product Details' }}
      </span>
    </nav>

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
      <div>
        <h1 class="text-2xl md:text-3xl font-bold" :class="isDarkMode ? 'text-white' : 'text-gray-900'">Product Details</h1>
        <p class="text-sm" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">View complete product information</p>
      </div>
      <div class="flex flex-wrap gap-2 w-full sm:w-auto">
        <router-link 
          to="/admin/products" 
          :class="[
            'px-4 py-2 rounded-lg transition-colors flex items-center gap-2 text-sm',
            isDarkMode ? 'bg-gray-700 text-gray-300 hover:bg-gray-600' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
          ]"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
          </svg>
          Back
        </router-link>
        <router-link 
          :to="`/admin/products/${product?.id}/edit`" 
          class="px-4 py-2 bg-blue-600 from-primary-500 to-primary-600 text-white rounded-lg hover:from-primary-600 hover:to-primary-700 transition-all duration-200 shadow-md flex items-center gap-2 text-sm"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
          </svg>
          Edit Product
        </router-link>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" :class="[
      'rounded-2xl shadow-lg p-12 text-center',
      isDarkMode ? 'bg-gray-800' : 'bg-white'
    ]">
      <div class="flex flex-col items-center justify-center">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
        <span class="mt-4" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Loading product...</span>
      </div>
    </div>

    <!-- Product Details -->
    <div v-else-if="product" class="space-y-6">
      <!-- Status Banner -->
      <div :class="[
        'rounded-2xl shadow-lg p-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4',
        isDarkMode ? 'bg-gray-800' : 'bg-white'
      ]">
        <div class="flex flex-wrap items-center gap-3">
          <span class="px-3 py-1 text-sm font-semibold rounded-full" :class="product.is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400'">
            {{ product.is_active ? 'Active' : 'Inactive' }}
          </span>
          <span class="text-sm hidden sm:inline" :class="isDarkMode ? 'text-gray-600' : 'text-gray-300'">•</span>
          <span class="text-sm" :class="isDarkMode ? 'text-gray-400' : 'text-gray-600'">
            SKU: <span class="font-medium" :class="isDarkMode ? 'text-white' : 'text-gray-900'">{{ product.sku || 'N/A' }}</span>
          </span>
          <span class="text-sm hidden sm:inline" :class="isDarkMode ? 'text-gray-600' : 'text-gray-300'">•</span>
          <span class="text-sm" :class="isDarkMode ? 'text-gray-400' : 'text-gray-600'">
            Stock: <span class="font-medium" :class="product.stock_quantity <= 5 ? 'text-red-600 dark:text-red-400' : (isDarkMode ? 'text-white' : 'text-gray-900')">
              {{ product.stock_quantity }}
              <span v-if="product.stock_quantity <= 5" class="text-red-600 dark:text-red-400">(Low)</span>
            </span>
          </span>
        </div>
        <div class="text-sm flex items-center gap-2" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
          </svg>
          Created: {{ formatDate(product.created_at) }}
        </div>
      </div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div :class="[
          'rounded-2xl shadow-lg p-5 border-l-4 border-blue-500',
          isDarkMode ? 'bg-gray-800' : 'bg-white'
        ]">
          <p class="text-xs uppercase tracking-wider" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Cost Price</p>
          <p class="text-2xl font-bold" :class="isDarkMode ? 'text-white' : 'text-gray-900'">${{ parseFloat(product.price).toFixed(2) }}</p>
        </div>
        <div :class="[
          'rounded-2xl shadow-lg p-5 border-l-4 border-green-500',
          isDarkMode ? 'bg-gray-800' : 'bg-white'
        ]">
          <p class="text-xs uppercase tracking-wider" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Sale Price</p>
          <p class="text-2xl font-bold text-green-600 dark:text-green-400">${{ parseFloat(product.sale_price).toFixed(2) }}</p>
        </div>
        <div :class="[
          'rounded-2xl shadow-lg p-5 border-l-4 border-purple-500',
          isDarkMode ? 'bg-gray-800' : 'bg-white'
        ]">
          <p class="text-xs uppercase tracking-wider" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Stock Quantity</p>
          <p class="text-2xl font-bold" :class="isDarkMode ? 'text-white' : 'text-gray-900'">{{ product.stock_quantity }}</p>
        </div>
        <div :class="[
          'rounded-2xl shadow-lg p-5 border-l-4 border-orange-500',
          isDarkMode ? 'bg-gray-800' : 'bg-white'
        ]">
          <p class="text-xs uppercase tracking-wider" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Payment Type</p>
          <p class="text-2xl font-bold" :class="isDarkMode ? 'text-white' : 'text-gray-900'">{{ formatPaymentType(product.payment_type) }}</p>
        </div>
      </div>

      <!-- Main Content Grid -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column - Product Info -->
        <div class="lg:col-span-2 space-y-6">
          <!-- Product Details Card -->
          <div :class="[
            'rounded-2xl shadow-lg p-6',
            isDarkMode ? 'bg-gray-800' : 'bg-white'
          ]">
            <h3 class="text-sm font-semibold uppercase tracking-wider mb-4" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Product Information</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
              <div>
                <p class="text-xs font-medium" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Product Name</p>
                <p class="text-base font-semibold mt-1" :class="isDarkMode ? 'text-white' : 'text-gray-900'">{{ product.name }}</p>
              </div>
              <div>
                <p class="text-xs font-medium" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Product Category</p>
                <p class="text-base font-semibold mt-1" :class="isDarkMode ? 'text-white' : 'text-gray-900'">{{ product.category.name }}</p>
              </div>
              <div>
                <p class="text-xs font-medium" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">SKU</p>
                <p class="text-base mt-1" :class="isDarkMode ? 'text-gray-300' : 'text-gray-900'">{{ product.sku || 'N/A' }}</p>
              </div>
              <div>
                <p class="text-xs font-medium" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Cost Price</p>
                <p class="text-base font-medium mt-1" :class="isDarkMode ? 'text-white' : 'text-gray-900'">${{ parseFloat(product.price).toFixed(2) }}</p>
              </div>
              <div>
                <p class="text-xs font-medium" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Sale Price</p>
                <p class="text-base font-medium text-green-600 dark:text-green-400 mt-1">${{ parseFloat(product.sale_price).toFixed(2) }}</p>
              </div>
              <div>
                <p class="text-xs font-medium" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Stock Quantity</p>
                <p class="text-base font-medium mt-1" :class="isDarkMode ? 'text-white' : 'text-gray-900'">
                  {{ product.stock_quantity }}
                  <span v-if="product.stock_quantity <= 5" class="text-xs text-red-600 dark:text-red-400 ml-1">(Low stock)</span>
                </p>
              </div>
              <div>
                <p class="text-xs font-medium" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Payment Type</p>
                <p class="text-base mt-1" :class="isDarkMode ? 'text-gray-300' : 'text-gray-900'">{{ formatPaymentType(product.payment_type) }}</p>
              </div>
              <div class="sm:col-span-2">
                <p class="text-xs font-medium" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Status</p>
                <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full mt-1" :class="product.is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400'">
                  {{ product.is_active ? 'Active' : 'Inactive' }}
                </span>
              </div>
            </div>
          </div>

          <!-- Description Card -->
          <div :class="[
            'rounded-2xl shadow-lg p-6',
            isDarkMode ? 'bg-gray-800' : 'bg-white'
          ]">
            <h3 class="text-sm font-semibold uppercase tracking-wider mb-4" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Description</h3>
            <p class="leading-relaxed" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">{{ product.description || 'No description provided.' }}</p>
          </div>
        </div>

        <!-- Right Column - Vendor & Additional Info -->
        <div class="lg:col-span-1 space-y-6">
          <!-- Vendor Card -->
          <div :class="[
            'rounded-2xl shadow-lg p-6',
            isDarkMode ? 'bg-gray-800' : 'bg-white'
          ]">
            <h3 class="text-sm font-semibold uppercase tracking-wider mb-4" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Vendor</h3>
            <div v-if="product.vendor" class="space-y-3">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center flex-shrink-0">
                  <span class="text-blue-600 dark:text-blue-400 font-semibold">{{ getInitials(product.vendor.business_name || product.vendor.name) }}</span>
                </div>
                <div>
                  <p class="font-medium" :class="isDarkMode ? 'text-white' : 'text-gray-900'">{{ product.vendor.business_name || product.vendor.name }}</p>
                  <p class="text-xs" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">{{ product.vendor.email }}</p>
                </div>
              </div>
              <div v-if="product.vendor.phone" class="pt-3 border-t" :class="isDarkMode ? 'border-gray-700' : 'border-gray-100'">
                <p class="text-xs" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Phone</p>
                <p class="text-sm" :class="isDarkMode ? 'text-white' : 'text-gray-900'">{{ product.vendor.phone }}</p>
              </div>
            </div>
            <p v-else class="text-sm" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">No vendor information available</p>
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
      <h3 class="text-lg font-medium mb-2" :class="isDarkMode ? 'text-white' : 'text-gray-900'">Product Not Found</h3>
      <p class="mb-4" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">The product you're looking for doesn't exist or has been removed.</p>
      <router-link to="/admin/products" class="px-4 py-2 bg-blue-600 from-primary-500 to-primary-600 text-white rounded-lg hover:from-primary-600 hover:to-primary-700 transition-all duration-200 shadow-md inline-block">
        Return to Products
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

const product = ref(null);
const loading = ref(false);
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

const fetchProduct = async () => {
  loading.value = true;
  try {
    const response = await axios.get(`/api/admin/products/${route.params.id}`);
    product.value = response.data;
  } catch (error) {
    console.error('Error fetching product:', error);
    showToast('Failed to load product', 'error');
  } finally {
    loading.value = false;
  }
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

const formatPaymentType = (type) => {
  const types = {
    one_time: 'One Time',
    subscription: 'Subscription',
    recurring: 'Recurring'
  };
  return types[type] || type || 'N/A';
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

onMounted(() => {
  checkDarkMode();
  setupDarkModeWatcher();
  fetchProduct();

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