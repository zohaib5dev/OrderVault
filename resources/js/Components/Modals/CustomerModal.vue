<!-- resources/js/components/Modals/CustomerModal.vue -->
<template>
    <div class="fixed inset-0 z-50 overflow-y-auto" @click.self="close">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 transition-opacity" @click="close">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <!-- Modal panel -->
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <!-- Header -->
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Create New Customer</h3>
                        <button @click="close" class="text-gray-400 hover:text-gray-500 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Success Message -->
                    <div v-if="successMessage" class="mb-4 p-3 bg-green-50 border border-green-200 rounded-md">
                        <p class="text-sm text-green-600">{{ successMessage }}</p>
                    </div>

                    <!-- Error Message -->
                    <div v-if="errorMessage" class="mb-4 p-3 bg-red-50 border border-red-200 rounded-md">
                        <p class="text-sm text-red-600">{{ errorMessage }}</p>
                    </div>

                    <!-- Form -->
                    <form @submit.prevent="submitCustomer">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Full Name <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    v-model="form.name" 
                                    class="w-full border rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    :class="{ 'border-red-500': errors.name }"
                                    required
                                    placeholder="Enter customer name"
                                    :disabled="loading"
                                >
                                <p v-if="errors.name" class="mt-1 text-sm text-red-600">{{ errors.name }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Email <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="email" 
                                    v-model="form.email" 
                                    class="w-full border rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    :class="{ 'border-red-500': errors.email }"
                                    required
                                    placeholder="customer@example.com"
                                    :disabled="loading"
                                >
                                <p v-if="errors.email" class="mt-1 text-sm text-red-600">{{ errors.email }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                                <input 
                                    type="tel" 
                                    v-model="form.phone" 
                                    class="w-full border rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    :class="{ 'border-red-500': errors.phone }"
                                    placeholder="+1 234 567 8900"
                                    :disabled="loading"
                                >
                                <p v-if="errors.phone" class="mt-1 text-sm text-red-600">{{ errors.phone }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                                <textarea 
                                    v-model="form.address" 
                                    class="w-full border rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    :class="{ 'border-red-500': errors.address }"
                                    rows="2"
                                    placeholder="Enter customer address"
                                    :disabled="loading"
                                ></textarea>
                                <p v-if="errors.address" class="mt-1 text-sm text-red-600">{{ errors.address }}</p>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="mt-6 flex justify-end space-x-3 border-t border-gray-200 pt-4">
                            <button 
                                type="button" 
                                @click="close" 
                                class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition-colors"
                                :disabled="loading"
                            >
                                Cancel
                            </button>
                            <button 
                                type="submit" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors disabled:opacity-50"
                                :disabled="loading"
                            >
                                <span v-if="loading">
                                    <svg class="inline w-4 h-4 mr-2 animate-spin" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Creating...
                                </span>
                                <span v-else>Create Customer</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { ref, reactive } from 'vue';
import axios from 'axios';
import { useToast } from 'vue-toastification';

export default {
    name: 'CustomerModal',
    emits: ['close', 'created'],
    setup(_, { emit }) {
        const toast = useToast();
        const loading = ref(false);
        const errorMessage = ref('');
        const successMessage = ref('');
        const errors = reactive({});

        const form = ref({
            name: '',
            email: '',
            phone: '',
            address: ''
        });

        const close = () => {
            if (!loading.value) {
                emit('close');
            }
        };

        const resetForm = () => {
            form.value = {
                name: '',
                email: '',
                phone: '',
                address: ''
            };
            errors.value = {};
            errorMessage.value = '';
            successMessage.value = '';
        };

        const submitCustomer = async () => {
            // Reset errors
            errors.value = {};
            errorMessage.value = '';
            successMessage.value = '';

            // Validate required fields
            if (!form.value.name.trim()) {
                errors.name = 'Customer name is required';
                return;
            }

            if (!form.value.email.trim()) {
                errors.email = 'Email is required';
                return;
            }

            // Validate email format
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(form.value.email)) {
                errors.email = 'Please enter a valid email address';
                return;
            }

            loading.value = true;

            try {
                const response = await axios.post('/api/customers', {
                    name: form.value.name.trim(),
                    email: form.value.email.trim(),
                    phone: form.value.phone || null,
                    address: form.value.address || null
                });

                if (response.data.success) {
                    // Show success message
                    successMessage.value = 'Customer created successfully!';
                    toast.success('Customer created successfully!');
                    
                    // Emit the created customer
                    emit('created', response.data.data);
                    
                    // Reset form
                    resetForm();
                    
                    // Close modal after short delay
                    setTimeout(() => {
                        emit('close');
                    }, 1500);
                } else {
                    errorMessage.value = response.data.message || 'Failed to create customer';
                    toast.error(errorMessage.value);
                }
            } catch (err) {
                console.error('Error creating customer:', err);
                
                if (err.response?.data?.errors) {
                    // Handle validation errors
                    errors.value = err.response.data.errors;
                    toast.error('Please fix the validation errors');
                } else if (err.response?.data?.message) {
                    errorMessage.value = err.response.data.message;
                    toast.error(errorMessage.value);
                } else {
                    errorMessage.value = 'Failed to create customer. Please try again.';
                    toast.error(errorMessage.value);
                }
            } finally {
                loading.value = false;
            }
        };

        return {
            form,
            loading,
            errors,
            errorMessage,
            successMessage,
            close,
            submitCustomer,
            resetForm
        };
    }
};
</script>

<style scoped>
/* Animation for modal */
.modal-enter-active,
.modal-leave-active {
    transition: opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
    opacity: 0;
}

/* Slide animation for modal content */
.slide-enter-active,
.slide-leave-active {
    transition: all 0.3s ease;
}

.slide-enter-from {
    transform: translateY(-20px);
    opacity: 0;
}

.slide-leave-to {
    transform: translateY(20px);
    opacity: 0;
}

/* Spinner animation */
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