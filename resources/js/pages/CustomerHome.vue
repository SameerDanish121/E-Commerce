<!-- 
<template>
  <div class="product-page">
    <section class="hero-banner">
      <div class="container">
        <div class="hero-content">
          <h1>Welcome to Our Premium Store</h1>
          <p class="lead">Discover amazing products at unbeatable prices</p>
          <p class="sub-lead">Exclusive deals for our valued customers</p>
          <p class="discount-text">Get 15% off on your first order!</p>
        </div>
      </div>
    </section>

    <section class="filters-section py-4 bg-light">
      <div class="container-fluid">
        <div class="row g-3 align-items-center">
          <div class="col-md-4">
            <label class="form-label">Filter by Category</label>
            <select 
              v-model="selectedCategory" 
              class="form-select"
              @change="handleCategoryChange"
            >
              <option value="all">All Categories</option>
              <option 
                v-for="category in uniqueCategories" 
                :key="category" 
                :value="category"
              >
                {{ category }}
              </option>
            </select>
          </div>
          <div class="col-md-8">
            <label class="form-label">Search Products</label>
            <input 
              v-model="searchQuery" 
              type="text" 
              class="form-control" 
              placeholder="Search by name or description..."
              @input="handleSearch"
            >
          </div>
        </div>
      </div>
    </section>

     
    <template v-if="selectedCategory === 'all'">
  <section 
    v-for="category in uniqueCategories" 
    :key="category" 
    class="category-section py-4"
  >
    <div class="container-fluid position-relative">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="section-title">{{ category }}</h2>
      </div>
      
      <div class="category-products-wrapper position-relative">
        <div 
          class="category-products" 
          :ref="el => setScrollContainerRef(el, category)"
          tabindex="0"
          @keydown.left="scrollCategory(category, 'left')"
          @keydown.right="scrollCategory(category, 'right')"
        >
          <ProductCard 
            v-for="product in getProductsByCategory(category)" 
            :key="product.id" 
            :product="product"
            :initial-quantity="getCartQuantity(product.id)"
            @add-to-cart="handleAddToCart"
          />
        </div>

        <button 
          v-if="shouldShowScrollButtons(category)"
          class="scroll-button scroll-left btn p-2 rounded-circle"
          @click="scrollCategory(category, 'left')"
          @mouseenter="setScrollHover(category, true)"
          @mouseleave="setScrollHover(category, false)"
        >
         <i class="bi bi-arrow-left"></i>
        </button>
      
        <button 
          v-if="shouldShowScrollButtons(category)"
          class="scroll-button scroll-right btn p-2 rounded-circle"
          @click="scrollCategory(category, 'right')"
          @mouseenter="setScrollHover(category, true)"
          @mouseleave="setScrollHover(category, false)"
        >
         <i class="bi bi-arrow-right"></i>
        </button>
      </div>
    </div>
  </section>
</template>

<section class="all-products py-4">
  <div class="container-fluid position-relative">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2 class="section-title">
        {{ selectedCategory === 'all' ? 'All Products' : selectedCategory }}
      </h2>
    </div>
    
    <div v-if="filteredProducts.length > 0" class="category-products-wrapper position-relative">
      <div 
        class="category-products" 
        ref="allProductsContainer"
        tabindex="0"
        @keydown.left="scrollCategory('all-products', 'left')"
        @keydown.right="scrollCategory('all-products', 'right')"
      >
        <ProductCard 
          v-for="product in filteredProducts" 
          :key="product.id" 
          :product="product"
          :initial-quantity="getCartQuantity(product.id)"
          @add-to-cart="handleAddToCart"
        />
      </div>   
       <button 
        v-if="shouldShowScrollButtons('all-products')"
        class="scroll-button scroll-left btn p-2 rounded-circle"
        @click="scrollCategory('all-products', 'left')"
        @mouseenter="setScrollHover('all-products', true)"
        @mouseleave="setScrollHover('all-products', false)"
      >
        <i class="bi bi-chevron-left"></i>
      </button>
      <button 
        v-if="shouldShowScrollButtons('all-products')"
        class="scroll-button scroll-right btn p-2 rounded-circle"
        @click="scrollCategory('all-products', 'right')"
        @mouseenter="setScrollHover('all-products', true)"
        @mouseleave="setScrollHover('all-products', false)"
      >
        <i class="bi bi-chevron-right"></i>
      </button>
    </div>
    <div v-else class="text-center py-5">
      <h4>No products found</h4>
      <p>Try adjusting your search or filter criteria</p>
      <button class="btn btn-primary" @click="resetFilters">Reset Filters</button>
    </div>
  </div>
</section>
    <div ref="cartAnim" class="cart-anim-element"></div>
  </div>

    
</template>


<script setup>
import { ref, computed, onMounted } from 'vue';
import { useProductStore } from '../stores/productStore';
import ProductCard from '../components/ProductCard.vue';
import { useCartStore } from '../stores/cartStore';
import { animate } from '@motionone/dom';

const productStore = useProductStore();
const cartStore = useCartStore();
const searchQuery = ref('');
const selectedCategory = ref('all');
const cartAnim = ref(null);
const scrollContainers = ref({});
const scrollHoverStates = ref({});

const uniqueCategories = computed(() => {
  const categories = new Set();
  productStore.products.forEach(product => {
    categories.add(product.category);
  });
  return Array.from(categories);
});

const filteredProducts = computed(() => {
  if (selectedCategory.value === 'all') {
    return productStore.filteredProducts;
  }
  return productStore.filteredProducts.filter(
    product => product.category === selectedCategory.value
  );
});

const getCartQuantity = (productId) => {
  const cartItem = cartStore.items.find(item => item.id === productId);
  return cartItem ? cartItem.quantity : 1;
};

onMounted(async () => {
  await productStore.fetchProducts();
  syncProductStockWithCart();
 
  scrollContainers.value['all-products'] = ref(null);
});

const syncProductStockWithCart = () => {
  cartStore.items.forEach(cartItem => {
    const product = productStore.products.find(p => p.id === cartItem.id);
    if (product) {
      product.stock -= cartItem.quantity;
    }
  });
};

const setScrollContainerRef = (el, category) => {
  if (el) {
    scrollContainers.value[category] = el;
    el.setAttribute('tabindex', '0');
  }
};
const shouldShowScrollButtons = (category) => {
  const container = scrollContainers.value[category];
  if (!container) return false;
  return container.scrollWidth > container.clientWidth;
};

const setScrollHover = (category, isHovering) => {
  scrollHoverStates.value[category] = isHovering;
  const wrapper = getScrollWrapper(category);
  if (wrapper) {
    wrapper.classList.toggle('scroll-hover', isHovering);
  }
};

const getScrollWrapper = (category) => {
  if (category === 'all-products') {
    return document.querySelector('.all-products .category-products-wrapper');
  }
  return document.querySelector(`.category-section[data-category="${category}"] .category-products-wrapper`);
};

const handleSearch = () => {
  productStore.searchQuery = searchQuery.value;
};

const handleCategoryChange = () => {
  productStore.selectedCategory = selectedCategory.value;
};

const resetFilters = () => {
  searchQuery.value = '';
  selectedCategory.value = 'all';
  productStore.searchQuery = '';
  productStore.selectedCategory = 'all';
};

const getProductsByCategory = (category) => {
  return productStore.products.filter(product => product.category === category);
};

const scrollCategory = (category, direction) => {
  const container = scrollContainers.value[category];
  if (container) {
    const scrollAmount = direction === 'left' ? -300 : 300;
    container.scrollBy({ 
      left: scrollAmount, 
      behavior: 'smooth' 
    });
  }
};

const handleAddToCart = (product) => {
  const storeProduct = productStore.products.find(p => p.id === product.id);
  if (storeProduct) {
    const cartItem = cartStore.items.find(item => item.id === product.id);
    const currentCartQuantity = cartItem ? cartItem.quantity : 0;
    const newTotalQuantity = currentCartQuantity + product.quantity;
    
    if (newTotalQuantity > storeProduct.stock + currentCartQuantity) {
      alert(`Cannot add more than available stock (${storeProduct.stock} available)`);
      return;
    }
    
    storeProduct.stock = storeProduct.stock + currentCartQuantity - newTotalQuantity;
  }
  
  cartStore.addToCart(product);
  
  const animElement = cartAnim.value;
  if (animElement) {
    const productCard = document.querySelector(`[data-product-id="${product.id}"]`);
    if (productCard) {
      const productRect = productCard.getBoundingClientRect();
      const cartRect = document.querySelector('.cart-icon')?.getBoundingClientRect();
      
      if (cartRect) {
        animElement.style.position = 'fixed';
        animElement.style.width = '40px';
        animElement.style.height = '40px';
        animElement.style.background = '#FFC43F';
        animElement.style.borderRadius = '50%';
        animElement.style.zIndex = '1000';
        animElement.style.left = `${productRect.left + productRect.width/2 - 20}px`;
        animElement.style.top = `${productRect.top}px`;
        animElement.style.opacity = '1';
        
        animate(
          animElement,
          {
            left: `${cartRect.left + cartRect.width/2 - 20}px`,
            top: `${cartRect.top}px`,
            scale: [1, 0.5],
            opacity: [1, 0]
          },
          { duration: 0.8, easing: 'ease-out' }
        ).finished.then(() => {
          animElement.style.opacity = '0';
        });
      }
    }
  }
};
</script>
<style scoped>
.product-page {
  padding-top: 0;
  width: 100%;
  overflow-x: hidden;
}

.hero-banner {
  background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), 
              url('/images/back_1.jpg') no-repeat center center;
  background-size: cover;
  color: white;
  padding: 8rem 0;
  text-align: center;
  position: relative;
  margin-bottom: 2rem;
}

.hero-content {
  max-width: 800px;
  margin: 0 auto;
  position: relative;
  z-index: 2;
}

.hero-banner h1 {
  font-size: clamp(2rem, 5vw, 3.5rem);
  font-weight: 700;
  margin-bottom: 1rem;
  text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
  font-family: 'Montserrat', sans-serif;
}

.hero-banner .lead {
  font-size: clamp(1.1rem, 2vw, 1.5rem);
  margin-bottom: 0.5rem;
  text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
}

.hero-banner .sub-lead {
  font-size: clamp(1rem, 1.5vw, 1.2rem);
  margin-bottom: 1rem;
  font-style: italic;
}

.hero-banner .discount-text {
  font-size: clamp(1.1rem, 1.8vw, 1.3rem);
  font-weight: bold;
  color: #FFC43F;
  text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.8);
}

.section-title {
  font-size: clamp(1.3rem, 2vw, 1.75rem);
  font-weight: 600;
  color: #333;
  position: relative;
  padding-bottom: 0.5rem;
}

.section-title::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 0;
  width: 50px;
  height: 3px;
  background: var(--bs-primary);
}

.category-products-wrapper {
  position: relative;
  overflow: hidden;
  width: 100%;
}

.category-products {
  display: flex;
  overflow-x: auto;
  scroll-behavior: smooth;
  scrollbar-width: none;
  -ms-overflow-style: none;
  padding-bottom: 1rem;
  gap: 1.5rem;
  min-height: 380px;
}

.category-products:focus {
  outline: 2px solid var(--bs-primary);
  outline-offset: 2px;
}

.category-products::-webkit-scrollbar {
  display: none;
}

.category-section {
  background: white;
  border-radius: 10px;
  margin-bottom: 2rem;
  box-shadow: 0 5px 15px rgba(0,0,0,0.05);
}

.cart-anim-element {
  position: fixed;
  pointer-events: none;
  opacity: 0;
}

@media (max-width: 768px) {
  .filters-section .row {
    flex-direction: column;
    gap: 1rem;
  }
  
  .filters-section .col-md-4,
  .filters-section .col-md-8 {
    width: 100%;
  }
  
  .category-products {
    gap: 1rem;
    min-height: 360px;
  }
}

@media (max-width: 576px) {
  .hero-banner {
    padding: 4rem 1rem;
  }
  
  .all-products .row {
    row-cols: 1 !important;
  }
  
  .category-products {
    min-height: 340px;
  }
}


.category-products-wrapper {
  position: relative;
}

.category-products {
  display: flex;
  overflow-x: auto;
  scroll-behavior: smooth;
  gap: 1rem;
  padding: 0.5rem 0;
  scrollbar-width: none; 
  -ms-overflow-style: none;
}

.category-products::-webkit-scrollbar {
  display: none; 
}


.scroll-button {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  z-index: 10;
  background-color: rgba(255, 255, 255, 0.7);
  color: #333;
  border: none;
  width: 36px;
  height: 36px;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s ease;
  opacity: 0.8;
  box-shadow: 0 2px 6px rgba(0,0,0,0.15);
}

.scroll-left {
  left: 12px;
}

.scroll-right {
  right: 12px;
}

.scroll-button:hover, .scroll-button:focus {
  background-color: rgba(13, 110, 253, 0.9);
  color: white;
  opacity: 1;
  box-shadow: 0 0 8px rgba(0, 0, 0, 0.2);
}

.scroll-button.scroll-left {
  left: 10px;
}

.scroll-button.scroll-right {
  right: 10px;
}

.category-products-wrapper:hover .category-products {
  transition: filter 0.3s ease;
}

.category-products-wrapper.scroll-hover .category-products {
  filter: blur(1px);
}
.scroll-button {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  z-index: 20;
  background-color: white;
  color: #333;
  border: 2px solid #ddd;
  width: 48px;
  height: 48px;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s ease;
  opacity: 1;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.scroll-button:hover, .scroll-button:focus {
  background-color: var(--bs-primary);
  color: white;
  border-color: var(--bs-primary);
  transform: translateY(-50%) scale(1.1);
  box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
}

.scroll-button i {
  font-weight: 900; 
  text-shadow: 0 1px 2px rgba(0,0,0,0.1);
}

.scroll-button.scroll-left {
  left: 0;
  border-top-right-radius: 24px;
  border-bottom-right-radius: 24px;
  border-left: none;
}

.scroll-button.scroll-right {
  right: 0;
  border-top-left-radius: 24px;
  border-bottom-left-radius: 24px;
  border-right: none;
}

.category-products-wrapper:hover .scroll-button {
  opacity: 1;
}
.category-products {
  scroll-padding: 0 24px;
}
</style> -->


<template>
  <div class="product-page">
    <section class="hero-banner">
      <div class="container">
        <div class="hero-content">
          <h1>Welcome to Our Premium Store</h1>
          <p class="lead">Discover amazing products at unbeatable prices</p>
          <p class="sub-lead">Exclusive deals for our valued customers</p>
          <p class="discount-text">Get 15% off on your first order!</p>
        </div>
      </div>
    </section>
    <section class="filters-section py-4 bg-light">
      <div class="container-fluid">
        <div class="row g-3 align-items-center">
          <div class="col-md-4">
            <label class="form-label">Filter by Category</label>
            <select v-model="selectedCategory" class="form-select" @change="handleCategoryChange">
              <option value="all">All Categories</option>
              <option v-for="category in uniqueCategories" :key="category" :value="category">
                {{ category }}
              </option>
            </select>
          </div>
          <div class="col-md-8">
            <label class="form-label">Search Products</label>
            <input v-model="searchQuery" type="text" class="form-control" placeholder="Search by name or description..."
              @input="handleSearch">
          </div>
        </div>
      </div>
    </section>
    <template v-if="selectedCategory === 'all'">
      <section v-for="category in categoriesWithProducts" :key="category" class="category-section py-4">
        <div class="container-fluid position-relative">
          <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="section-title">{{ category }}</h2>
          </div>

          <div class="category-products-wrapper position-relative">
            <div class="category-products" :ref="el => setScrollContainerRef(el, category)" tabindex="0"
              @keydown.left="scrollCategory(category, 'left')" @keydown.right="scrollCategory(category, 'right')">
              <ProductCard v-for="product in filteredProducts.filter(p => p.category === category)" :key="product.id"
                :product="product" :initial-quantity="getCartQuantity(product.id)" @add-to-cart="handleAddToCart" />
            </div>
            <button v-if="shouldShowScrollButtons(category)" class="scroll-button scroll-left btn p-0 rounded-circle"
              @click="scrollCategory(category, 'left')" @mouseenter="setScrollHover(category, true)"
              @mouseleave="setScrollHover(category, false)">
              <i class="bi bi-arrow-left fs-3 fw-bold"></i>
            </button>

            <button v-if="shouldShowScrollButtons(category)" class="scroll-button scroll-right btn p-0 rounded-circle"
              @click="scrollCategory(category, 'right')" @mouseenter="setScrollHover(category, true)"
              @mouseleave="setScrollHover(category, false)">
              <i class="bi bi-arrow-right fs-3 fw-bold"></i>
            </button>

          </div>
        </div>
      </section>
    </template>
 
    <section class="all-products py-4">
      <div class="container-fluid position-relative">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h2 class="section-title">
            {{ selectedCategory === 'all' ? 'All Products' : selectedCategory }}
          </h2>
        </div>

        <div v-if="filteredProducts.length > 0" class="category-products-wrapper position-relative">
          <div class="category-products" ref="allProductsContainer" tabindex="0"
            @keydown.left="scrollCategory('all-products', 'left')"
            @keydown.right="scrollCategory('all-products', 'right')">
            <ProductCard v-for="product in filteredProducts" :key="product.id" :product="product"
              :initial-quantity="getCartQuantity(product.id)" @add-to-cart="handleAddToCart" />
          </div>

          <button v-if="shouldShowScrollButtons('all-products')"
            class="scroll-button scroll-left btn p-0 rounded-circle" @click="scrollCategory('all-products', 'left')"
            @mouseenter="setScrollHover('all-products', true)" @mouseleave="setScrollHover('all-products', false)">
            <i class="bi bi-arrow-left fs-3 fw-bold"></i>
          </button>

          <button v-if="shouldShowScrollButtons('all-products')"
            class="scroll-button scroll-right btn p-0 rounded-circle" @click="scrollCategory('all-products', 'right')"
            @mouseenter="setScrollHover('all-products', true)" @mouseleave="setScrollHover('all-products', false)">
            <i class="bi bi-arrow-right fs-3 fw-bold"></i>
          </button>
        </div>
        <div v-else class="text-center py-5">
          <h4>No products found</h4>
          <p>Try adjusting your search or filter criteria</p>
          <button class="btn btn-primary" @click="resetFilters">Reset Filters</button>
        </div>
      </div>
    </section>

    <div ref="cartAnim" class="cart-anim-element"></div>

  </div>

</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useProductStore } from '../stores/productStore';
import ProductCard from '../components/ProductCard.vue';
import { useCartStore } from '../stores/cartStore';
import { animate } from '@motionone/dom';

const productStore = useProductStore();
const cartStore = useCartStore();
const searchQuery = ref('');
const selectedCategory = ref('all');
const cartAnim = ref(null);
const scrollContainers = ref({});
const scrollHoverStates = ref({});

const uniqueCategories = computed(() => {
  const categories = new Set();
  productStore.products.forEach(product => {
    categories.add(product.category);
  });
  return Array.from(categories);
});

const filteredProducts = computed(() => {
  if (selectedCategory.value === 'all') {
    return productStore.filteredProducts;
  }
  return productStore.filteredProducts.filter(
    product => product.category === selectedCategory.value
  );
});

const getCartQuantity = (productId) => {
  const cartItem = cartStore.items.find(item => item.id === productId);
  return cartItem ? cartItem.quantity : 1;
};

onMounted(async () => {
  await productStore.fetchProducts();
  syncProductStockWithCart();
});

const syncProductStockWithCart = () => {
  cartStore.items.forEach(cartItem => {
    const product = productStore.products.find(p => p.id === cartItem.id);
    if (product) {
      product.stock -= cartItem.quantity;
    }
  });
};

const setScrollContainerRef = (el, category) => {
  if (el) {
    scrollContainers.value[category] = el;
    el.setAttribute('tabindex', '0');
  }
};

const shouldShowScrollButtons = (category) => {
  const container = scrollContainers.value[category] ||
    (category === 'all-products' ? document.querySelector('.all-products .category-products') : null);
  if (!container) return false;
  return container.scrollWidth > container.clientWidth;
};

const setScrollHover = (category, isHovering) => {
  scrollHoverStates.value[category] = isHovering;
  const wrapper = category === 'all-products'
    ? document.querySelector('.all-products .category-products-wrapper')
    : document.querySelector(`.category-section[data-category="${category}"] .category-products-wrapper`);
  if (wrapper) {
    wrapper.classList.toggle('scroll-hover', isHovering);
  }
};

const handleSearch = () => {
  productStore.searchQuery = searchQuery.value;
};

const handleCategoryChange = () => {
  productStore.selectedCategory = selectedCategory.value;
};

const resetFilters = () => {
  searchQuery.value = '';
  selectedCategory.value = 'all';
  productStore.searchQuery = '';
  productStore.selectedCategory = 'all';
};

const getProductsByCategory = (category) => {
  return productStore.products.filter(product => product.category === category);
};
// const getProductsByCategory = (category) => {
//   let products = productStore.products.filter(product => product.category === category);
//   if (searchQuery.value) {
//     const query = searchQuery.value.toLowerCase();
//     products = products.filter(product =>
//       product.name.toLowerCase().includes(query) ||
//       product.description.toLowerCase().includes(query)
//     );
//   }
//   return products;
// };
const categoriesWithProducts = computed(() => {
  if (!searchQuery.value) {
    return uniqueCategories.value;
  }
  const matchingCategories = new Set();
  filteredProducts.value.forEach(product => {
    matchingCategories.add(product.category);
  });
  return uniqueCategories.value.filter(cat => matchingCategories.has(cat));
});
const scrollCategory = (category, direction) => {
  const container = scrollContainers.value[category] ||
    (category === 'all-products' ? document.querySelector('.all-products .category-products') : null);
  if (container) {
    const scrollAmount = direction === 'left' ? -300 : 300;
    container.scrollBy({
      left: scrollAmount,
      behavior: 'smooth'
    });
  }
};

const handleAddToCart = (product) => {
  const storeProduct = productStore.products.find(p => p.id === product.id);
  if (storeProduct) {
    const cartItem = cartStore.items.find(item => item.id === product.id);
    const currentCartQuantity = cartItem ? cartItem.quantity : 0;
    const newTotalQuantity = currentCartQuantity + product.quantity;

    if (newTotalQuantity > storeProduct.stock + currentCartQuantity) {
      alert(`Cannot add more than available stock (${storeProduct.stock} available)`);
      return;
    }

    storeProduct.stock = storeProduct.stock + currentCartQuantity - newTotalQuantity;
  }

  cartStore.addToCart(product);

  const animElement = cartAnim.value;
  if (animElement) {
    const productCard = document.querySelector(`[data-product-id="${product.id}"]`);
    if (productCard) {
      const productRect = productCard.getBoundingClientRect();
      const cartRect = document.querySelector('.cart-icon')?.getBoundingClientRect();

      if (cartRect) {
        animElement.style.position = 'fixed';
        animElement.style.width = '40px';
        animElement.style.height = '40px';
        animElement.style.background = '#FFC43F';
        animElement.style.borderRadius = '50%';
        animElement.style.zIndex = '1000';
        animElement.style.left = `${productRect.left + productRect.width / 2 - 20}px`;
        animElement.style.top = `${productRect.top}px`;
        animElement.style.opacity = '1';

        animate(
          animElement,
          {
            left: `${cartRect.left + cartRect.width / 2 - 20}px`,
            top: `${cartRect.top}px`,
            scale: [1, 0.5],
            opacity: [1, 0]
          },
          { duration: 0.8, easing: 'ease-out' }
        ).finished.then(() => {
          animElement.style.opacity = '0';
        });
      }
    }
  }
};

</script>

<style scoped>

.product-page {
  padding-top: 0;
  width: 100%;
  overflow-x: hidden;
}

.hero-banner {
  background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)),
    url('/images/back_1.jpg') no-repeat center center;
  background-size: cover;
  color: white;
  padding: 8rem 0;
  text-align: center;
  position: relative;
  margin-bottom: 2rem;
}

.hero-content {
  max-width: 800px;
  margin: 0 auto;
  position: relative;
  z-index: 2;
}

.hero-banner h1 {
  font-size: clamp(2rem, 5vw, 3.5rem);
  font-weight: 700;
  margin-bottom: 1rem;
  text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
  font-family: 'Montserrat', sans-serif;
}

.hero-banner .lead {
  font-size: clamp(1.1rem, 2vw, 1.5rem);
  margin-bottom: 0.5rem;
  text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
}

.hero-banner .sub-lead {
  font-size: clamp(1rem, 1.5vw, 1.2rem);
  margin-bottom: 1rem;
  font-style: italic;
}

.hero-banner .discount-text {
  font-size: clamp(1.1rem, 1.8vw, 1.3rem);
  font-weight: bold;
  color: #FFC43F;
  text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.8);
}

/* .section-title {
  font-size: clamp(1.3rem, 2vw, 1.75rem);
  font-weight: 600;
  color: #333;
  position: relative;
  padding-bottom: 0.5rem;
}

.section-title::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 0;
  width: 50px;
  height: 3px;
  background: var(--bs-primary);
} */
.section-title {
  font-size: clamp(1.3rem, 2vw, 1.75rem);
  font-weight: 600;
  color: #333;
  position: relative;
  display: inline-block;
  /* Important */
  padding-bottom: 0.5rem;
}

.section-title::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 0;
  height: 3px;
  width: 100%;
  /* Match width of the text */
  background: var(--bs-primary);
}

.category-products-wrapper {
  position: relative;
  overflow: hidden;
  width: 100%;
}

.category-products {
  display: flex;
  overflow-x: auto;
  scroll-behavior: smooth;
  scrollbar-width: none;
  -ms-overflow-style: none;
  padding-bottom: 1rem;
  gap: 1.5rem;
  min-height: 380px;
  scroll-padding: 0 24px;
}

.category-products:focus {
  outline: 2px solid var(--bs-primary);
  outline-offset: 2px;
}

.category-products::-webkit-scrollbar {
  display: none;
}

.category-section,
.all-products {
  background: white;
  border-radius: 10px;
  margin-bottom: 2rem;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
}

.cart-anim-element {
  position: fixed;
  pointer-events: none;
  opacity: 0;
}

.scroll-button {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  z-index: 20;
  background-color: white;
  color: #333;
  border: 2px solid #ddd;
  width: 48px;
  height: 48px;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s ease;
  opacity: 1;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.scroll-button:hover,
.scroll-button:focus {
  background-color: var(--bs-primary);
  color: white;
  border-color: var(--bs-primary);
  transform: translateY(-50%) scale(1.1);
  box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
}

.scroll-button i {
  font-weight: 900;
  text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
}

.scroll-button.scroll-left {
  left: 0;
  border-top-right-radius: 24px;
  border-bottom-right-radius: 24px;
  border-left: none;
}

.scroll-button.scroll-right {
  right: 0;
  border-top-left-radius: 24px;
  border-bottom-left-radius: 24px;
  border-right: none;
}

.category-products-wrapper:hover .scroll-button {
  opacity: 1;
}


@media (max-width: 768px) {
  .filters-section .row {
    flex-direction: column;
    gap: 1rem;
  }

  .filters-section .col-md-4,
  .filters-section .col-md-8 {
    width: 100%;
  }

  .category-products {
    gap: 1rem;
    min-height: 340px;
  }

  .scroll-button {
    width: 40px;
    height: 40px;
  }

  .scroll-button i {
    font-size: 1.25rem;
  }
}

@media (max-width: 576px) {
  .hero-banner {
    padding: 4rem 1rem;
  }

  .category-products {
    min-height: 320px;
    gap: 0.75rem;
  }

  .scroll-button {
    width: 36px;
    height: 36px;
  }

  .scroll-button i {
    font-size: 1.1rem;
  }
}
</style>