<!-- resources/js/pages/Profile/Index.vue -->
<template>
  <div class="space-y-6 pb-8">
    <!-- Breadcrumb -->
    <nav class="flex items-center space-x-2 text-sm" aria-label="Breadcrumb">
      <router-link :to="dashboardLink" :class="isDarkMode ? 'text-gray-400 hover:text-gray-200' : 'text-gray-500 hover:text-gray-700'">
        <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
          <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
        </svg>
        Dashboard
      </router-link>
      <span :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'">/</span>
      <span :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">Profile</span>
    </nav>

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
      <div>
        <h1 class="text-2xl md:text-3xl font-bold" :class="isDarkMode ? 'text-white' : 'text-gray-900'">My Profile</h1>
        <p class="text-sm" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Manage your account settings and preferences</p>
      </div>
      <button 
        @click="toggleEditMode" 
        class="px-4 py-2 rounded-lg transition-colors flex items-center gap-2 font-medium"
        :class="[
          isEditing 
            ? 'bg-red-600 text-white hover:bg-red-700' 
            : 'bg-blue-600 text-white hover:bg-blue-700'
        ]"
      >
        <svg v-if="!isEditing" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
        </svg>
        <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
        {{ isEditing ? 'Cancel' : 'Edit Profile' }}
      </button>
    </div>

    <!-- Loading State -->
    <div v-if="loading" :class="[
      'rounded-2xl shadow-lg p-12 text-center',
      isDarkMode ? 'bg-gray-800' : 'bg-white'
    ]">
      <div class="flex flex-col items-center justify-center">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
        <span class="mt-4" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Loading profile...</span>
      </div>
    </div>

    <!-- Profile Content -->
    <div v-else-if="user" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Profile Card -->
      <div class="lg:col-span-1">
        <div :class="[
          'rounded-2xl shadow-lg p-6 transition-colors duration-300',
          isDarkMode ? 'bg-gray-800' : 'bg-white'
        ]">
          <div class="flex flex-col items-center">
            <!-- Avatar -->
            <div class="relative">
              <div class="w-32 h-32 rounded-full flex items-center justify-center overflow-hidden"
                   :class="isDarkMode ? 'bg-gray-700' : 'bg-blue-100'">
                <span v-if="!avatarPreview" class="text-4xl font-bold" 
                      :class="isDarkMode ? 'text-blue-400' : 'text-blue-600'">
                  {{ getInitials(user.name) }}
                </span>
                <img v-else :src="avatarPreview" class="w-32 h-32 object-cover">
              </div>
              <button v-if="isEditing" @click="triggerFileInput" 
                      class="absolute bottom-0 right-0 p-2 bg-blue-600 rounded-full text-white hover:bg-blue-700 transition-colors shadow-lg">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
              </button>
              <input type="file" ref="fileInput" @change="handleFileUpload" accept="image/*" class="hidden">
            </div>
            
            <h2 class="mt-4 text-xl font-bold" :class="isDarkMode ? 'text-white' : 'text-gray-900'">
              {{ user.name }}
            </h2>
            <p class="text-sm" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">
              {{ user.email }}
            </p>
            <span class="mt-2 px-3 py-1 text-xs font-semibold rounded-full" :class="getRoleClass(user.role)">
              {{ formatRole(user.role) }}
            </span>
          </div>

          <!-- Quick Info -->
          <div class="mt-6 pt-6 border-t" :class="isDarkMode ? 'border-gray-700' : 'border-gray-200'">
            <div class="flex justify-between py-2">
              <span class="text-sm" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Member since</span>
              <span class="text-sm font-medium" :class="isDarkMode ? 'text-gray-300' : 'text-gray-900'">
                {{ formatDate(user.created_at) }}
              </span>
            </div>
            <div class="flex justify-between py-2">
              <span class="text-sm" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Last login</span>
              <span class="text-sm font-medium" :class="isDarkMode ? 'text-gray-300' : 'text-gray-900'">
                {{ user.last_login_at ? formatDate(user.last_login_at) : 'Never' }}
              </span>
            </div>
            <div class="flex justify-between py-2">
              <span class="text-sm" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">IP Address</span>
              <span class="text-sm font-medium" :class="isDarkMode ? 'text-gray-300' : 'text-gray-900'">
                {{ user.last_login_ip || 'N/A' }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Profile Form -->
      <div class="lg:col-span-2">
        <div :class="[
          'rounded-2xl shadow-lg p-6 transition-colors duration-300',
          isDarkMode ? 'bg-gray-800' : 'bg-white'
        ]">
          <h3 class="text-lg font-semibold mb-4" :class="isDarkMode ? 'text-white' : 'text-gray-800'">
            Profile Information
          </h3>
          
          <form @submit.prevent="updateProfile">
            <div class="space-y-4">
              <!-- Name -->
              <div>
                <label class="block text-sm font-medium mb-1" 
                       :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">
                  Full Name <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       v-model="form.name" 
                       :disabled="!isEditing"
                       class="w-full rounded-lg px-3 py-2 transition-colors"
                       :class="[
                         isDarkMode 
                           ? 'bg-gray-700 border-gray-600 text-white focus:ring-blue-500 focus:border-blue-500' 
                           : 'border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500',
                         !isEditing ? (isDarkMode ? 'bg-gray-800' : 'bg-gray-100') : '',
                         errors.name ? 'border-red-500 dark:border-red-500' : ''
                       ]"
                       required>
                <p v-if="errors.name" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ errors.name }}</p>
              </div>

              <!-- Email -->
              <div>
                <label class="block text-sm font-medium mb-1" 
                       :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">
                  Email Address <span class="text-red-500">*</span>
                </label>
                <input type="email" 
                       v-model="form.email" 
                       :disabled="!isEditing"
                       class="w-full rounded-lg px-3 py-2 transition-colors"
                       :class="[
                         isDarkMode 
                           ? 'bg-gray-700 border-gray-600 text-white focus:ring-blue-500 focus:border-blue-500' 
                           : 'border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500',
                         !isEditing ? (isDarkMode ? 'bg-gray-800' : 'bg-gray-100') : '',
                         errors.email ? 'border-red-500 dark:border-red-500' : ''
                       ]"
                       required>
                <p v-if="errors.email" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ errors.email }}</p>
              </div>

              <!-- Phone -->
              <div>
                <label class="block text-sm font-medium mb-1" 
                       :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">
                  Phone Number
                </label>
                <input type="tel" 
                       v-model="form.phone" 
                       :disabled="!isEditing"
                       class="w-full rounded-lg px-3 py-2 transition-colors"
                       :class="[
                         isDarkMode 
                           ? 'bg-gray-700 border-gray-600 text-white focus:ring-blue-500 focus:border-blue-500' 
                           : 'border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500',
                         !isEditing ? (isDarkMode ? 'bg-gray-800' : 'bg-gray-100') : '',
                         errors.phone ? 'border-red-500 dark:border-red-500' : ''
                       ]"
                       placeholder="+1 (555) 123-4567">
                <p v-if="errors.phone" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ errors.phone }}</p>
              </div>

              <!-- Password Change Section -->
              <div v-if="isEditing" class="pt-4 border-t" :class="isDarkMode ? 'border-gray-700' : 'border-gray-200'">
                <h4 class="text-md font-medium mb-3" :class="isDarkMode ? 'text-gray-300' : 'text-gray-800'">
                  Change Password
                </h4>
                <div class="space-y-4">
                  <div>
                    <label class="block text-sm font-medium mb-1" 
                           :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">
                      Current Password
                    </label>
                    <input type="password" 
                           v-model="passwordForm.current_password"
                           class="w-full rounded-lg px-3 py-2 transition-colors"
                           :class="[
                             isDarkMode 
                               ? 'bg-gray-700 border-gray-600 text-white focus:ring-blue-500 focus:border-blue-500' 
                               : 'border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500',
                             errors.current_password ? 'border-red-500 dark:border-red-500' : ''
                           ]"
                           placeholder="Enter current password">
                    <p v-if="errors.current_password" class="mt-1 text-sm text-red-600 dark:text-red-400">
                      {{ errors.current_password }}
                    </p>
                  </div>
                  <div>
                    <label class="block text-sm font-medium mb-1" 
                           :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">
                      New Password
                    </label>
                    <input type="password" 
                           v-model="passwordForm.password"
                           class="w-full rounded-lg px-3 py-2 transition-colors"
                           :class="[
                             isDarkMode 
                               ? 'bg-gray-700 border-gray-600 text-white focus:ring-blue-500 focus:border-blue-500' 
                               : 'border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500',
                             errors.password ? 'border-red-500 dark:border-red-500' : ''
                           ]"
                           placeholder="Enter new password">
                    <p v-if="errors.password" class="mt-1 text-sm text-red-600 dark:text-red-400">
                      {{ errors.password }}
                    </p>
                  </div>
                  <div>
                    <label class="block text-sm font-medium mb-1" 
                           :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">
                      Confirm New Password
                    </label>
                    <input type="password" 
                           v-model="passwordForm.password_confirmation"
                           class="w-full rounded-lg px-3 py-2 transition-colors"
                           :class="[
                             isDarkMode 
                               ? 'bg-gray-700 border-gray-600 text-white focus:ring-blue-500 focus:border-blue-500' 
                               : 'border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500'
                           ]"
                           placeholder="Confirm new password">
                  </div>
                </div>
              </div>

              <!-- Submit Buttons -->
              <div v-if="isEditing" class="pt-4 border-t flex flex-col sm:flex-row gap-3" 
                   :class="isDarkMode ? 'border-gray-700' : 'border-gray-200'">
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center gap-2"
                        :disabled="submitting">
                  <svg v-if="submitting" class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  <span v-if="submitting">Saving...</span>
                  <span v-else>Save Changes</span>
                </button>
                <button type="button" 
                        @click="resetForm" 
                        class="px-6 py-2 rounded-lg transition-colors"
                        :class="[
                          isDarkMode 
                            ? 'bg-gray-700 text-gray-300 hover:bg-gray-600' 
                            : 'bg-gray-200 text-gray-800 hover:bg-gray-300'
                        ]">
                  Reset
                </button>
              </div>

              <!-- Success Message -->
              <div v-if="successMessage" class="mt-4 p-3 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 rounded-lg">
                {{ successMessage }}
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Error State -->
    <div v-else :class="[
      'rounded-2xl shadow-lg p-12 text-center transition-colors duration-300',
      isDarkMode ? 'bg-gray-800' : 'bg-white'
    ]">
      <svg class="w-16 h-16 mx-auto mb-4" :class="isDarkMode ? 'text-gray-600' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
      </svg>
      <h3 class="text-lg font-medium mb-2" :class="isDarkMode ? 'text-white' : 'text-gray-900'">
        Profile Not Found
      </h3>
      <p class="mb-4" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">
        Unable to load your profile information.
      </p>
      <button @click="fetchProfile" 
              class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
        Retry
      </button>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, onUnmounted, computed } from 'vue';
import { useAuthStore } from '../../store';
import { useRoute } from 'vue-router';
import axios from 'axios';
import { useToast } from 'vue-toastification';

export default {
  setup() {
    const toast = useToast();
    const authStore = useAuthStore();
    const route = useRoute();
    
    const user = ref(null);
    const loading = ref(false);
    const isEditing = ref(false);
    const submitting = ref(false);
    const successMessage = ref('');
    const avatarPreview = ref(null);
    const fileInput = ref(null);
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

    // Determine dashboard link based on user role
    const dashboardLink = computed(() => {
      let role = user.value?.role;
      if (!role) {
        role = authStore.user?.value?.role;
      }
      
      if (role === 'admin') {
        return '/admin/dashboard';
      } else if (role === 'vendor') {
        return '/vendor/dashboard';
      } else if (role === 'customer') {
        return '/customer/dashboard';
      }
      
      // Default fallback
      return '/dashboard';
    });

    const form = ref({
      name: '',
      email: '',
      phone: '',
    });

    const passwordForm = ref({
      current_password: '',
      password: '',
      password_confirmation: '',
    });

    const errors = ref({});

    const getInitials = (name) => {
      if (!name) return '?';
      return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2);
    };

    const getRoleClass = (role) => {
      const classes = {
        admin: 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
        vendor: 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
        customer: 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400'
      };
      return classes[role] || 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
    };

    const formatRole = (role) => {
      return role ? role.charAt(0).toUpperCase() + role.slice(1) : 'N/A';
    };

    const formatDate = (date) => {
      if (!date) return 'N/A';
      return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      });
    };

    const showToast = (message, type = 'info') => {
      const toastEl = document.createElement('div');
      const bgColor = type === 'success' ? '#16a34a' : type === 'error' ? '#dc2626' : '#3b82f6';
      toastEl.style.cssText = `
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
      toastEl.textContent = message;
      document.body.appendChild(toastEl);
      
      setTimeout(() => {
        toastEl.style.opacity = '0';
        toastEl.style.transform = 'translateY(20px)';
        toastEl.style.transition = 'all 0.3s ease';
        setTimeout(() => {
          document.body.removeChild(toastEl);
        }, 300);
      }, 3000);
    };

    const fetchProfile = async () => {
      loading.value = true;
      try {
        const response = await axios.get('/api/user/profile');
        user.value = response.data.data || response.data;
        form.value = {
          name: user.value.name,
          email: user.value.email,
          phone: user.value.phone || '',
        };
        if (user.value.avatar) {
          avatarPreview.value = user.value.avatar;
        }
      } catch (error) {
        console.error('Error fetching profile:', error);
        showToast('Failed to load profile', 'error');
      } finally {
        loading.value = false;
      }
    };

    const updateProfile = async () => {
      submitting.value = true;
      errors.value = {};
      successMessage.value = '';

      try {
        const formData = new FormData();
        
        // Add _method for Laravel to treat as PUT
        formData.append('_method', 'PUT');
        formData.append('name', form.value.name);
        formData.append('email', form.value.email);
        formData.append('phone', form.value.phone || '');

        // Add password if being changed
        if (passwordForm.value.password) {
          formData.append('current_password', passwordForm.value.current_password);
          formData.append('password', passwordForm.value.password);
          formData.append('password_confirmation', passwordForm.value.password_confirmation);
        }

        // Add avatar if uploaded
        if (fileInput.value && fileInput.value.files[0]) {
          formData.append('avatar', fileInput.value.files[0]);
        }

        const response = await axios.post('/api/user/profile', formData, {
          headers: {
            'Content-Type': 'multipart/form-data',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
          }
        });

        if (response.data.success) {
          user.value = response.data.data;
          form.value = {
            name: user.value.name,
            email: user.value.email,
            phone: user.value.phone || '',
          };
          if (user.value.avatar) {
            avatarPreview.value = user.value.avatar;
          }
          
          // Update auth store
          authStore.user.value = user.value;
          
          successMessage.value = 'Profile updated successfully!';
          showToast('Profile updated successfully!', 'success');
          
          // Reset password form
          passwordForm.value = {
            current_password: '',
            password: '',
            password_confirmation: '',
          };
          
          // Exit edit mode after successful update
          setTimeout(() => {
            isEditing.value = false;
            successMessage.value = '';
          }, 3000);
        }
      } catch (error) {
        console.error('Error updating profile:', error);
        
        if (error.response?.data?.errors) {
          errors.value = error.response.data.errors;
          showToast('Please fix the validation errors', 'error');
        } else if (error.response?.data?.message) {
          showToast(error.response.data.message, 'error');
        } else {
          showToast('Failed to update profile', 'error');
        }
      } finally {
        submitting.value = false;
      }
    };

    const toggleEditMode = () => {
      if (isEditing.value) {
        resetForm();
      }
      isEditing.value = !isEditing.value;
    };

    const resetForm = () => {
      form.value = {
        name: user.value.name,
        email: user.value.email,
        phone: user.value.phone || '',
      };
      passwordForm.value = {
        current_password: '',
        password: '',
        password_confirmation: '',
      };
      errors.value = {};
      successMessage.value = '';
      if (user.value.avatar) {
        avatarPreview.value = user.value.avatar;
      }
    };

    const triggerFileInput = () => {
      fileInput.value.click();
    };

    const handleFileUpload = (event) => {
      const file = event.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
          avatarPreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
      }
    };

    onMounted(() => {
      checkDarkMode();
      setupDarkModeWatcher();
      fetchProfile();

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
      window.removeEventListener('storage', () => {});
    });

    return {
      user,
      loading,
      isEditing,
      submitting,
      successMessage,
      form,
      passwordForm,
      errors,
      avatarPreview,
      fileInput,
      isDarkMode,
      dashboardLink,
      getInitials,
      getRoleClass,
      formatRole,
      formatDate,
      fetchProfile,
      updateProfile,
      toggleEditMode,
      resetForm,
      triggerFileInput,
      handleFileUpload,
    };
  }
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

/* Smooth transitions for theme changes */
* {
  transition: background-color 0.3s ease, border-color 0.3s ease, color 0.3s ease;
}

/* Responsive improvements */
@media (max-width: 640px) {
  .space-y-6 {
    --tw-space-y-reverse: 0;
    margin-top: calc(1.5rem * calc(1 - var(--tw-space-y-reverse)));
    margin-bottom: calc(1.5rem * var(--tw-space-y-reverse));
  }
}
</style>