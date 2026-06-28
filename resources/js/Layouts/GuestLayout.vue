<!-- resources/js/layouts/GuestLayout.vue -->
<template>
    <div class="min-h-screen flex flex-col items-center justify-center bg-gray-100">
        <div class="w-full max-w-md">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900">VendorHub</h1>
                <p class="text-gray-600 mt-2">Multi-Vendor Order Management System</p>
            </div>
            
            <slot />
        </div>
    </div>
</template>

<script>
export default {
    name: 'GuestLayout'
};
</script>