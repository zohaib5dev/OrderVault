<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-100 py-8 px-4 sm:px-6 lg:px-8">
        <div class="w-full max-w-6xl bg-white rounded-lg shadow-lg p-6 sm:p-8">
            <div>
               <div class="text-center mb-8">
          <div class="inline-block">
            <div class=" bg-gray-200 rounded-xl  p-2 mb-2">
              <div class="w-40 bg-blue-600 rounded-xl mb-2 flex items-center justify-center shadow-lg">
                <img 
                v-if="'/logo.jpeg'" 
                :src="'/logo.jpeg'" 
                alt="Logo" 
                class="w-40 object-contain"
              />
              <svg v-else class="w-40 h-40 text-blue-600 dark:text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4zm0 5c0 2.21 3.582 4 8 4s8-1.79 8-4"/>
              </svg>
              </div>
              <span class="text-2xl font-bold text-gray-900">OrderValut</span>
            </div>
          </div>
          <h2 class="text-3xl font-extrabold text-gray-900 mt-4">
            Create your Account
          </h2>
          
        </div>
                
            </div>

          

            <form @submit.prevent="handleRegister">
                <!-- Common Fields - 2 columns on desktop -->
               <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <!-- Row 1: Name + Email (2 columns) -->
    <div class="">
        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
            Full Name *
        </label>
        <input 
            id="name"
            type="text" 
            v-model="form.name" 
            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
            required
            placeholder="Enter your full name"
        >
    </div>
    
    <div class="md:col-span-1">
        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
            Email Address *
        </label>
        <input 
            id="email"
            type="email" 
            v-model="form.email" 
            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
            required
            placeholder="Enter your email"
        >
    </div>

    <!-- Row 2: Phone + Password + Confirm Password (3 columns) -->
    <div>
        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">
            Phone Number
        </label>
        <input 
            id="phone"
            type="tel" 
            v-model="form.phone" 
            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
            placeholder="Enter your phone number"
        >
    </div>
    
    <div>
        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
            Password *
        </label>
        <input 
            id="password"
            type="password" 
            v-model="form.password" 
            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
            required
            placeholder="Choose a password"
        >
        <p class="mt-1 text-xs text-gray-500">Must be at least 8 characters</p>
    </div>
    
    <div>
        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
            Confirm Password *
        </label>
        <input 
            id="password_confirmation"
            type="password" 
            v-model="form.password_confirmation" 
            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
            required
            placeholder="Confirm your password"
        >
    </div>
</div>

            

                <!-- Error & Success Messages -->
                <div v-if="error" class="mt-4 p-3 bg-red-50 border border-red-200 rounded-md">
                    <p class="text-sm text-red-600">{{ error }}</p>
                </div>

                <div v-if="successMessage" class="mt-4 p-3 bg-green-50 border border-green-200 rounded-md">
                    <p class="text-sm text-green-600">{{ successMessage }}</p>
                </div>

                
                
                <!-- Submit Button -->
                <button 
                    type="submit" 
                    class="w-full mt-6 bg-blue-600 text-white py-2.5 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors"
                    :disabled="loading"
                >
                    <span v-if="loading">
                        <svg class="inline w-4 h-4 mr-2 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        {{  'Creating Account...' }}
                    </span>
                    <span v-else>
                        {{ 'Create Account' }}
                    </span>
                </button>

                <!-- Login Link -->
                <div class="mt-4 text-center">
                    <p class="text-sm text-gray-600">
                        Already have an account? 
                        <router-link to="/login" class="text-blue-600 hover:text-blue-800 font-medium">
                            Sign in here
                        </router-link>
                    </p>
                </div>

                <!-- Social Login Buttons  
                <div class="relative my-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-white text-gray-500">Or continue with</span>
                    </div>
                </div>
 
                <div class="grid grid-cols-2 gap-4">
                    <button type="button" class="flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                        <svg class="w-5 h-5 mr-2" viewBox="0 0 24 24">
                            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 01-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z"/>
                            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                        </svg>
                        Google
                    </button>
                    <button type="button" class="flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="#1877F2" viewBox="0 0 24 24">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                        Facebook
                    </button>
                </div> -->
            </form>
        </div>
    </div>
</template>


<script>
import { ref, watch } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

export default {
    name: 'Register',
    setup() {
        const router = useRouter();
        const accountType = ref('customer');
        const loading = ref(false);
        const error = ref('');
        const successMessage = ref('');

        const form = ref({
            name: '',
            email: '',
            phone: '',
            password: '',
            password_confirmation: '',
        });

        // Watch account type to reset errors
        watch(accountType, () => {
            error.value = '';
            successMessage.value = '';
        });

        const handleRegister = async () => {
            error.value = '';
            successMessage.value = '';
            
            // Validate password
            if (form.value.password.length < 8) {
                error.value = 'Password must be at least 8 characters';
                return;
            }

            if (form.value.password !== form.value.password_confirmation) {
                error.value = 'Passwords do not match';
                return;
            } 
            
            loading.value = true;
            
            try {
                // Prepare registration data
                const registrationData = {
                    name: form.value.name,
                    email: form.value.email,
                    phone: form.value.phone || null,
                    password: form.value.password,
                    password_confirmation: form.value.password_confirmation,
                    role: accountType.value,
                 };

                const response = await axios.post('/api/register', registrationData);
                
                if (response.data.success) {
                      
                        router.push('/login?registered=true');
                     
                } else {
                    error.value = response.data.message || 'Registration failed. Please try again.';
                }
            } catch (err) {
                if (err.response?.data?.errors) {
                    // Handle validation errors
                    const errors = err.response.data.errors;
                    const firstError = Object.values(errors)[0];
                    error.value = Array.isArray(firstError) ? firstError[0] : firstError;
                } else {
                    error.value = err.response?.data?.message || 'Registration failed. Please try again.';
                }
            } finally {
                loading.value = false;
            }
        };

        return {
            accountType,
            form,
            error,
            successMessage,
            loading,
            handleRegister
        };
    }
};
</script>

<style scoped> 
button:focus {
    outline: 2px solid transparent;
    outline-offset: 2px;
}

/* Smooth transitions */
.transition-all {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 200ms;
}

/* Custom checkbox styling */
input[type="checkbox"] {
    cursor: pointer;
}

/* Animate spin for loading */
@keyframes spin {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}

.animate-spin {
    animation: spin 1s linear infinite;
}
</style>