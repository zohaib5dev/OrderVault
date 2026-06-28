<!-- resources/js/pages/vendor/orders/Edit.vue -->
<template>
    <div>
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Edit Order</h1>
        
        <div v-if="order" class="bg-white rounded-lg shadow p-6">
            <form @submit.prevent="updateOrder">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select v-model="form.status" class="w-full border rounded-md px-3 py-2">
                            <option value="pending">Pending</option>
                            <option value="processing">Processing</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Payment Status</label>
                        <select v-model="form.payment_status" class="w-full border rounded-md px-3 py-2">
                            <option value="pending">Pending</option>
                            <option value="paid">Paid</option>
                            <option value="failed">Failed</option>
                            <option value="refunded">Refunded</option>
                        </select>
                    </div>
                    
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Shipping Address</label>
                        <textarea v-model="form.shipping_address" class="w-full border rounded-md px-3 py-2" rows="3"></textarea>
                    </div>
                    
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                        <textarea v-model="form.notes" class="w-full border rounded-md px-3 py-2" rows="2"></textarea>
                    </div>
                </div>
                
                <div class="mt-6 flex justify-end space-x-4">
                    <router-link :to="`/vendor/orders/${order.id}`" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">
                        Cancel
                    </router-link>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Update Order
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';

export default {
    setup() {
        const route = useRoute();
        const router = useRouter();
        const order = ref(null);
        const form = ref({
            status: 'pending',
            payment_status: 'pending',
            shipping_address: '',
            notes: ''
        });

        const fetchOrder = async () => {
            try {
                const response = await axios.get(`/api/vendor/orders/${route.params.id}`);
                order.value = response.data;
                form.value = {
                    status: response.data.status,
                    payment_status: response.data.payment_status,
                    shipping_address: response.data.shipping_address,
                    notes: response.data.notes || ''
                };
            } catch (error) {
                console.error('Error fetching order:', error);
            }
        };

        const updateOrder = async () => {
            try {
                await axios.put(`/api/vendor/orders/${route.params.id}`, form.value);
                router.push(`/vendor/orders/${route.params.id}`);
            } catch (error) {
                console.error('Error updating order:', error);
                alert('Failed to update order');
            }
        };

        onMounted(() => {
            fetchOrder();
        });

        return {
            order,
            form,
            updateOrder
        };
    }
};
</script>