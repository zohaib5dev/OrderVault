<template>
  <div :class="['min-h-screen transition-colors duration-300', isDarkMode ? 'dark bg-gray-900' : 'bg-gray-50']">
    <!-- Top Navigation Bar -->
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
            <!-- Logo -->
            <router-link to="/customer/dashboard" class="flex items-center ml-2 md:mr-24">
              <img 
                v-if="logoUrl && logoUrl.includes('/storage/logos/')" 
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
                  {{ getInitials(customerName) }}
                </div>
              </button>
              
              <!-- Dropdown menu -->
              <div v-if="isDropdownOpen" class="absolute right-0 mt-2 w-48 rounded-lg shadow-2xl py-1 z-50 border" :class="[isDarkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-200']">
                <div class="px-4 py-3 border-b" :class="isDarkMode ? 'border-gray-700' : 'border-gray-100'">
                  <p class="text-sm font-medium" :class="isDarkMode ? 'text-white' : 'text-gray-900'">{{ customerName }}</p>
                  <p class="text-xs truncate" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">{{ authStore.user?.email || 'customer@example.com' }}</p>
                </div>
                <router-link to="/customer/profile" class="flex items-center px-4 py-2 text-sm transition-colors" :class="[isDarkMode ? 'text-gray-300 hover:bg-gray-700 hover:text-white' : 'text-gray-700 hover:bg-gray-100']">
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

    <!-- Main Content -->
    <div class="p-4  mt-16">
      <div class="p-4 rounded-lg">
        <router-view />
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { useAuthStore } from '../store';

export default {
    setup() {
        const authStore = useAuthStore();
        const isDropdownOpen = ref(false);
        const isDarkMode = ref(false);
        const logoUrl = ref(null);

        // Get logo from localStorage
        const loadLogo = () => {
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

        // Load logo on mount
        loadLogo();

        const customerName = computed(() => {
            return authStore.user?.name || 'Customer';
        });

        const getInitials = (name) => {
            if (!name) return 'A';
            return name.split(' ').map(word => word[0]).join('').toUpperCase().slice(0, 2);
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

        onMounted(() => {
            document.addEventListener('click', handleClickOutside);
            loadThemePreference();

            // Listen for system theme changes
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

            // Listen for storage changes for logo
            window.addEventListener('storage', (event) => {
                if (event.key === 'logo') {
                    loadLogo();
                }
            });
        });

        onUnmounted(() => {
            document.removeEventListener('click', handleClickOutside);
        });

        return {
            authStore,
            customerName,
            logoUrl,
            isDropdownOpen,
            isDarkMode,
            getInitials,
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
</style>