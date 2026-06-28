<template>
    <div class="p-6">
        <h1 class="text-2xl font-bold mb-6" :class="isDarkMode ? 'text-white' : 'text-gray-900'">My Profile</h1>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Left Column: Profile Information -->
            <div :class="[
                'rounded-2xl shadow-lg overflow-hidden',
                isDarkMode ? 'bg-gray-800' : 'bg-white'
            ]">
                <div class="p-6 md:p-8">
                    <form @submit.prevent="updateProfile" class="space-y-6">
                        <!-- Profile Header -->
                        <div class="flex items-center gap-4 pb-6 border-b" :class="isDarkMode ? 'border-gray-700' : 'border-gray-200'">
                            <div class="w-16 h-16 bg-gradient-to-r from-primary-500 to-primary-600 rounded-full flex items-center justify-center text-2xl font-bold text-white shadow-md">
                                {{ form.name?.charAt(0) || 'U' }}
                            </div>
                            <div>
                                <h2 class="text-lg font-semibold" :class="isDarkMode ? 'text-white' : 'text-gray-900'">{{ form.name || 'User' }}</h2>
                                <p class="text-sm" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">{{ form.email }}</p>
                            </div>
                        </div>

                        <!-- Profile Information -->
                        <div>
                            <h3 class="text-lg font-semibold mb-4" :class="isDarkMode ? 'text-white' : 'text-gray-900'">Personal Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium mb-1" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">Full Name *</label>
                                    <input type="text" v-model="form.name" required 
                                        :class="[
                                            'w-full px-4 py-2.5 border rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition',
                                            isDarkMode ? 'bg-gray-700 border-gray-600 text-gray-200 placeholder-gray-400' : 'bg-white border-gray-300 text-gray-900'
                                        ]">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-1" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">Email *</label>
                                    <input type="email" v-model="form.email" required 
                                        :class="[
                                            'w-full px-4 py-2.5 border rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition',
                                            isDarkMode ? 'bg-gray-700 border-gray-600 text-gray-400 cursor-not-allowed' : 'bg-gray-50 border-gray-300 text-gray-900'
                                        ]" disabled>
                                    <p class="text-xs mt-1" :class="isDarkMode ? 'text-gray-400' : 'text-gray-400'">Email cannot be changed</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-1" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">Phone</label>
                                    <input type="text" v-model="form.phone" 
                                        :class="[
                                            'w-full px-4 py-2.5 border rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition',
                                            isDarkMode ? 'bg-gray-700 border-gray-600 text-gray-200 placeholder-gray-400' : 'bg-white border-gray-300 text-gray-900'
                                        ]">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-1" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">Address</label>
                                    <input type="text" v-model="form.address" 
                                        :class="[
                                            'w-full px-4 py-2.5 border rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition',
                                            isDarkMode ? 'bg-gray-700 border-gray-600 text-gray-200 placeholder-gray-400' : 'bg-white border-gray-300 text-gray-900'
                                        ]">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-1" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">City</label>
                                    <input type="text" v-model="form.city" 
                                        :class="[
                                            'w-full px-4 py-2.5 border rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition',
                                            isDarkMode ? 'bg-gray-700 border-gray-600 text-gray-200 placeholder-gray-400' : 'bg-white border-gray-300 text-gray-900'
                                        ]">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-1" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">State/Province</label>
                                    <input type="text" v-model="form.state" 
                                        :class="[
                                            'w-full px-4 py-2.5 border rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition',
                                            isDarkMode ? 'bg-gray-700 border-gray-600 text-gray-200 placeholder-gray-400' : 'bg-white border-gray-300 text-gray-900'
                                        ]">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-1" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">ZIP/Postal Code</label>
                                    <input type="text" v-model="form.zip" 
                                        :class="[
                                            'w-full px-4 py-2.5 border rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition',
                                            isDarkMode ? 'bg-gray-700 border-gray-600 text-gray-200 placeholder-gray-400' : 'bg-white border-gray-300 text-gray-900'
                                        ]">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-1" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">Country</label>
                                    <select v-model="form.country" 
                                        :class="[
                                            'w-full px-4 py-2.5 border rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition',
                                            isDarkMode ? 'bg-gray-700 border-gray-600 text-gray-200' : 'bg-white border-gray-300 text-gray-900'
                                        ]">
                                        <option value="">Select Country</option>
                                        <option value="US">United States</option>
                                        <option value="CA">Canada</option>
                                        <option value="GB">United Kingdom</option>
                                        <option value="DE">Germany</option>
                                        <option value="FR">France</option>
                                        <option value="AU">Australia</option>
                                        <option value="IN">India</option>
                                        <option value="AE">UAE</option>
                                        <option value="SG">Singapore</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex justify-end items-center gap-4">
                            <button type="button" @click="resetForm" 
                                :class="[
                                    'px-4 py-2.5 border rounded-xl transition',
                                    isDarkMode ? 'border-gray-600 text-gray-300 hover:bg-gray-700' : 'border-gray-300 text-gray-700 hover:bg-gray-50'
                                ]">
                                Cancel
                            </button>
                            <button type="submit" :disabled="isLoading" 
                                class="px-6 py-2.5 bg-gradient-to-r from-primary-500 to-primary-600 text-white rounded-xl hover:from-primary-600 hover:to-primary-700 transition disabled:opacity-50 disabled:cursor-not-allowed shadow-md font-medium">
                                {{ isLoading ? 'Saving...' : 'Save Changes' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Right Column: Change Password -->
            <div :class="[
                'rounded-2xl shadow-lg overflow-hidden h-fit',
                isDarkMode ? 'bg-gray-800' : 'bg-white'
            ]">
                <div class="p-6 md:p-8">
                    <h3 class="text-lg font-semibold mb-4" :class="isDarkMode ? 'text-white' : 'text-gray-900'">Change Password</h3>
                    
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium mb-1" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">New Password</label>
                            <input type="password" v-model="passwordForm.password" 
                                :class="[
                                    'w-full px-4 py-2.5 border rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition',
                                    isDarkMode ? 'bg-gray-700 border-gray-600 text-gray-200 placeholder-gray-400' : 'bg-white border-gray-300 text-gray-900'
                                ]"
                                placeholder="Enter new password">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium mb-1" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">Confirm Password</label>
                            <input type="password" v-model="passwordForm.password_confirmation" 
                                :class="[
                                    'w-full px-4 py-2.5 border rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition',
                                    isDarkMode ? 'bg-gray-700 border-gray-600 text-gray-200 placeholder-gray-400' : 'bg-white border-gray-300 text-gray-900'
                                ]"
                                placeholder="Confirm new password">
                        </div>

                        <div v-if="passwordForm.password && passwordForm.password.length > 0" class="text-xs" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">
                            Password must be at least 8 characters
                        </div>

                        <div class="flex justify-end items-center gap-4">
                            <button type="button" @click="resetPasswordForm" 
                                :class="[
                                    'px-4 py-2.5 border rounded-xl transition',
                                    isDarkMode ? 'border-gray-600 text-gray-300 hover:bg-gray-700' : 'border-gray-300 text-gray-700 hover:bg-gray-50'
                                ]">
                                Cancel
                            </button>
                            <button type="button" @click="changePassword" :disabled="isPasswordLoading" 
                                class="px-6 py-2.5 bg-gradient-to-r from-primary-500 to-primary-600 text-white rounded-xl hover:from-primary-600 hover:to-primary-700 transition disabled:opacity-50 disabled:cursor-not-allowed shadow-md font-medium">
                                {{ isPasswordLoading ? 'Updating...' : 'Update' }}
                            </button>
                        </div>

                        <div v-if="passwordMessage" class="text-sm" :class="passwordMessageType">
                            {{ passwordMessage }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import axios from 'axios';
import { useAuthStore } from '../../store';
import { useToast } from 'vue-toastification';

const authStore = useAuthStore();
const toast = useToast();
const isLoading = ref(false);
const isPasswordLoading = ref(false);
const passwordMessage = ref('');
const passwordMessageType = ref('');
const isDarkMode = ref(false);
let darkModeObserver = null;

const form = ref({
    name: '',
    email: '',
    phone: '',
    address: '',
    city: '',
    state: '',
    zip: '',
    country: '',
});

const passwordForm = ref({
    password: '',
    password_confirmation: '',
});

// Check dark mode from localStorage or system preference
const checkDarkMode = () => {
    const savedTheme = localStorage.getItem('darkMode');
    if (savedTheme !== null) {
        isDarkMode.value = savedTheme === 'true';
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
                    localStorage.setItem('darkMode', String(hasDarkClass));
                }
            }
        });
    });
    darkModeObserver.observe(htmlElement, { attributes: true });
};

const loadProfile = () => {
    const user = authStore.user;
    if (user) {
        form.value = {
            name: user.name || '',
            email: user.email || '',
            phone: user.phone || '',
            address: user.address || '',
            city: user.city || '',
            state: user.state || '',
            zip: user.zip || '',
            country: user.country || ''
        };
    }
};

const updateProfile = async () => {
    isLoading.value = true;
    try {
        const response = await axios.put('/api/vendor/profile', form.value);
        authStore.user = { ...authStore.user, ...response.data.user };
        localStorage.setItem('user', JSON.stringify(authStore.user));
        toast.success('Profile updated successfully');
    } catch (error) {
        const message = error.response?.data?.message || 'Failed to update profile';
        toast.error(message);
    } finally {
        isLoading.value = false;
    }
};

const changePassword = async () => {
    // Validate password
    if (!passwordForm.value.password) {
        passwordMessage.value = 'Please enter a new password';
        passwordMessageType.value = 'text-red-600';
        return;
    }
    
    if (passwordForm.value.password.length < 8) {
        passwordMessage.value = 'Password must be at least 8 characters';
        passwordMessageType.value = 'text-red-600';
        return;
    }
    
    if (passwordForm.value.password !== passwordForm.value.password_confirmation) {
        passwordMessage.value = 'Passwords do not match';
        passwordMessageType.value = 'text-red-600';
        return;
    }

    isPasswordLoading.value = true;
    passwordMessage.value = '';
    
    try {
        await axios.put('/api/vendor/profile/password', {
            password: passwordForm.value.password,
            password_confirmation: passwordForm.value.password_confirmation
        });
        
        passwordMessage.value = 'Password updated successfully!';
        passwordMessageType.value = 'text-green-600';
        toast.success('Password updated successfully');
        
        // Clear password fields
        passwordForm.value.password = '';
        passwordForm.value.password_confirmation = '';
        
    } catch (error) {
        const message = error.response?.data?.message || 'Failed to update password';
        passwordMessage.value = message;
        passwordMessageType.value = 'text-red-600';
        toast.error(message);
    } finally {
        isPasswordLoading.value = false;
    }
};

const resetForm = () => {
    loadProfile();
};

const resetPasswordForm = () => {
    passwordForm.value.password = '';
    passwordForm.value.password_confirmation = '';
    passwordMessage.value = '';
};

onMounted(() => {
    checkDarkMode();
    setupDarkModeWatcher();
    loadProfile();

    // Listen for storage changes from other tabs
    window.addEventListener('storage', (e) => {
        if (e.key === 'darkMode') {
            isDarkMode.value = e.newValue === 'true';
        }
    });
});

onUnmounted(() => {
    if (darkModeObserver) {
        darkModeObserver.disconnect();
    }
});
</script>