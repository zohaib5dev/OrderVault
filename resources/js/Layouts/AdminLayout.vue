 <template>
  <div :class="['min-h-screen transition-colors duration-300', isDarkMode ? 'dark bg-gray-900' : 'bg-gray-50']">
      <nav
      class="fixed w-full z-30 top-0 p-0 transition-all duration-300"
      :class="[
        isDarkMode 
          ? 'bg-gray-800/90 backdrop-blur-md border-b border-gray-700' 
          : 'bg-white/90 backdrop-blur-md border-b border-gray-200 shadow-sm'
      ]"
    >
      <div class="px-3 py-3 lg:py-4 lg:px-5 lg:pl-3">
        <div class="flex items-center justify-between">
          <div class="flex items-center justify-start">
            <!-- Mobile menu button -->
            <button 
              @click="toggleSidebar" 
              class="inline-flex items-center p-2 text-sm rounded-lg lg:hidden focus:outline-none focus:ring-2"
              :class="[
                isDarkMode 
                  ? 'text-gray-300 hover:bg-gray-700 focus:ring-gray-600' 
                  : 'text-gray-600 hover:bg-gray-100 focus:ring-gray-200'
              ]"
            >
              <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/>
              </svg>
            </button>
            
            <!-- Logo -->
          <router-link to="/admin/dashboard" class="flex items-center ml-2 md:mr-24">
            <img 
              v-if="logoUrl && logoUrl.includes('/storage/company-logos/')" 
              :src="logoUrl" 
              alt="Logo" 
              class="h-8 w-auto object-contain"
            />
            <svg v-else class="h-8 w-8 text-blue-600 dark:text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4zm0 5c0 2.21 3.582 4 8 4s8-1.79 8-4"/>
            </svg>
            <span class="ml-2 text-xl font-bold" :class="isDarkMode ? 'text-white' : 'text-gray-800'">OrderValut</span>
          </router-link>
          </div>

          <!-- Right side nav items -->
          <div class="flex items-center gap-3">
            <!-- Dark Mode Toggle -->
            <button
              @click="toggleDarkMode"
              class="p-2 rounded-lg transition-all duration-200"
              :class="[
                isDarkMode 
                  ? 'text-yellow-400 hover:bg-gray-700 hover:text-yellow-300' 
                  : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900'
              ]"
              :title="isDarkMode ? 'Switch to Light Mode' : 'Switch to Dark Mode'"
            >
              <svg v-if="!isDarkMode" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"/>
              </svg>
              <svg v-else class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"/>
              </svg>
            </button>

            <!-- User Dropdown -->
            <div class="relative" @click.stop="toggleDropdown">
              <button class="flex items-center text-sm rounded-full focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-600" :class="isDarkMode ? 'bg-gray-700' : 'bg-gray-100'">
                <span class="sr-only">Open user menu</span>
                <div class="w-8 h-8 rounded-full bg-blue-600 dark:bg-blue-500 flex items-center justify-center text-white font-semibold">
                  {{ getInitials(adminName) }}
                </div>
              </button>
              
              <!-- Dropdown menu -->
              <div v-if="isDropdownOpen" class="absolute right-0 mt-2 w-48 rounded-lg shadow-2xl py-1 z-50 border" :class="[isDarkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-200']">
                <div class="px-4 py-3 border-b" :class="isDarkMode ? 'border-gray-700' : 'border-gray-100'">
                  <p class="text-sm font-medium" :class="isDarkMode ? 'text-white' : 'text-gray-900'">{{ adminName }}</p>
                  <p class="text-xs truncate" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">{{ authStore.user?.value?.email || 'admin@example.com' }}</p>
                </div>
                <router-link to="/admin/profile" class="flex items-center px-4 py-2 text-sm transition-colors" :class="[isDarkMode ? 'text-gray-300 hover:bg-gray-700 hover:text-white' : 'text-gray-700 hover:bg-gray-100']">
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                  </svg>
                  Profile
                </router-link>
                <div class="border-t" :class="isDarkMode ? 'border-gray-700' : 'border-gray-100'"></div>
                <button @click="logout" class="flex items-center w-full px-4 py-2 text-sm text-red-600 dark:text-red-400 transition-colors" :class="isDarkMode ? 'hover:bg-red-900/20' : 'hover:bg-red-50'">
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                  </svg>
                  Logout
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </nav>

    <!-- Sidebar -->
<aside 
  :class="[
    'fixed left-0 z-40 w-64 h-[calc(100vh-64px)] transition-transform duration-300 border-r',
    isSidebarOpen ? 'translate-x-0' : '-translate-x-full',
    'lg:translate-x-0',
    isDarkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-200'
  ]"
  :style="{ top: '64px' }"
>
      <div class="h-full px-3 pb-4 overflow-y-auto">
        <ul class="space-y-1 font-medium">
          <!-- Dashboard -->
          <li>
            <router-link 
              to="/admin/dashboard" 
              :class="[
                'flex items-center p-2 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20 group',
                $route.path === '/admin/dashboard' 
                  ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400' 
                  : isDarkMode ? 'text-gray-300 hover:text-white' : 'text-gray-700 hover:text-gray-900'
              ]"
              @click="closeSidebarOnMobile"
            >
              <svg class="w-5 h-5 transition duration-75 group-hover:text-blue-600 dark:group-hover:text-blue-400" :class="[$route.path === '/admin/dashboard' ? 'text-blue-600 dark:text-blue-400' : (isDarkMode ? 'text-gray-400' : 'text-gray-500')]" fill="currentColor" viewBox="0 0 20 20">
                <path d="M3 12a2 2 0 100 4h14a2 2 0 100-4H3zM3 6a2 2 0 100 4h14a2 2 0 100-4H3zM3 14h14M3 8h14"/>
              </svg>
              <span class="ml-3">Dashboard</span>
            </router-link>
          </li>


          <li>
            <router-link 
              to="/admin/orders" 
              :class="[
                'flex items-center p-2 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20 group',
                $route.path === '/admin/orders' 
                  ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400' 
                  : isDarkMode ? 'text-gray-300 hover:text-white' : 'text-gray-700 hover:text-gray-900'
              ]"
              @click="closeSidebarOnMobile"
            >
             <svg class="w-5 h-5 transition duration-75 group-hover:text-blue-600 dark:group-hover:text-blue-400" :class="(isDarkMode ? 'text-gray-400' : 'text-gray-500')" fill="currentColor" viewBox="0 0 20 20">
                <path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3.5-7A1 1 0 0017.5 2H6.55l-.31-1.243A1 1 0 005.22 1H3zm3.5 4a.5.5 0 000 1h10a.5.5 0 000-1h-10zM6 17a2 2 0 100 4 2 2 0 000-4zm9 0a2 2 0 100 4 2 2 0 000-4z"/>
              </svg>
              <span class="ml-3">Orders</span>
            </router-link>
          </li>
          
          <li>
            <router-link 
              to="/admin/categories" 
              :class="[
                'flex items-center p-2 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20 group',
                $route.path === '/admin/categories' 
                  ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400' 
                  : isDarkMode ? 'text-gray-300 hover:text-white' : 'text-gray-700 hover:text-gray-900'
              ]"
              @click="closeSidebarOnMobile"
            >
              <svg class="w-5 h-5 transition duration-75 group-hover:text-blue-600 dark:group-hover:text-blue-400" :class="(isDarkMode ? 'text-gray-400' : 'text-gray-500')" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
              </svg>
              <span class="ml-3">categories</span>
            </router-link>
          </li>
          
          
          <li>
            <router-link 
              to="/admin/products" 
              :class="[
                'flex items-center p-2 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20 group',
                $route.path === '/admin/products' 
                  ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400' 
                  : isDarkMode ? 'text-gray-300 hover:text-white' : 'text-gray-700 hover:text-gray-900'
              ]"
              @click="closeSidebarOnMobile"
            >
              <svg class="w-5 h-5 transition duration-75 group-hover:text-blue-600 dark:group-hover:text-blue-400" :class="(isDarkMode ? 'text-gray-400' : 'text-gray-500')" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
              </svg>
              <span class="ml-3">Products</span>
            </router-link>
          </li>
          
          <li>
            <router-link 
              to="/admin/customers" 
              :class="[
                'flex items-center p-2 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20 group',
                $route.path === '/admin/customers' 
                  ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400' 
                  : isDarkMode ? 'text-gray-300 hover:text-white' : 'text-gray-700 hover:text-gray-900'
              ]"
              @click="closeSidebarOnMobile"
            >
              <svg class="w-5 h-5 transition duration-75 group-hover:text-blue-600 dark:group-hover:text-blue-400" :class="(isDarkMode ? 'text-gray-400' : 'text-gray-500')" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
              </svg>
              <span class="ml-3">Customers</span>
            </router-link>
          </li>
          
          <li>
            <router-link 
              to="/admin/analytics" 
              :class="[
                'flex items-center p-2 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20 group',
                $route.path === '/admin/analytics' 
                  ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400' 
                  : isDarkMode ? 'text-gray-300 hover:text-white' : 'text-gray-700 hover:text-gray-900'
              ]"
              @click="closeSidebarOnMobile"
            >
              <svg class="w-5 h-5 transition duration-75 group-hover:text-blue-600 dark:group-hover:text-blue-400" :class="(isDarkMode ? 'text-gray-400' : 'text-gray-500')" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
              </svg>
              <span class="ml-3">Analytics</span>
            </router-link>
          </li>

          <li>
            <router-link 
              to="/admin/settings" 
              :class="[
                'flex items-center p-2 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20 group',
                $route.path === '/admin/settings' 
                  ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400' 
                  : isDarkMode ? 'text-gray-300 hover:text-white' : 'text-gray-700 hover:text-gray-900'
              ]"
              @click="closeSidebarOnMobile"
            >
              <svg class="w-5 h-5 transition duration-75 group-hover:text-blue-600 dark:group-hover:text-blue-400" :class="(isDarkMode ? 'text-gray-400' : 'text-gray-500')" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
              </svg>
              <span class="ml-3">Settings</span>
            </router-link>
          </li>
          
        </ul>
      </div>
    </aside>

    <!-- Main Content -->
    <div class="p-4 lg:ml-64 mt-16">
      <div class="p-4 rounded-lg">
        <router-view />
      </div>
    </div>

    <!-- Mobile sidebar backdrop -->
    <div 
      v-if="isSidebarOpen" 
      @click="toggleSidebar"
      class="fixed inset-0 z-30 bg-gray-900 bg-opacity-50 lg:hidden"
    ></div>
  </div>
</template>

 

<script>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';
import { useAuthStore } from '../store';
import { useRoute } from 'vue-router';
import axios from 'axios';

export default {
    setup() {
        const authStore = useAuthStore();
        const route = useRoute();
        const isSidebarOpen = ref(false);
        const isDropdownOpen = ref(false); 
        const isDarkMode = ref(false);
        
        // Use a ref for logoUrl to make it reactive
        const logoUrl = ref(null);

        // Function to update logo from localStorage
        const updateLogoFromStorage = () => {
            const storedLogo = localStorage.getItem('logo');
            if (storedLogo) {
                try {
                    const logoData = JSON.parse(storedLogo);
                    logoUrl.value = logoData.url || null;
                } catch {
                    logoUrl.value = storedLogo;
                }
            } else {
                logoUrl.value = null;
            }
        };

        // Initial logo load
        updateLogoFromStorage();

        const adminName = computed(() => {
            const user = authStore.user?.value;
            return user?.name || 'Admin';
        });

        const getInitials = (name) => {
            if (!name) return 'A';
            return name.split(' ').map(word => word[0]).join('').toUpperCase().slice(0, 2);
        };

        const toggleSidebar = () => {
            isSidebarOpen.value = !isSidebarOpen.value;
        };

        const closeSidebarOnMobile = () => {
            if (window.innerWidth < 1024) {
                isSidebarOpen.value = false;
            }
        };

        const toggleDropdown = () => {
            isDropdownOpen.value = !isDropdownOpen.value;
        };

        const loadThemePreference = () => {
            const savedTheme = localStorage.getItem('theme');
            if (savedTheme === 'dark') {
                isDarkMode.value = true;
                document.documentElement.classList.add('dark');
            } else if (savedTheme === 'light') {
                isDarkMode.value = false;
                document.documentElement.classList.remove('dark');
            } else if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
                isDarkMode.value = true;
                document.documentElement.classList.add('dark');
            } else {
                isDarkMode.value = false;
                document.documentElement.classList.remove('dark');
            }
        };

        const toggleDarkMode = () => {
            isDarkMode.value = !isDarkMode.value;
            if (isDarkMode.value) {
                document.documentElement.classList.add('dark');
                localStorage.setItem('theme', 'dark');
            } else {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('theme', 'light');
            }
        };

        const logout = async () => {
            await authStore.logout();
            window.location.href = '/login';
        };

        const handleClickOutside = (event) => {
            if (!event.target.closest('.relative')) {
                isDropdownOpen.value = false;
            }
        };

        // Listen for logo updates
        const handleLogoUpdate = (event) => {
            updateLogoFromStorage();
        };

        // Listen for storage changes (for cross-tab)
        const handleStorageChange = (event) => {
            if (event.key === 'logo') {
                updateLogoFromStorage();
            }
        };


        onMounted(() => {
            document.addEventListener('click', handleClickOutside);
             loadThemePreference();

            // Listen for custom logo-updated event
            window.addEventListener('logo-updated', handleLogoUpdate);
            
            // Listen for storage events (cross-tab)
            window.addEventListener('storage', handleStorageChange);

            const darkModeMediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
            const handleSystemThemeChange = (e) => {
                if (!localStorage.getItem('theme')) {
                    isDarkMode.value = e.matches;
                    if (e.matches) {
                        document.documentElement.classList.add('dark');
                    } else {
                        document.documentElement.classList.remove('dark');
                    }
                }
            };
            darkModeMediaQuery.addEventListener('change', handleSystemThemeChange);
        });

        onUnmounted(() => {
            document.removeEventListener('click', handleClickOutside);
            window.removeEventListener('logo-updated', handleLogoUpdate);
            window.removeEventListener('storage', handleStorageChange);
        });

        return {
            authStore,
            adminName,
            logoUrl,
            isSidebarOpen,
            isDropdownOpen, 
            isDarkMode,
            getInitials,
            toggleSidebar,
            closeSidebarOnMobile,
            toggleDropdown,
            toggleDarkMode,
            logout
        };
    }
};
</script>

<style scoped>
.router-link-active {
    background-color: #eff6ff;
    color: #2563eb;
}

.dark .router-link-active {
    background-color: rgba(37, 99, 235, 0.2);
    color: #60a5fa;
}

.overflow-y-auto::-webkit-scrollbar {
    width: 4px;
}

.overflow-y-auto::-webkit-scrollbar-track {
    background: transparent;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
    background: #d1d5db;
    border-radius: 2px;
}

.dark .overflow-y-auto::-webkit-scrollbar-thumb {
    background: #4b5563;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background: #9ca3af;
}

.dark .overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background: #6b7280;
}

.transition-transform {
    transition: transform 0.3s ease-in-out;
}

.relative .absolute {
    animation: slideDown 0.2s ease-out;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.rotate-90 {
    transform: rotate(90deg);
}
</style>