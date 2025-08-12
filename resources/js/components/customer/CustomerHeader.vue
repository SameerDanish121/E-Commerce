<!-- 

<template>
  <header class="sticky-top">
    <div class="container-fluid px-3 px-md-4">
      <div class="row align-items-center py-2 py-md-3">
    
        <div class="col-6 col-md-4 col-lg-3">
          <router-link to="/home" class="text-decoration-none d-flex align-items-center">
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-shopping-bag me-2">
              <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
              <line x1="3" y1="6" x2="21" y2="6"></line>
              <path d="M16 10a4 4 0 0 1-8 0"></path>
            </svg>
            <h1 class="m-0 fs-3 fw-bold text-primary">ShopMaster</h1>
          </router-link>
        </div>
      
        <div class="col-6 col-md-8 col-lg-9 d-flex">
          <nav class="sameer navbar navbar-expand-lg w-100 px-0">
            <button class="navbar-toggler border-0 p-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
              <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-menu">
                <line x1="3" y1="12" x2="21" y2="12"></line>
                <line x1="3" y1="6" x2="21" y2="6"></line>
                <line x1="3" y1="18" x2="21" y2="18"></line>
              </svg>
            </button>
            
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
              <button class="close-menu-btn d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x">
                  <line x1="18" y1="6" x2="6" y2="18"></line>
                  <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
              </button>
              <ul class="navbar-nav align-items-lg-center gap-lg-3">
                <li class="nav-item" v-for="route in routes" :key="route.name">
                  <router-link 
                    :to="route.path" 
                    class="nav-link position-relative px-3 py-2 d-flex align-items-center"
                    :class="{ 'active': isActive(route.path) }"
                  >
                    <span class="nav-icon me-2">
                      <component :is="getRouteIcon(route.name)" />
                    </span>
                    <span class="nav-text">{{ route.name }}</span>
                  </router-link>
                </li>
              
                <li class="nav-item dropdown ms-lg-3">
                  <a class="nav-link dropdown-toggle p-0 d-flex align-items-center" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="profile-pic-container">
                      <img v-if="customerStore.profile_pic" :src="customerStore.profile_pic" class="profile-pic rounded-circle" alt="Profile">
                      <div v-else class="profile-placeholder rounded-circle d-flex align-items-center justify-content-center bg-secondary text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user">
                          <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                          <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                      </div>
                      <span class="profile-name ms-2 d-none d-lg-inline">{{ customerStore.name }}</span>
                    </div>
                  </a>
                  <ul class="dropdown-menu dropdown-menu-end border-0 shadow" aria-labelledby="profileDropdown">
                    <li>
                      <router-link to="/profile" class="dropdown-item d-flex align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user me-2">
                          <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                          <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        Profile
                      </router-link>
                    </li>
                    <li><hr class="dropdown-divider my-1"></li>
                    <li>
                      <button @click="handleLogout" class="dropdown-item d-flex align-items-center text-danger">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-log-out me-2">
                          <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                          <polyline points="16 17 21 12 16 7"></polyline>
                          <line x1="21" y1="12" x2="9" y2="12"></line>
                        </svg>
                        Logout
                      </button>
                    </li>
                  </ul>
                </li>
              </ul>
            </div>
          </nav>
        </div>
      </div>
      <div class="border-bottom"></div>
    </div>
  </header>
</template>

<script>
import { routes } from '../../customer_router';
import { useCustomerStore } from '../../stores/customerStore';
import { useRouter } from 'vue-router';

const HomeIcon = {
  template: `
    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-home">
      <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
      <polyline points="9 22 9 12 15 12 15 22"></polyline>
    </svg>
  `
};

const ProductsIcon = {
  template: `
    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-shopping-cart">
      <circle cx="9" cy="21" r="1"></circle>
      <circle cx="20" cy="21" r="1"></circle>
      <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
    </svg>
  `
};

const OrdersIcon = {
  template: `
    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-package">
      <line x1="16.5" y1="9.4" x2="7.5" y2="4.21"></line>
      <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
      <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
      <line x1="12" y1="22.08" x2="12" y2="12"></line>
    </svg>
  `
};

export default {
  name: 'CustomerHeader',
  components: {
    HomeIcon,
    ProductsIcon,
    OrdersIcon
  },
  setup() {
    const customerStore = useCustomerStore();
    const router = useRouter();

    const handleLogout = () => {
      customerStore.logout();
      window.location.href='/';
    };

    return {
      customerStore,
      handleLogout
    };
  },
  data() {
    return {
      routes: routes.filter(route => route.name && route.name !== 'login' && route.name !== 'signup')
    }
  },
  methods: {
    isActive(path) {
      return this.$route.path.startsWith(path);
    },
    getRouteIcon(routeName) {
      switch(routeName.toLowerCase()) {
        case 'home': return 'HomeIcon';
        case 'products': return 'ProductsIcon';
        case 'orders': return 'OrdersIcon';
        default: return 'HomeIcon';
      }
    }
  }
}
</script>

<style scoped>
.sameer{
justify-content: end;
}
header {
  background-color: white;
  box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08);
  z-index: 1030;
}

.navbar-toggler:focus {
  box-shadow: none;
}

.nav-link {
  font-weight: 500;
  color: var(--bs-gray-700);
  border-radius: 8px;
  transition: all 0.3s ease;
  position: relative;
  display: flex;
  align-items: center;
}

.nav-link:hover {
  color: var(--bs-primary);
  background-color: rgba(108, 117, 125, 0.1);
}

.nav-link:hover .nav-icon {
  transform: translateY(-2px);
}

.nav-link.active {
  color: var(--bs-primary);
  font-weight: 600;
}

.nav-link.active::after {
  content: '';
  position: absolute;
  bottom: -12px;
  left: 0;
  width: 100%;
  height: 3px;
  background-color: var(--bs-primary);
  border-radius: 3px;
}

.nav-icon {
  transition: all 0.3s ease;
  display: inline-flex;
}

.nav-text {
  transition: all 0.3s ease;
}
.profile-pic-container {
  display: flex;
  align-items: center;
}

.profile-pic {
  width: 32px;
  height: 32px;
  object-fit: cover;
}

.profile-placeholder {
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.profile-name {
  font-weight: 500;
  color: var(--bs-gray-800);
}

.dropdown-menu {
  min-width: 200px;
}

.dropdown-item {
  padding: 0.5rem 1rem;
}

@media (max-width: 991.98px) {
  .navbar-collapse {
    position: fixed;
    top: 0;
    right: 0;
    bottom: 0;
    left: auto;
    width: 280px;
    padding: 1rem;
    background-color: white;
    box-shadow: -5px 0 15px rgba(0, 0, 0, 0.1);
    z-index: 1050;
    overflow-y: auto;
    transform: translateX(100%);
    transition: transform 0.3s ease;
  }
  
  .navbar-collapse.show {
    transform: translateX(0);
  }
  
  .navbar-nav {
    padding-top: 2rem;
  }
  
  .nav-item {
    margin-bottom: 0.5rem;
  }
  
  .nav-link {
    padding: 0.75rem 1rem;
    justify-content: flex-start;
    border-radius: 6px;
  }
  
  .nav-link.active::after {
    bottom: auto;
    left: 0;
    top: 50%;
    transform: translateY(-50%);
    width: 3px;
    height: 60%;
    border-radius: 0 3px 3px 0;
  }
  
  .dropdown-menu {
    position: static !important;
    transform: none !important;
    border: none;
    box-shadow: none;
    margin-left: 2rem;
    background-color: rgba(0, 0, 0, 0.02);
  }
  
  .profile-name {
    display: inline !important;
  }

  .close-menu-btn {
    position: absolute;
    top: 1rem;
    right: 1rem;
    background: none;
    border: none;
    padding: 0.5rem;
    cursor: pointer;
    z-index: 1051;
  }
}
@media (min-width: 992px) {
  .nav-link {
    padding: 0.5rem 1rem;
  }
  
  .nav-text {
    display: inline !important;
  }

  .close-menu-btn {
    display: none;
  }
}

.navbar-collapse::before {
  content: '';
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.3);
  z-index: -1;
  opacity: 0;
  pointer-events: none; 
  transition: opacity 0.3s ease;
}

.navbar-collapse.show::before {
  opacity: 1;
  pointer-events: auto;
}
</style> -->

<template>
  <header class="sticky-top">
    <div class="container-fluid px-3 px-md-4">
      <div class="row align-items-center py-2 py-md-3">

        <div class="col-6 col-md-4 col-lg-3">
          <router-link to="/home" class="text-decoration-none d-flex align-items-center">
            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
              class="feather feather-shopping-bag me-2">
              <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
              <line x1="3" y1="6" x2="21" y2="6"></line>
              <path d="M16 10a4 4 0 0 1-8 0"></path>
            </svg>
            <h1 class="m-0 fs-4 fs-md-3 fw-bold text-primary">ShopMaster</h1>
          </router-link>
        </div>

        <div class="col-6 col-md-8 col-lg-9 d-flex">
          <nav class="sameer navbar navbar-expand-lg w-100 px-0">
            <button class="navbar-toggler border-0 p-0" type="button" data-bs-toggle="collapse"
              data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false"
              aria-label="Toggle navigation">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="feather feather-menu">
                <line x1="3" y1="12" x2="21" y2="12"></line>
                <line x1="3" y1="6" x2="21" y2="6"></line>
                <line x1="3" y1="18" x2="21" y2="18"></line>
              </svg>
            </button>

            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
              <button class="close-menu-btn d-lg-none" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarNav">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                  stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                  class="feather feather-x">
                  <line x1="18" y1="6" x2="6" y2="18"></line>
                  <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
              </button>
              <ul class="navbar-nav align-items-lg-center gap-lg-3">
                <li class="nav-item" v-for="route in routes" :key="route.name">
                  <router-link :to="route.path"
                    class="nav-link position-relative px-2 px-lg-3 py-2 d-flex align-items-center"
                    :class="{ 'active': isActive(route.path) }">
                    <span class="nav-icon me-1 me-lg-2">
                      <component :is="getRouteIcon(route.name)" />
                    </span>
                    <span class="nav-text">{{ route.name }}</span>
                  </router-link>
                </li>

                <li class="nav-item dropdown ms-lg-3">
                  <a class="nav-link dropdown-toggle p-0 d-flex align-items-center" href="#" id="profileDropdown"
                    role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="profile-pic-container">
                      <img v-if="customerStore.profile_pic" :src="customerStore.profile_pic"
                        class="profile-pic rounded-circle" alt="Profile">
                      <div v-else
                        class="profile-placeholder rounded-circle d-flex align-items-center justify-content-center bg-secondary text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                          stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                          class="feather feather-user">
                          <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                          <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                      </div>
                      <span class="profile-name ms-2 d-none d-lg-inline">{{ customerStore.name }}</span>
                    </div>
                  </a>
                  <ul class="dropdown-menu dropdown-menu-end border-0 shadow" aria-labelledby="profileDropdown">
                    <li>
                      <router-link to="/profile" class="dropdown-item d-flex align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                          stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                          class="feather feather-user me-2">
                          <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                          <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        Profile
                      </router-link>
                    </li>
                    <li>
                      <hr class="dropdown-divider my-1">
                    </li>
                    <li>
                      <button @click="handleLogout" class="dropdown-item d-flex align-items-center text-danger">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                          stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                          class="feather feather-log-out me-2">
                          <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                          <polyline points="16 17 21 12 16 7"></polyline>
                          <line x1="21" y1="12" x2="9" y2="12"></line>
                        </svg>
                        Logout
                      </button>
                    </li>
                  </ul>
                </li>
              </ul>
            </div>
          </nav>
        </div>
      </div>
      <div class="border-bottom"></div>
    </div>
  </header>
  <!-- Floating Action Buttons -->
  <div class="fab-container">
    <button class="fab fab-cart" @click="openCart">
      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
        <path fill="currentColor"
          d="M17 18a2 2 0 0 1 2 2a2 2 0 0 1-2 2a2 2 0 0 1-2-2c0-1.11.89-2 2-2M1 2h3.27l.94 2H20a1 1 0 0 1 1 1c0 .17-.05.34-.12.5l-3.58 6.47c-.34.61-1 1.03-1.75 1.03H8.1l-.9 1.63l-.03.12a.25.25 0 0 0 .25.25H19v2H7a2 2 0 0 1-2-2c0-.35.09-.68.24-.96l1.36-2.45L3 4H1V2m6 16a2 2 0 0 1 2 2a2 2 0 0 1-2 2a2 2 0 0 1-2-2c0-1.11.89-2 2-2m9-7l2.78-5H6.14l2.36 5H16z" />
      </svg>
    </button>

    <button class="fab fab-support" @click="openSupport">
      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
        <path fill="currentColor"
          d="M12 3a9 9 0 0 0-9 9c0 1.42.36 2.76 1 3.96l-1.5 4.44l4.44-1.5c1.2.64 2.54 1 3.96 1a9 9 0 0 0 9-9a9 9 0 0 0-9-9m0 16c-1.23 0-2.4-.32-3.42-.88l-.3-.18l-3.17 1.07l1.07-3.17l-.18-.3A7.93 7.93 0 0 1 4 12a8 8 0 0 1 8-8a8 8 0 0 1 8 8a8 8 0 0 1-8 8m-3.5-5.5l1.41-1.41L12 14.08l2.09-2.08l1.41 1.41L13.41 15.5l2.08 2.09l-1.41 1.41L12 16.91l-2.09 2.09l-1.41-1.41l2.09-2.09l-2.09-2.09Z" />
      </svg>
    </button>
  </div>
</template>

<script>
import { routes } from '../../customer_router';
import { useCustomerStore } from '../../stores/customerStore';
import { useRouter } from 'vue-router';

const HomeIcon = {
  template: `
    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-home">
      <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
      <polyline points="9 22 9 12 15 12 15 22"></polyline>
    </svg>
  `
};

const ProductsIcon = {
  template: `
    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-shopping-cart">
      <circle cx="9" cy="21" r="1"></circle>
      <circle cx="20" cy="21" r="1"></circle>
      <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
    </svg>
  `
};

const OrdersIcon = {
  template: `
    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-package">
      <line x1="16.5" y1="9.4" x2="7.5" y2="4.21"></line>
      <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
      <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
      <line x1="12" y1="22.08" x2="12" y2="12"></line>
    </svg>
  `
};


export default {
  name: 'CustomerHeader',
  components: {
    HomeIcon,
    ProductsIcon,
    OrdersIcon
  },
  setup() {
    const customerStore = useCustomerStore();
    const router = useRouter();
    const openCart = () => {

      console.log('Open cart');
      router.push('/customer/cart');
    };

    const openSupport = () => {
      console.log('Open support');
     window.location.href = '/chat';
    };
    const handleLogout = () => {
      localStorage.removeItem('savedCredentials');
      customerStore.logout();
      window.location.href = '/';
    };

    return {
      customerStore,
      handleLogout,
      openCart,
      openSupport
    };
  },
  data() {
    return {
      routes: routes.filter(route => route.name && route.name !== 'login' && route.name !== 'signup')
    }
  },
  methods: {
    isActive(path) {
      return this.$route.path.startsWith(path);
    },
    getRouteIcon(routeName) {
      switch (routeName.toLowerCase()) {
        case 'home': return 'HomeIcon';
        case 'products': return 'ProductsIcon';
        case 'orders': return 'OrdersIcon';
        default: return 'HomeIcon';
      }
    }
  }
}
</script>

<style scoped>
header {
  background-color: white;
  box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08);
  z-index: 1030;
}

.sameer {
  justify-content: end;
}

.navbar-toggler:focus {
  box-shadow: none;
}

.nav-link {
  font-weight: 500;
  color: var(--bs-gray-700);
  border-radius: 8px;
  transition: all 0.3s ease;
  position: relative;
  display: flex;
  align-items: center;
}

.nav-link:hover {
  color: var(--bs-primary);
  background-color: rgba(108, 117, 125, 0.1);
}

.nav-link:hover .nav-icon {
  transform: translateY(-2px);
}

.nav-link.active {
  color: var(--bs-primary);
  font-weight: 600;
}

.nav-link.active::after {
  content: '';
  position: absolute;
  bottom: -12px;
  left: 0;
  width: 100%;
  height: 3px;
  background-color: var(--bs-primary);
  border-radius: 3px;
}

.nav-icon {
  transition: all 0.3s ease;
  display: inline-flex;
}

.nav-text {
  transition: all 0.3s ease;
}

.profile-pic-container {
  display: flex;
  align-items: center;
}

.profile-pic {
  width: 32px;
  height: 32px;
  object-fit: cover;
}

.profile-placeholder {
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.profile-name {
  font-weight: 500;
  color: var(--bs-gray-800);
}

.dropdown-menu {
  min-width: 200px;
}

.dropdown-item {
  padding: 0.5rem 1rem;
}

/* Mobile styles */
@media (max-width: 991.98px) {
  .navbar-collapse {
    position: fixed;
    top: 0;
    right: 0;
    bottom: 0;
    left: auto;
    width: 280px;
    padding: 1rem;
    background-color: white;
    box-shadow: -5px 0 15px rgba(0, 0, 0, 0.1);
    z-index: 1050;
    overflow-y: auto;
    transform: translateX(100%);
    transition: transform 0.3s ease;
  }

  .navbar-collapse.show {
    transform: translateX(0);
  }

  .navbar-nav {
    padding-top: 3rem;
  }

  .nav-item {
    margin-bottom: 0.5rem;
  }

  .nav-link {
    padding: 0.75rem 1rem;
    justify-content: flex-start;
    border-radius: 6px;
  }

  .nav-link.active::after {
    bottom: auto;
    left: 0;
    top: 50%;
    transform: translateY(-50%);
    width: 3px;
    height: 60%;
    border-radius: 0 3px 3px 0;
  }

  .dropdown-menu {
    position: static !important;
    transform: none !important;
    border: none;
    box-shadow: none;
    margin-left: 1.5rem;
    background-color: rgba(0, 0, 0, 0.02);
  }

  .profile-name {
    display: inline !important;
  }

  .close-menu-btn {
    position: absolute;
    top: 1rem;
    right: 1rem;
    background: none;
    border: none;
    padding: 0.5rem;
    cursor: pointer;
    z-index: 1051;
  }
}

/* Desktop styles */
@media (min-width: 992px) {
  .nav-link {
    padding: 0.5rem 1rem;
  }

  .nav-text {
    display: inline !important;
  }

  .close-menu-btn {
    display: none;
  }
}

/* Overlay for mobile menu */
.navbar-collapse::before {
  content: '';
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.3);
  z-index: -1;
  opacity: 0;
  pointer-events: none;
  transition: opacity 0.3s ease;
}

.navbar-collapse.show::before {
  opacity: 1;
  pointer-events: auto;
}

/* Extra small devices (phones, 480px and down) */
@media (max-width: 480px) {
  .main-logo h1 {
    font-size: 1.5rem;
  }

  .navbar-toggler svg {
    width: 22px;
    height: 22px;
  }

  .navbar-collapse {
    width: 240px;
  }

  .nav-link {
    padding: 0.5rem 0.75rem;
    font-size: 0.95rem;
  }

  .nav-icon svg {
    width: 18px;
    height: 18px;
  }

  .profile-pic,
  .profile-placeholder {
    width: 28px;
    height: 28px;
  }
}

/* Floating Action Buttons */
.fab-container {
  position: fixed;
  bottom: 20px;
  right: 20px;
  display: flex;
  flex-direction: column-reverse;
  gap: 15px;
  z-index: 100;
}

.fab {
  width: 56px;
  height: 56px;
  border-radius: 50%;
  border: none;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
  transition: all 0.2s ease;
}

.fab:hover {
  transform: scale(1.1);
  box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
}

.fab svg {
  width: 24px;
  height: 24px;
}

.fab-support {
  background-color: #2196F3;
  color: white;
}

.fab-cart {
  background-color: #4CAF50;
  color: white;
}

/* Dark mode support */
.dark-theme .fab-support {
  background-color: #1976D2;
}

.dark-theme .fab-cart {
  background-color: #388E3C;
}
</style>