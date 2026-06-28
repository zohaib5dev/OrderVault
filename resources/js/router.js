import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from './store';

const routes = [
    {
        path: '/',
        name: '',
        component: () => import('./Pages/Auth/Login.vue'),
        meta: { guest: true }
    },
    {
        path: '/login',
        name: 'Login',
        component: () => import('./Pages/Auth/Login.vue'),
        meta: { guest: true }
    },
    {
        path: '/register',
        name: 'Register',
        component: () => import('./Pages/Auth/Register.vue'),
        meta: { guest: true }
    },

    {
        path: '/customer',
        component: () => import('./Layouts/CustomerLayout.vue'),
        meta: { requiresAuth: true, role: 'customer' },
        children: [
            {
                path: 'dashboard',
                name: 'CustomerDashboard',
                component: () => import('./Pages/Customer/Dashboard.vue')
            },
            {
                path: 'profile',
                name: 'CustomerProfile',
                component: () => import('./Pages/Profile/Index.vue')
            },
            {
                path: 'orders/:id',
                name: 'CustomerOrder',
                component: () => import('./Pages/Customer/Order.vue')
            },
            {
                path: 'orders/:id/payment',
                name: 'CustomerOrderPayment',
                component: () => import('./Pages/Customer/Payment.vue')
            },
        ]
    },

    {
        path: '/admin',
        component: () => import('./Layouts/AdminLayout.vue'),
        meta: { requiresAuth: true, role: 'admin' },
        children: [
            {
                path: 'dashboard',
                name: 'AdminDashboard',
                component: () => import('./Pages/Admin/Dashboard.vue')
            },
            {
                path: 'settings',
                name: 'AdminSettings',
                component: () => import('./Pages/Admin/Settings.vue')
            },

            {
                path: 'profile',
                name: 'AdminProfile',
                component: () => import('./Pages/Profile/Index.vue')
            },

            {
                path: 'products',
                name: 'AdminProducts',
                component: () => import('./Pages/Admin/Products/Index.vue')
            },
            {
                path: 'products/create',
                name: 'AdminProductCreate',
                component: () => import('./Pages/Admin/Products/Create.vue')
            },
            {
                path: 'products/:id',
                name: 'AdminProductshow',
                component: () => import('./Pages/Admin/Products/Show.vue')
            },
            {
                path: 'products/:id/edit',
                name: 'AdminProductEdit',
                component: () => import('./Pages/Admin/Products/Edit.vue')
            },
            {
                path: 'orders',
                name: 'AdminOrders',
                component: () => import('./Pages/Admin/Orders/Index.vue')
            }, 
            {
                path: 'orders/create',
                name: 'AdminOrderCreate',
                component: () => import('./Pages/Admin/Orders/Create.vue')
            },
            {
                path: 'orders/:id',
                name: 'AdminOrderShow',
                component: () => import('./Pages/Admin/Orders/Show.vue')
            },
            {
                path: 'orders/:id/edit',
                name: 'AdminOrderEdit',
                component: () => import('./Pages/Admin/Orders/Edit.vue')
            },
            {
                path: 'customers',
                name: 'AdminCustomers',
                component: () => import('./Pages/Admin/Customers/Index.vue')
            },
            {
                path: 'customers/create',
                name: 'AdminCustomersCreate',
                component: () => import('./Pages/Admin/Customers/Create.vue')
            },
            {
                path: 'customers/:id',
                name: 'AdminCustomerShow',
                component: () => import('./Pages/Admin/Customers/Show.vue')
            },
            {
                path: 'customers/:id/edit',
                name: 'AdminCustomerEdit',
                component: () => import('./Pages/Admin/Customers/Edit.vue')
            },
            {
                path: 'analytics',
                name: 'AdminAnalytics',
                component: () => import('./Pages/Admin/Analytics.vue')
            },
            {
                path: 'categories',
                name: 'AdminCategories',
                component: () => import('./Pages/Admin/Categories/Index.vue')
            },

        ]
    },

];

const router = createRouter({
    history: createWebHistory(),
    routes
});

router.beforeEach((to, from, next) => {
    const authStore = useAuthStore();
    const isAuthenticated = authStore.isAuthenticated;
    const userRole = authStore.user?.role;


    // Check if route requires authentication
    if (to.meta.requiresAuth && !isAuthenticated) {
        next('/login');
        return;
    }

    // Check if route has role restrictions
    if (to.meta.requiresAuth && to.meta.role) {
        const allowedRoles = Array.isArray(to.meta.role) ? to.meta.role : [to.meta.role];

        // Map super_admin to admin for routing purposes
        const normalizedUserRole = userRole === 'super_admin' ? 'admin' : userRole;

        if (!allowedRoles.includes(normalizedUserRole) && !allowedRoles.includes(userRole)) {
            // Redirect to appropriate dashboard based on role
            if (userRole === 'super_admin' || userRole === 'admin') {
                next('/admin/dashboard');
            } else if (userRole === 'customer') {
                next('/customer/dashboard');
            } else {
                next('/');
            }
            return;
        }
    }

    // Redirect guest users away from auth pages
    if (to.meta.guest && isAuthenticated) {
        const userRole = authStore.user?.role;
        if (userRole === 'super_admin' || userRole === 'admin') {
            next('/admin/dashboard');
        } else if (userRole === 'customer') {
            next('/customer/dashboard');
        } else {
            next('/');
        }
        return;
    }

    next();
});

export default router;