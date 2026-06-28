import { defineStore } from 'pinia';
import axios from './bootstrap';
import { ref, computed } from 'vue';

export const useAuthStore = defineStore('auth', () => {
    const user = ref(null);
    const token = ref(localStorage.getItem('access_token'));
    const logo = ref(localStorage.getItem('logo'));

    const isAuthenticated = computed(() => !!token.value && !!user.value);

    async function login(credentials) {
        try {
            const response = await axios.post('/api/login', credentials);

            console.log('Login API response:', response.data.data.logo);

            // Handle different response structures
            let userData, tokenData, logoData;

            // Check if response has data.data structure
            if (response.data.data) {
                tokenData = response.data.data.token || response.data.data.access_token;
                userData = response.data.data.user;
                logoData = response.data.data.logo;
            } else {
                // Direct response structure
                tokenData = response.data.token || response.data.access_token;
                userData = response.data.user;
                logoData = response.data.logo;
            }

            // If token or user is missing, try alternative structure
            if (!tokenData && response.data.token) {
                tokenData = response.data.token;
                userData = response.data.user;
                logoData = response.data.logo;
            }

            if (!tokenData || !userData) {
                console.error('Invalid response structure:', response.data);
                return {
                    success: false,
                    error: 'Invalid response from server'
                };
            }

            console.log(logoData); 
            // Store token and user
            token.value = tokenData;
            user.value = userData;
            logo.value = logoData;


            // Save to localStorage
            localStorage.setItem('logo', logo.value);
            localStorage.setItem('access_token', token.value);
            localStorage.setItem('user', JSON.stringify(user.value));

            // Set axios default header
            axios.defaults.headers.common['Authorization'] = `Bearer ${token.value}`;

            console.log('Auth store updated:', {
                token: token.value,
                user: user.value,
                logo: logo.value,
                role: user.value?.role
            });

            return { success: true };
        } catch (error) {
            console.error('Login error:', error);
            return {
                success: false,
                error: error.response?.data?.message || 'Login failed'
            };
        }
    }

    async function logout() {
        try {
            await axios.post('/api/logout');
        } catch (error) {
            console.error('Logout error:', error);
        } finally {
            token.value = null;
            user.value = null;
            localStorage.removeItem('logo');
            localStorage.removeItem('access_token');
            localStorage.removeItem('user');
            delete axios.defaults.headers.common['Authorization'];
        }
    }

    async function fetchUser() {
        if (!token.value) {
            return null;
        }

        try {
            axios.defaults.headers.common['Authorization'] = `Bearer ${token.value}`;
            const response = await axios.get('/api/user');
            user.value = response.data;
            localStorage.setItem('user', JSON.stringify(user.value));
            return user.value;
        } catch (error) {
            console.error('Fetch user error:', error);
            if (error.response?.status === 401) {
                token.value = null;
                user.value = null;
                localStorage.removeItem('access_token');
                localStorage.removeItem('user');
                delete axios.defaults.headers.common['Authorization'];
            }
            return null;
        }
    }

    // Initialize user from localStorage if token exists
    function initialize() {
        const savedUser = localStorage.getItem('user');
        const savedToken = localStorage.getItem('access_token');

        console.log('Initializing auth store:', {
            hasToken: !!savedToken,
            hasUser: !!savedUser
        });

        if (savedToken) {
            token.value = savedToken;
            axios.defaults.headers.common['Authorization'] = `Bearer ${savedToken}`;
        }

        if (savedUser && savedToken) {
            try {
                user.value = JSON.parse(savedUser);
                console.log('User restored from localStorage:', user.value);
            } catch (e) {
                console.error('Error parsing saved user:', e);
                localStorage.removeItem('user');
            }
        }
    }

    // Call initialize
    initialize();

    return {
        user,
        token,
        isAuthenticated,
        login,
        logout,
        fetchUser,
        initialize
    };
});



export const useOrderStore = defineStore('orders', () => {
    const orders = ref([]);
    const currentOrder = ref(null);
    const loading = ref(false);
    const pagination = ref({
        current_page: 1,
        last_page: 1,
        per_page: 20,
        total: 0
    });

    async function fetchOrders(filters = {}) {
        loading.value = true;
        try {
            const response = await axios.get('/api/orders', { params: filters });
            orders.value = response.data.data;
            pagination.value = response.data.meta || response.data.pagination;
        } catch (error) {
            console.error('Fetch orders error:', error);
        } finally {
            loading.value = false;
        }
    }

    async function fetchOrder(id) {
        loading.value = true;
        try {
            const response = await axios.get(`/api/orders/${id}`);
            currentOrder.value = response.data;
            return response.data;
        } catch (error) {
            console.error('Fetch order error:', error);
        } finally {
            loading.value = false;
        }
    }

    async function createOrder(data) {
        loading.value = true;
        try {
            const response = await axios.post('/api/orders', data);
            return { success: true, data: response.data };
        } catch (error) {
            return { success: false, errors: error.response?.data?.errors || {} };
        } finally {
            loading.value = false;
        }
    }

    async function updateOrder(id, data) {
        loading.value = true;
        try {
            const response = await axios.put(`/api/orders/${id}`, data);
            return { success: true, data: response.data };
        } catch (error) {
            return { success: false, errors: error.response?.data?.errors || {} };
        } finally {
            loading.value = false;
        }
    }

    async function deleteOrder(id) {
        loading.value = true;
        try {
            await axios.delete(`/api/orders/${id}`);
            return { success: true };
        } catch (error) {
            return { success: false, error: error.response?.data?.message };
        } finally {
            loading.value = false;
        }
    }

    return {
        orders,
        currentOrder,
        loading,
        pagination,
        fetchOrders,
        fetchOrder,
        createOrder,
        updateOrder,
        deleteOrder
    };
});

export const useProductStore = defineStore('products', () => {
    const products = ref([]);
    const currentProduct = ref(null);
    const loading = ref(false);
    const pagination = ref({
        current_page: 1,
        last_page: 1,
        per_page: 20,
        total: 0
    });

    async function fetchProducts(filters = {}) {
        loading.value = true;
        try {
            const response = await axios.get('/api/products', { params: filters });
            products.value = response.data.data;
            pagination.value = response.data.meta || response.data.pagination;
        } catch (error) {
            console.error('Fetch products error:', error);
        } finally {
            loading.value = false;
        }
    }

    async function fetchProduct(id) {
        loading.value = true;
        try {
            const response = await axios.get(`/api/products/${id}`);
            currentProduct.value = response.data;
            return response.data;
        } catch (error) {
            console.error('Fetch product error:', error);
        } finally {
            loading.value = false;
        }
    }

    async function createProduct(data) {
        loading.value = true;
        try {
            const response = await axios.post('/api/products', data);
            return { success: true, data: response.data };
        } catch (error) {
            return { success: false, errors: error.response?.data?.errors || {} };
        } finally {
            loading.value = false;
        }
    }

    async function updateProduct(id, data) {
        loading.value = true;
        try {
            const response = await axios.put(`/api/products/${id}`, data);
            return { success: true, data: response.data };
        } catch (error) {
            return { success: false, errors: error.response?.data?.errors || {} };
        } finally {
            loading.value = false;
        }
    }

    async function deleteProduct(id) {
        loading.value = true;
        try {
            await axios.delete(`/api/products/${id}`);
            return { success: true };
        } catch (error) {
            return { success: false, error: error.response?.data?.message };
        } finally {
            loading.value = false;
        }
    }

    return {
        products,
        currentProduct,
        loading,
        pagination,
        fetchProducts,
        fetchProduct,
        createProduct,
        updateProduct,
        deleteProduct
    };
});

export const useCartStore = defineStore('cart', () => {
    const items = ref([]);
    const loading = ref(false);

    const totalItems = computed(() =>
        items.value.reduce((sum, item) => sum + item.quantity, 0)
    );

    const subtotal = computed(() =>
        items.value.reduce((sum, item) => sum + (item.price * item.quantity), 0)
    );

    const total = computed(() => subtotal.value);

    async function fetchCart() {
        loading.value = true;
        try {
            const response = await axios.get('/api/cart');
            items.value = response.data;
        } catch (error) {
            console.error('Fetch cart error:', error);
        } finally {
            loading.value = false;
        }
    }

    async function addItem(productId, quantity = 1, attributes = {}) {
        loading.value = true;
        try {
            const response = await axios.post('/api/cart/add', {
                product_id: productId,
                quantity,
                attributes
            });
            await fetchCart();
            return { success: true, data: response.data };
        } catch (error) {
            return { success: false, error: error.response?.data?.message };
        } finally {
            loading.value = false;
        }
    }

    async function updateItem(itemId, quantity) {
        loading.value = true;
        try {
            await axios.put(`/api/cart/update/${itemId}`, { quantity });
            await fetchCart();
            return { success: true };
        } catch (error) {
            return { success: false, error: error.response?.data?.message };
        } finally {
            loading.value = false;
        }
    }

    async function removeItem(itemId) {
        loading.value = true;
        try {
            await axios.delete(`/api/cart/remove/${itemId}`);
            await fetchCart();
            return { success: true };
        } catch (error) {
            return { success: false, error: error.response?.data?.message };
        } finally {
            loading.value = false;
        }
    }

    async function clearCart() {
        loading.value = true;
        try {
            await axios.delete('/api/cart/clear');
            items.value = [];
            return { success: true };
        } catch (error) {
            return { success: false, error: error.response?.data?.message };
        } finally {
            loading.value = false;
        }
    }

    return {
        items,
        loading,
        totalItems,
        subtotal,
        total,
        fetchCart,
        addItem,
        updateItem,
        removeItem,
        clearCart
    };
});