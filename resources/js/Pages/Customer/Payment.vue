<template>
  <div class="payment-page" :class="{ 'dark-mode': isDarkMode }">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
      <div>
        <nav class="flex items-center gap-2 text-sm mb-2" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">
          <router-link to="/customer/dashboard" :class="isDarkMode ? 'hover:text-gray-200' : 'hover:text-blue-600'" class="transition-colors">
            Dashboard
          </router-link>
          <span :class="isDarkMode ? 'text-gray-600' : 'text-gray-300'">/</span>
          <router-link to="/customer/orders" :class="isDarkMode ? 'hover:text-gray-200' : 'hover:text-blue-600'" class="transition-colors">
            Orders
          </router-link>
          <span :class="isDarkMode ? 'text-gray-600' : 'text-gray-300'">/</span>
          <span :class="isDarkMode ? 'text-gray-200' : 'text-gray-700'" class="font-medium">Payment</span>
        </nav>
        <h1 :class="isDarkMode ? 'text-white' : 'text-gray-800'" class="text-2xl font-bold">Order Payment</h1>
        <p :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'" class="text-sm mt-1">Complete your payment for order #{{ order?.order_number || order?.id }}</p>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center items-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
    </div>

    <div v-else>
      <!-- Order Summary -->
      <div :class="[
        'rounded-xl shadow-sm border overflow-hidden mb-6',
        isDarkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-100'
      ]">
        <div :class="[
          'px-6 py-4 border-b',
          isDarkMode ? 'bg-gray-700/50 border-gray-700' : 'bg-gradient-to-r from-blue-50 to-indigo-50 border-gray-100'
        ]">
          <div class="flex items-center gap-2">
            <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
            </svg>
            <span class="text-xs font-semibold text-blue-600 dark:text-blue-400 uppercase tracking-wider">Order Summary</span>
          </div>
        </div>
        
        <div class="p-6">
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
              <p :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'" class="text-xs uppercase tracking-wider">Order Number</p>
              <p :class="isDarkMode ? 'text-white' : 'text-gray-900'" class="text-sm font-medium">
                #{{ order?.order_number || 'ORD-' + String(order?.id).padStart(6, '0') }}
              </p>
            </div>
            <div>
              <p :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'" class="text-xs uppercase tracking-wider">Date</p>
              <p :class="isDarkMode ? 'text-white' : 'text-gray-900'" class="text-sm font-medium">{{ formatDate(order?.created_at) }}</p>
            </div>
            <div>
              <p :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'" class="text-xs uppercase tracking-wider">Vendor</p>
              <p :class="isDarkMode ? 'text-white' : 'text-gray-900'" class="text-sm font-medium">{{ order?.vendor?.business_name || order?.vendor?.name || 'N/A' }}</p>
            </div>
            <div>
              <p :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'" class="text-xs uppercase tracking-wider">Status</p>
              <span class="text-xs px-2 py-1 rounded-full font-medium" :class="getStatusClass(order?.status)">
                {{ formatStatus(order?.status) }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Payment Section -->
      <div :class="[
        'rounded-xl shadow-sm border overflow-hidden',
        isDarkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-100'
      ]">
        <div :class="[
          'px-6 py-4 border-b flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2',
          isDarkMode ? 'bg-gray-700/50 border-gray-700' : 'bg-gray-50/50 border-gray-100'
        ]">
          <div class="flex items-center gap-2">
            <svg class="w-5 h-5" :class="isDarkMode ? 'text-gray-400' : 'text-gray-600'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v1m0 1v1m0 1v1m0 1v1m0 1v1"/>
            </svg>
            <h2 :class="isDarkMode ? 'text-white' : 'text-gray-900'" class="text-lg font-semibold">Payment Details</h2>
          </div>
          <div class="flex items-center gap-2">
            <span :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'" class="text-sm">Total:</span>
            <span class="text-2xl font-bold text-blue-600 dark:text-blue-400">${{ formatNumber(order?.total_amount) }}</span>
          </div>
        </div>

        <div class="p-6">
          <!-- Order Items -->
          <div class="border-b pb-4 mb-4" :class="isDarkMode ? 'border-gray-700' : 'border-gray-200'">
            <h3 :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'" class="text-sm font-semibold mb-3">Order Items</h3>
            <div v-for="item in order?.items" :key="item.id" class="flex items-center justify-between py-2">
              <div class="flex items-center gap-3">
                <img 
                  v-if="getFirstImage(item.product?.images)" 
                  :src="getFirstImage(item.product?.images)" 
                  :alt="item.product?.name"
                  class="w-12 h-12 rounded-lg object-cover"
                />
                <div class="w-12 h-12 bg-gray-200 dark:bg-gray-700 rounded-lg flex items-center justify-center" v-else>
                  <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                  </svg>
                </div>
                <div>
                  <p :class="isDarkMode ? 'text-white' : 'text-gray-900'" class="font-medium">{{ item.product?.name || 'Product' }}</p>
                  <p :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'" class="text-xs">Qty: {{ item.quantity }} × ${{ formatNumber(item.price) }}</p>
                </div>
              </div>
              <span :class="isDarkMode ? 'text-white' : 'text-gray-900'" class="font-semibold">
                ${{ formatNumber(item.total || item.price * item.quantity) }}
              </span>
            </div>
          </div>

          <!-- Amount Breakdown -->
          <div class="space-y-2 mb-6">
            <div class="flex justify-between text-sm">
              <span :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Subtotal</span>
              <span :class="isDarkMode ? 'text-white' : 'text-gray-900'">${{ formatNumber(order?.subtotal || order?.total_amount) }}</span>
            </div>
            <div v-if="order?.discount > 0" class="flex justify-between text-sm">
              <span :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Discount</span>
              <span class="text-green-600 dark:text-green-400">-${{ formatNumber(order?.discount) }}</span>
            </div>
            <div v-if="order?.tax > 0" class="flex justify-between text-sm">
              <span :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Tax</span>
              <span :class="isDarkMode ? 'text-white' : 'text-gray-900'">${{ formatNumber(order?.tax) }}</span>
            </div>
            <div v-if="order?.shipping_cost > 0" class="flex justify-between text-sm">
              <span :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Shipping</span>
              <span :class="isDarkMode ? 'text-white' : 'text-gray-900'">${{ formatNumber(order?.shipping_cost) }}</span>
            </div>
            <div class="flex justify-between text-lg font-bold pt-2 border-t" :class="isDarkMode ? 'border-gray-700' : 'border-gray-200'">
              <span :class="isDarkMode ? 'text-white' : 'text-gray-900'">Total</span>
              <span :class="isDarkMode ? 'text-white' : 'text-gray-900'">${{ formatNumber(order?.total_amount) }}</span>
            </div>
          </div>

          <!-- Already Paid -->
          <div v-if="isPaid" class="text-center py-8">
            <svg class="w-16 h-16 mx-auto mb-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <h3 :class="isDarkMode ? 'text-white' : 'text-gray-900'" class="text-xl font-semibold mb-2">Payment Completed</h3>
            <p :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">This order has been paid successfully.</p>
            <router-link 
              :to="'/customer/orders'"
              class="inline-block mt-4 px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
            >
              Back to Orders
            </router-link>
          </div>

          <!-- Payment Form -->
          <div v-else>
            <div class="flex items-center gap-2 mb-4">
              <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
              </svg>
              <span class="text-sm font-medium" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">
                Secure Payment with Stripe
              </span>
            </div>

            <!-- Payment Method Selection -->
            <div class="space-y-3 mb-4">
              <div 
                class="flex items-center gap-3 p-3 border-2 rounded-xl cursor-pointer transition-all"
                :class="paymentMethod === 'stripe' 
                  ? (isDarkMode ? 'border-purple-600 bg-purple-900/20' : 'border-purple-500 bg-purple-50') 
                  : (isDarkMode ? 'border-gray-600 hover:border-gray-500' : 'border-gray-200 hover:border-gray-300')"
                @click="paymentMethod = 'stripe'"
              >
                <input type="radio" value="stripe" v-model="paymentMethod" class="w-4 h-4 text-purple-600">
                <div class="flex-1">
                  <label :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'" class="text-sm font-medium cursor-pointer">Credit Card / Debit Card</label>
                  <p :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'" class="text-xs">Secure instant payment</p>
                </div>
                <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                </svg>
              </div>
            </div>

            <!-- Stripe Card Element -->
            <div v-if="paymentMethod === 'stripe'" class="mt-4">
              <div :class="[
                'border-2 rounded-xl p-4 transition-colors focus-within:border-blue-500',
                isDarkMode ? 'border-gray-600 bg-gray-700' : 'border-gray-200 bg-gray-50'
              ]">
                <div id="card-element" class="py-2"></div>
                <div id="card-errors" class="text-red-600 text-xs mt-2" role="alert"></div>
              </div>
              <p :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'" class="text-xs mt-2 flex items-center gap-1">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                </svg>
                Your payment information is secure and encrypted.
              </p>
            </div>

            <!-- Payment Errors -->
            <div v-if="paymentError" class="mt-4 p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
              <p class="text-sm text-red-600 dark:text-red-400">{{ paymentError }}</p>
            </div>

            <!-- Pay Button -->
            <button 
              @click="processPayment"
              :disabled="processing"
              class="w-full mt-6 py-3 px-4 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl font-semibold hover:from-blue-600 hover:to-blue-700 transition-all duration-200 shadow-md disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
            >
              <span v-if="!processing">
                Pay ${{ formatNumber(order?.total_amount) }}
              </span>
              <span v-else class="flex items-center justify-center gap-2">
                <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Processing...
              </span>
            </button>

            <p class="text-xs text-center mt-4" :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'">
              🔒 Your payment is secure and encrypted with Stripe
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useAuthStore } from '../../store';
import axios from 'axios';

export default {
  setup() {
    const route = useRoute();
    const router = useRouter();
    const authStore = useAuthStore();
    
    const orderId = route.params.id;
    const order = ref(null);
    const loading = ref(false);
    const processing = ref(false);
    const paymentError = ref('');
    const isDarkMode = ref(false);
    const isPaid = ref(false);
    const paymentMethod = ref('stripe');
    const stripeKey = ref('');
    
    let stripeInstance = null;
    let cardElement = null;
    let stripeInitialized = false;
    let darkModeObserver = null;

    const getFirstImage = (images) => {
      if (!images) return null;
      try {
        if (typeof images === 'string') {
          const parsed = JSON.parse(images);
          return parsed[0] || null;
        }
        if (Array.isArray(images)) {
          return images[0] || null;
        }
        return null;
      } catch {
        return null;
      }
    };

    const getStatusClass = (status) => {
      const classes = {
        pending: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
        processing: 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
        shipped: 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400',
        delivered: 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
        completed: 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
        cancelled: 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400'
      };
      return classes[status?.toLowerCase()] || 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
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
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      });
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
              if (stripeInitialized && !isPaid.value) {
                initializeStripe();
              }
            }
          }
        });
      });
      darkModeObserver.observe(htmlElement, { attributes: true });
    };

    const loadStripeScript = () => {
      return new Promise((resolve, reject) => {
        if (typeof Stripe !== 'undefined') {
          resolve();
          return;
        }
        const script = document.createElement('script');
        script.src = 'https://js.stripe.com/v3/';
        script.async = true;
        script.onload = resolve;
        script.onerror = reject;
        document.head.appendChild(script);
      });
    };

    const initializeStripe = () => {
      if (paymentMethod.value !== 'stripe' || !stripeKey.value) return;
      
      if (typeof Stripe !== 'undefined') {
        try {
          if (cardElement) {
            cardElement.destroy();
            cardElement = null;
          }
          
          stripeInstance = Stripe(stripeKey.value);
          const elements = stripeInstance.elements();
          
          const style = {
            base: {
              color: isDarkMode.value ? '#e5e7eb' : '#32325d',
              fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
              fontSmoothing: 'antialiased',
              fontSize: '16px',
              '::placeholder': {
                color: isDarkMode.value ? '#6b7280' : '#aab7c4'
              }
            },
            invalid: {
              color: '#fa755a',
              iconColor: '#fa755a'
            }
          };
          
          const cardElementContainer = document.getElementById('card-element');
          if (cardElementContainer) {
            cardElement = elements.create('card', { style: style });
            cardElement.mount('#card-element');
            stripeInitialized = true;
            
            cardElement.on('change', ({error}) => {
              const displayError = document.getElementById('card-errors');
              if (displayError) {
                if (error) {
                  displayError.textContent = error.message;
                } else {
                  displayError.textContent = '';
                }
              }
            });
          }
        } catch (error) {
          console.error('Error initializing Stripe:', error);
          showToast('Failed to initialize payment system', 'error');
        }
      } else {
        loadStripeScript().then(() => {
          setTimeout(() => initializeStripe(), 500);
        }).catch(() => {
          showToast('Failed to load payment system. Please refresh the page.', 'error');
        });
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

    const fetchOrderDetails = async () => {
      loading.value = true;
      try {
        const response = await axios.get(`/api/customer/orders/${orderId}`);
        if (response.data) {
          order.value = response.data.order || response.data;
          stripeKey.value = response.data.stripe_key;
          isPaid.value = order.value?.payment_status === 'paid';
          
          if (!isPaid.value && stripeKey.value) {
            await initializeStripe();
          }
        }
      } catch (error) {
        console.error('Error fetching order:', error);
        showToast('Failed to load order details', 'error');
        router.push('/customer/orders');
      } finally {
        loading.value = false;
      }
    };

    const processPayment = async () => {
      if (!order.value || isPaid.value) return;
      
      processing.value = true;
      paymentError.value = '';

      try {
        if (!stripeInstance || !cardElement) {
          paymentError.value = 'Payment system not initialized. Please refresh the page.';
          processing.value = false;
          return;
        }

        const { error, paymentMethod: stripePaymentMethod } = await stripeInstance.createPaymentMethod({
          type: 'card',
          card: cardElement,
        });

        if (error) {
          paymentError.value = error.message;
          showToast(error.message, 'error');
          processing.value = false;
          return;
        }

        // Process payment
        const response = await axios.post(`/api/customer/orders/${orderId}/pay`, {
          payment_method_id: stripePaymentMethod.id
        });

        if (response.data.success) {
          showToast('Payment successful!', 'success');
          isPaid.value = true;
          order.value.payment_status = 'paid';
          
          setTimeout(() => {
            router.push(`/customer/orders/${orderId}`);
          }, 2000);
        }
      } catch (error) {
        paymentError.value = error.response?.data?.message || 'Payment failed. Please try again.';
        showToast(paymentError.value, 'error');
      } finally {
        processing.value = false;
      }
    };

    // Watch for payment method changes
    watch(paymentMethod, () => {
      if (paymentMethod.value === 'stripe' && !isPaid.value) {
        setTimeout(() => initializeStripe(), 300);
      } else if (paymentMethod.value !== 'stripe' && stripeInitialized) {
        if (cardElement) {
          cardElement.destroy();
          cardElement = null;
          stripeInitialized = false;
        }
      }
    });

    // Watch dark mode changes
    watch(isDarkMode, () => {
      if (cardElement && stripeInitialized && !isPaid.value) {
        const style = {
          base: {
            color: isDarkMode.value ? '#e5e7eb' : '#32325d',
            fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
            fontSmoothing: 'antialiased',
            fontSize: '16px',
            '::placeholder': {
              color: isDarkMode.value ? '#6b7280' : '#aab7c4'
            }
          },
          invalid: {
            color: '#fa755a',
            iconColor: '#fa755a'
          }
        };
        cardElement.destroy();
        const elements = stripeInstance.elements();
        cardElement = elements.create('card', { style: style });
        cardElement.mount('#card-element');
        cardElement.on('change', ({error}) => {
          const displayError = document.getElementById('card-errors');
          if (displayError) {
            if (error) {
              displayError.textContent = error.message;
            } else {
              displayError.textContent = '';
            }
          }
        });
      }
    });

    onMounted(() => {
      checkDarkMode();
      setupDarkModeWatcher();
      fetchOrderDetails();

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
      if (cardElement) {
        cardElement.destroy();
        cardElement = null;
        stripeInitialized = false;
      }
    });

    return {
      order,
      loading,
      processing,
      paymentError,
      isDarkMode,
      isPaid,
      paymentMethod,
      stripeKey,
      getFirstImage,
      getStatusClass,
      formatStatus,
      formatNumber,
      formatDate,
      processPayment,
    };
  }
};
</script>

<style scoped>
.payment-page {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 1rem;
}

@media (max-width: 640px) {
  .payment-page {
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

/* Stripe dark mode override */
.dark-mode #card-element .Input {
  color: #e5e7eb !important;
}

.dark-mode #card-element .Input::placeholder {
  color: #6b7280 !important;
}
</style>