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
      <span :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">{{ product?.name || 'Edit Product' }}</span>
    </nav>

    <!-- Header -->
    <div>
      <h1 class="text-2xl md:text-3xl font-bold" :class="isDarkMode ? 'text-white' : 'text-gray-900'">Edit Product</h1>
      <p class="text-sm" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Update product information and inventory details</p>
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

    <!-- Form Card -->
    <div v-else-if="product" :class="[
      'rounded-2xl shadow-lg p-6',
      isDarkMode ? 'bg-gray-800' : 'bg-white'
    ]">
      <form @submit.prevent="updateProduct">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- Left Column -->
          <div class="space-y-6">
            <!-- Product Name -->
            <div>
              <label class="block text-sm font-medium mb-1.5" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">
                Product Name <span class="text-red-500">*</span>
              </label>
              <input 
                type="text" 
                v-model="form.name" 
                :class="[
                  'w-full border rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors',
                  isDarkMode ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-400' : 'bg-white border-gray-300 text-gray-900'
                ]"
                placeholder="Enter product name"
                required
              >
            </div>

            <!-- Category -->
            <div>
              <label class="block text-sm font-medium mb-1.5" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">
                Category <span class="text-red-500">*</span>
              </label>
              <select 
                v-model="form.category_id" 
                :class="[
                  'w-full border rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors appearance-none',
                  isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'
                ]"
                required
              >
                <option value="">Select a category</option>
                <option v-for="category in categories" :key="category.id" :value="category.id">
                  {{ category.name }}
                </option>
              </select>
              <div class="flex justify-between items-center mt-1">
                <p v-if="!categories.length" class="text-xs" :class="isDarkMode ? 'text-yellow-400' : 'text-yellow-600'">
                  ⚠️ No categories available. Please create a category first.
                </p>
                <button 
                  type="button"
                  @click="openCategoryModal"
                  class="text-xs text-blue-600 dark:text-blue-400 hover:underline focus:outline-none"
                >
                  + Create new category
                </button>
              </div>
            </div>

            <!-- Description -->
            <div>
              <label class="block text-sm font-medium mb-1.5" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">
                Description
              </label>
              <textarea 
                v-model="form.description" 
                :class="[
                  'w-full border rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors resize-y',
                  isDarkMode ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-400' : 'bg-white border-gray-300 text-gray-900'
                ]"
                rows="4"
                placeholder="Enter product description (optional)"
              ></textarea>
            </div>

            <!-- SKU -->
            <div>
              <label class="block text-sm font-medium mb-1.5" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">
                SKU <span class="text-xs" :class="isDarkMode ? 'text-gray-400' : 'text-gray-400'">(Optional)</span>
              </label>
              <input 
                type="text" 
                v-model="form.sku" 
                :class="[
                  'w-full border rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors',
                  isDarkMode ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-400' : 'bg-white border-gray-300 text-gray-900'
                ]"
                placeholder="Enter SKU (e.g., PRD-001)"
              >
            </div>
          </div>

          <!-- Right Column -->
          <div class="space-y-6">
            <!-- Cost Price -->
            <div>
              <label class="block text-sm font-medium mb-1.5" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">
                Cost Price <span class="text-red-500">*</span>
              </label>
              <div class="relative">
                <span class="absolute left-3 top-1/2 transform -translate-y-1/2" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">$</span>
                <input 
                  type="number" 
                  step="0.01" 
                  min="0"
                  v-model="form.price" 
                  :class="[
                    'w-full border rounded-xl pl-8 pr-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors',
                    isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'
                  ]"
                  placeholder="0.00"
                  required
                >
              </div>
            </div>

            <!-- Sale Price -->
            <div>
              <label class="block text-sm font-medium mb-1.5" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">
                Sale Price <span class="text-red-500">*</span>
              </label>
              <div class="relative">
                <span class="absolute left-3 top-1/2 transform -translate-y-1/2" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">$</span>
                <input 
                  type="number" 
                  step="0.01" 
                  min="0"
                  v-model="form.sale_price" 
                  :class="[
                    'w-full border rounded-xl pl-8 pr-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors',
                    isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'
                  ]"
                  placeholder="0.00"
                  required
                >
              </div>
              <p v-if="form.price && form.sale_price" class="text-xs mt-1" :class="form.sale_price < form.price ? 'text-red-500 dark:text-red-400' : 'text-green-600 dark:text-green-400'">
                {{ form.sale_price < form.price ? '⚠️ Sale price is lower than cost price' : '✓ Sale price is higher than cost price' }}
              </p>
            </div>

            <!-- Stock Quantity -->
            <div>
              <label class="block text-sm font-medium mb-1.5" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">
                Stock Quantity <span class="text-red-500">*</span>
              </label>
              <input 
                type="number" 
                min="0"
                v-model="form.stock_quantity" 
                :class="[
                  'w-full border rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors',
                  isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'
                ]"
                placeholder="Enter stock quantity"
                required
              >
            </div>

            <!-- Status -->
            <div>
              <label class="block text-sm font-medium mb-1.5" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">
                Status
              </label>
              <div class="flex items-center gap-4">
                <label class="flex items-center gap-2 cursor-pointer">
                  <input 
                    type="radio" 
                    v-model="form.is_active" 
                    :value="true"
                    class="form-radio"
                    :class="isDarkMode ? 'bg-gray-700 border-gray-500' : 'bg-white border-gray-300'"
                  >
                  <span class="text-sm" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">Active</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                  <input 
                    type="radio" 
                    v-model="form.is_active" 
                    :value="false"
                    class="form-radio"
                    :class="isDarkMode ? 'bg-gray-700 border-gray-500' : 'bg-white border-gray-300'"
                  >
                  <span class="text-sm" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">Inactive</span>
                </label>
              </div>
            </div>
          </div>
        </div>

        <!-- Form Actions -->
        <div class="mt-8 pt-6 border-t flex flex-col sm:flex-row justify-end gap-3" :class="isDarkMode ? 'border-gray-700' : 'border-gray-200'">
          <router-link 
            :to="`/admin/products/${product.id}`" 
            :class="[
              'px-6 py-2.5 rounded-lg transition-colors text-center',
              isDarkMode ? 'bg-gray-700 text-gray-300 hover:bg-gray-600' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
            ]"
          >
            Cancel
          </router-link>
          <button 
            type="submit" 
            class="px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-200 shadow-md flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
            :disabled="updating || !categories.length"
          >
            <svg v-if="updating" class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span v-if="updating">Updating...</span>
            <span v-else>Update Product</span>
          </button>
        </div>
      </form>
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
      <router-link to="/admin/products" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-200 shadow-md inline-block">
        Return to Products
      </router-link>
    </div>

    <!-- Quick Category Creation Modal -->
    <div
      v-if="showCategoryModal"
      class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
      @click.self="closeCategoryModal"
    >
      <div :class="[
        'rounded-2xl shadow-2xl max-w-md w-full p-6',
        isDarkMode ? 'bg-gray-800' : 'bg-white'
      ]">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold" :class="isDarkMode ? 'text-white' : 'text-gray-900'">
            Create New Category
          </h3>
          <button
            @click="closeCategoryModal"
            class="p-1 rounded-lg transition-colors"
            :class="isDarkMode ? 'text-gray-400 hover:bg-gray-700' : 'text-gray-400 hover:bg-gray-100'"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <form @submit.prevent="createCategory">
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium mb-1" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">
                Category Name *
              </label>
              <input
                type="text"
                v-model="newCategory.name"
                required
                :class="[
                  'w-full px-3 py-2 border rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors',
                  isDarkMode ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-400' : 'bg-white border-gray-300 text-gray-900'
                ]"
                placeholder="Enter category name"
              />
            </div>

            <div>
              <label class="block text-sm font-medium mb-1" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">
                Description <span class="text-xs" :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'">(optional)</span>
              </label>
              <textarea
                v-model="newCategory.description"
                rows="2"
                :class="[
                  'w-full px-3 py-2 border rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors resize-none',
                  isDarkMode ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-400' : 'bg-white border-gray-300 text-gray-900'
                ]"
                placeholder="Enter category description"
              ></textarea>
            </div>
          </div>

          <div class="mt-6 flex flex-col sm:flex-row justify-end gap-3">
            <button
              type="button"
              @click="closeCategoryModal"
              :class="[
                'px-4 py-2 border rounded-lg transition',
                isDarkMode ? 'border-gray-600 text-gray-300 hover:bg-gray-700' : 'border-gray-300 text-gray-700 hover:bg-gray-50'
              ]"
            >
              Cancel
            </button>
            <button
              type="submit"
              :disabled="categoryCreating"
              class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition shadow-md disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <span v-if="categoryCreating" class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-2"></span>
              Create Category
            </button>
          </div>
        </form>
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

const product = ref(null);
const loading = ref(false);
const updating = ref(false);
const isDarkMode = ref(false);
let darkModeObserver = null;

// Categories
const categories = ref([]);
const showCategoryModal = ref(false);
const categoryCreating = ref(false);
const newCategory = ref({
  name: '',
  description: '',
  is_active: true
});

const form = ref({
  name: '',
  category_id: '',
  price: '',
  sale_price: '',
  stock_quantity: '',
  description: '',
  sku: '',
  is_active: true
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

// Fetch categories
const fetchCategories = async () => {
  try {
    const response = await axios.get('/api/admin/categories/active');
    categories.value = response.data.data || [];
  } catch (error) {
    console.error('Error fetching categories:', error);
    showToast('Failed to load categories', 'error');
  }
};

// Open category creation modal
const openCategoryModal = () => {
  newCategory.value = {
    name: '',
    description: '',
    is_active: true
  };
  showCategoryModal.value = true;
};

// Close category creation modal
const closeCategoryModal = () => {
  showCategoryModal.value = false;
  newCategory.value = {
    name: '',
    description: '',
    is_active: true
  };
};

// Create new category
const createCategory = async () => {
  categoryCreating.value = true;
  try {
    const response = await axios.post('/api/admin/categories', {
      name: newCategory.value.name,
      description: newCategory.value.description,
      is_active: true
    });
    
    // Add new category to the list
    categories.value.push(response.data.data);
    // Auto-select the newly created category
    form.value.category_id = response.data.data.id;
    
    showToast('Category created successfully!', 'success');
    closeCategoryModal();
  } catch (error) {
    console.error('Error creating category:', error);
    const message = error.response?.data?.message || 'Failed to create category';
    showToast(message, 'error');
  } finally {
    categoryCreating.value = false;
  }
};

const fetchProduct = async () => {
  loading.value = true;
  try {
    const response = await axios.get(`/api/admin/products/${route.params.id}`);
    product.value = response.data;
    form.value = {
      name: response.data.name || '',
      category_id: response.data.category_id || '',
      price: response.data.price || '',
      sale_price: response.data.sale_price || '',
      stock_quantity: response.data.stock_quantity || '',
      description: response.data.description || '',
      sku: response.data.sku || '',
      is_active: response.data.is_active !== undefined ? response.data.is_active : true
    };
  } catch (error) {
    console.error('Error fetching product:', error);
    showToast('Failed to load product', 'error');
  } finally {
    loading.value = false;
  }
};

const updateProduct = async () => {
  updating.value = true;

  try {
    // Validate sale price is not less than cost price
    if (parseFloat(form.value.sale_price) < parseFloat(form.value.price)) {
      if (!confirm('Sale price is lower than cost price. This may result in losses. Continue anyway?')) {
        updating.value = false;
        return;
      }
    }

    await axios.put(`/api/admin/products/${route.params.id}`, form.value);
    showToast('Product updated successfully!', 'success');
    router.push(`/admin/products/${route.params.id}`);
  } catch (err) {
    console.error('Error updating product:', err);
    const message = err.response?.data?.message || 'Failed to update product';
    showToast(message, 'error');
  } finally {
    updating.value = false;
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
  fetchCategories();
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
.form-radio {
  width: 1.2rem;
  height: 1.2rem;
  border-radius: 50%;
  border: 2px solid #d1d5db;
  transition: all 0.2s;
  cursor: pointer;
  appearance: none;
  -webkit-appearance: none;
  background-color: white;
}

.dark .form-radio {
  background-color: #1f2937;
  border-color: #4b5563;
}

.form-radio:checked {
  border-color: #3b82f6;
  background-color: #3b82f6;
  background-image: url("data:image/svg+xml,%3csvg viewBox='0 0 16 16' fill='white' xmlns='http://www.w3.org/2000/svg'%3e%3ccircle cx='8' cy='8' r='3'/%3e%3c/svg%3e");
  background-size: 100% 100%;
  background-position: center;
  background-repeat: no-repeat;
}

.dark .form-radio:checked {
  background-color: #3b82f6;
  border-color: #3b82f6;
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

/* Custom select arrow */
select {
  background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
  background-position: right 0.5rem center;
  background-repeat: no-repeat;
  background-size: 1.5em 1.5em;
  padding-right: 2.5rem;
}

.dark select {
  background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%239ca3af' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
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