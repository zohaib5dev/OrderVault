<!-- resources/js/components/Common/AppSidebar.vue -->
<template>
    <aside class="w-64 bg-white shadow-lg min-h-screen">
        <nav class="mt-6 px-4">
            <router-link to="/dashboard" class="sidebar-link" active-class="bg-blue-50 text-blue-600">
                <HomeIcon class="w-5 h-5" />
                Dashboard
            </router-link>
            
            <router-link to="/orders" class="sidebar-link" active-class="bg-blue-50 text-blue-600">
                <ShoppingBagIcon class="w-5 h-5" />
                Orders
            </router-link>
            
            <router-link to="/products" class="sidebar-link" active-class="bg-blue-50 text-blue-600">
                <CubeIcon class="w-5 h-5" />
                Products
            </router-link>
            
            <template v-if="authStore.user?.role === 'admin'">
                <router-link to="/vendors" class="sidebar-link" active-class="bg-blue-50 text-blue-600">
                    <UsersIcon class="w-5 h-5" />
                    Vendors
                </router-link>
            </template>
            
            <template v-if="authStore.user?.role === 'vendor'">
                <router-link to="/vendor/dashboard" class="sidebar-link" active-class="bg-blue-50 text-blue-600">
                    <ChartBarIcon class="w-5 h-5" />
                    Vendor Dashboard
                </router-link>
                
                <router-link to="/vendor/settings" class="sidebar-link" active-class="bg-blue-50 text-blue-600">
                    <CogIcon class="w-5 h-5" />
                    Settings
                </router-link>
            </template>
        </nav>
    </aside>
</template>

<script>
import { useAuthStore } from '../../store';
import {
    HomeIcon,
    ShoppingBagIcon,
    CubeIcon,
    UsersIcon,
    ChartBarIcon,
    CogIcon
} from '@heroicons/vue/24/outline';

export default {
    components: {
        HomeIcon,
        ShoppingBagIcon,
        CubeIcon,
        UsersIcon,
        ChartBarIcon,
        CogIcon
    },
    setup() {
        const authStore = useAuthStore();
        return { authStore };
    }
};
</script>

<style scoped>
.sidebar-link {
    @apply flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100 transition-colors mb-1;
}
.sidebar-link svg {
    @apply mr-3;
}
</style>