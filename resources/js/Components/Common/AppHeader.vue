<!-- resources/js/components/Common/AppHeader.vue -->
<template>
    <header class="bg-white shadow">
        <div class="px-6 py-4 flex justify-between items-center">
            <div class="flex items-center">
                <button @click="toggleSidebar" class="lg:hidden mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <h1 class="text-2xl font-bold text-gray-800">VendorHub</h1>
            </div>
            
            <div class="flex items-center space-x-4">
                <!-- Cart -->
                <router-link to="/cart" class="relative text-gray-600 hover:text-gray-800">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <span v-if="cartCount > 0" 
                          class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full px-2 py-1">
                        {{ cartCount }}
                    </span>
                </router-link>
                
                <!-- User Menu -->
                <div class="relative" @click="toggleDropdown">
                    <div class="flex items-center cursor-pointer">
                        <img :src="userAvatar" class="w-8 h-8 rounded-full" />
                        <span class="ml-2 text-gray-700">{{ userName }}</span>
                        <svg class="w-4 h-4 ml-1 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                    
                    <div v-if="showDropdown" 
                         class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                        <router-link to="/profile" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <svg class="inline w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Profile
                        </router-link>
                        <router-link v-if="userRole === 'admin'" to="/vendors" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <svg class="inline w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            Vendors
                        </router-link>
                        <router-link v-if="userRole === 'vendor'" to="/vendor/dashboard" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <svg class="inline w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                            Vendor Dashboard
                        </router-link>
                        <button @click="logout" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                            <svg class="inline w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            Logout
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </header>
</template>

<script>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore, useCartStore } from '../../store';

export default {
    name: 'AppHeader',
    setup() {
        const router = useRouter();
        const authStore = useAuthStore();
        const cartStore = useCartStore();
        
        const showDropdown = ref(false);

        // Computed properties
        const user = computed(() => authStore.user);
        const userName = computed(() => user.value?.name || 'Guest');
        const userRole = computed(() => user.value?.role || '');
        const userAvatar = computed(() => {
            if (user.value?.avatar) {
                return user.value.avatar;
            }
            return `https://ui-avatars.com/api/?name=${encodeURIComponent(userName.value)}&background=3b82f6&color=fff`;
        });
        const cartCount = computed(() => cartStore.totalItems || 0);

        // Methods
        const toggleDropdown = () => {
            showDropdown.value = !showDropdown.value;
        };

        const toggleSidebar = () => {
            // Emit event to toggle sidebar
            window.dispatchEvent(new CustomEvent('toggle-sidebar'));
        };

        const logout = async () => {
            await authStore.logout();
            router.push('/login');
        };

        // Fetch cart on mount
        onMounted(() => {
            if (authStore.isAuthenticated) {
                cartStore.fetchCart();
            }
        });

        return {
            user,
            userName,
            userRole,
            userAvatar,
            cartCount,
            showDropdown,
            toggleDropdown,
            toggleSidebar,
            logout
        };
    }
};
</script>