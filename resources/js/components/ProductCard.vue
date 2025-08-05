<template>
  <div class="product-card" :data-product-id="product.id">
    <div class="card h-100 border-0 shadow-sm">
      <div class="badge-container">
        <span v-if="product.stock === 0" class="badge bg-danger">Out of Stock</span>
        <span v-else-if="product.price < 1000" class="badge bg-success">Great Deal</span>
      </div>

      <!-- <button class="btn-wishlist" @click="toggleWishlist">
        <i class="bi" :class="isInWishlist ? 'bi-heart-fill text-danger' : 'bi-heart'"></i>
      </button> -->

      <div class="product-image-container">
        <img :src="product.image_url" :alt="product.name" class="card-img-top" @error="this.src = '/images/temp.jpg'">
      </div>

      <div class="card-body">
        <h5 class="card-title">{{ product.name }}</h5>
        <p class="card-text text-muted small">{{ product.description.substring(0, 50) }}...</p>

        <!-- <div class="d-flex justify-content-between align-items-center mb-3">
          <div>
            <span class="text-primary fw-bold">Rs {{ product.price.toLocaleString('en-IN') }}</span>
            <span v-if="product.original_price" class="text-decoration-line-through text-muted small ms-2">
              ₹{{ product.original_price.toLocaleString('en-IN') }}
            </span>
          </div>
          <span class="stock-badge" :class="{ 'text-danger': product.stock < 5, 'text-success': product.stock >= 5 }">
            {{ product.stock }} left
          </span>
        </div> -->
        <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
          <div class="text-nowrap">
            <span class="text-primary fw-bold">
              Rs {{ formatNumber(product.price) }}
            </span>
            <span v-if="product.original_price" class="text-decoration-line-through text-muted small ms-2 text-nowrap">
              ₹{{ formatNumber(product.original_price) }}
            </span>
          </div>
        
          <span class="stock-badge text-nowrap ms-2 mt-2 mt-sm-0"
            :class="{ 'text-danger': product.stock < 5, 'text-success': product.stock >= 5 }">
            {{ formatNumber(product.stock) }} left
          </span>
        </div>

        <div class="quantity-controls" v-if="product.stock > 0">
          <div class="input-group">
            <button class="btn btn-outline-secondary" type="button" @click="decreaseQuantity" :disabled="quantity <= 1">
              <i class="bi bi-dash-lg"></i>
            </button>
            <input type="text" class="form-control text-center" v-model="quantity" @change="validateQuantity" readonly>
            <button class="btn btn-outline-secondary" type="button" @click="increaseQuantity"
              :disabled="quantity >= product.stock">
              <i class="bi bi-plus-lg"></i>
            </button>
          </div>
        </div>

        <button class="btn btn-primary w-100 mt-2 add-to-cart" @click="addToCart" :disabled="product.stock === 0"
          :class="{ 'btn-disabled': product.stock === 0 }">
          <span v-if="product.stock === 0">Out of Stock</span>
          <span v-else-if="cartQuantity > 0">Add {{ quantity }} ({{ cartQuantity }} in cart)</span>
          <span v-else>Add {{ quantity }} to Cart <i class="bi bi-cart-plus ms-1"></i></span>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useCartStore } from '../stores/cartStore';

const props = defineProps({
  product: {
    type: Object,
    required: true
  },
  initialQuantity: {
    type: Number,
    default: 1
  }
});

const emit = defineEmits(['add-to-cart']);

const cartStore = useCartStore();
const quantity = ref(props.initialQuantity);
const isInWishlist = ref(false);

const cartQuantity = computed(() => {
  const cartItem = cartStore.items.find(item => item.id === props.product.id);
  return cartItem ? cartItem.quantity : 0;
});

const increaseQuantity = () => {
  if (quantity.value < props.product.stock) {
    quantity.value++;
  }
};

const decreaseQuantity = () => {
  if (quantity.value > 1) {
    quantity.value--;
  }
};

const validateQuantity = () => {
  if (quantity.value < 1) quantity.value = 1;
  if (quantity.value > props.product.stock) quantity.value = props.product.stock;
};

const toggleWishlist = () => {
  isInWishlist.value = !isInWishlist.value;
};

const addToCart = () => {
  emit('add-to-cart', {
    ...props.product,
    quantity: quantity.value
  });

  const button = document.querySelector(`[data-product-id="${props.product.id}"] .add-to-cart`);
  if (button) {
    button.classList.add('clicked');
    setTimeout(() => {
      button.classList.remove('clicked');
    }, 300);
  }
};
const formatNumber = (value) => {
  if (value >= 1_000_000_000) return (value / 1_000_000_000).toFixed(1) + 'B';
  if (value >= 1_000_000) return (value / 1_000_000).toFixed(1) + 'M';
  if (value >= 1_000) return (value / 1_000).toFixed(1) + 'K';
  return value;
};
</script>

<style scoped>
.product-card {
  position: relative;
  transition: transform 0.3s ease;
  height: 100%;
  min-width: 250px;
  max-width: 250px;
}

.product-card:hover {
  transform: translateY(-5px);
}

.card {
  border-radius: 12px;
  overflow: hidden;
  transition: box-shadow 0.3s ease;
  height: 100%;
  display: flex;
  flex-direction: column;
}

.card:hover {
  box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
}

.badge-container {
  position: absolute;
  top: 10px;
  left: 10px;
  z-index: 2;
}

.badge {
  font-size: 0.7rem;
  padding: 5px 8px;
}

.btn-wishlist {
  position: absolute;
  top: 10px;
  right: 10px;
  width: 36px;
  height: 36px;
  background: white;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  border: none;
  z-index: 2;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
  transition: all 0.3s ease;
}

.btn-wishlist:hover {
  background: #f8f9fa;
  transform: scale(1.1);
}

.product-image-container {
  height: 180px;
  overflow: hidden;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f8f9fa;
  flex-shrink: 0;
}

.product-image-container img {
  max-height: 100%;
  width: auto;
  max-width: 100%;
  object-fit: contain;
  transition: transform 0.5s ease;
}

.product-card:hover .product-image-container img {
  transform: scale(1.05);
}

.card-body {
  padding: 1.25rem;
  display: flex;
  flex-direction: column;
  flex-grow: 1;

}

.card-title {
  font-size: 1rem;
  font-weight: 600;
  margin-bottom: 0.5rem;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.card-text {
  font-size: 0.85rem;
  margin-bottom: 1rem;
  min-height: 40px;
  flex-grow: 1;
}

.stock-badge {
  font-size: 0.75rem;
  font-weight: 500;
}

.quantity-controls .input-group {
  width: 120px;
  margin: 0 auto;
}

.quantity-controls .btn {
  padding: 0.25rem 0.5rem;
}

.quantity-controls .form-control {
  padding: 0.25rem;
  text-align: center;
}

.add-to-cart {
  transition: all 0.3s ease;
  margin-top: auto;
  padding: 0.5rem;
  font-size: 0.9rem;
  white-space: nowrap;
}

.add-to-cart.clicked {
  animation: pulse 0.3s ease;
}

.btn-disabled {
  background-color: #e9ecef !important;
  border-color: #e9ecef !important;
  color: #6c757d !important;
  cursor: not-allowed;
}

@keyframes pulse {
  0% {
    transform: scale(1);
  }

  50% {
    transform: scale(0.95);
  }

  100% {
    transform: scale(1);
  }
}

@media (max-width: 992px) {
  .product-card {
    min-width: 220px;
    max-width: 220px;
  }
}

@media (max-width: 768px) {
  .product-card {
    min-width: 200px;
    max-width: 200px;
  }

  .product-image-container {
    height: 150px;
  }

  .add-to-cart {
    font-size: 0.85rem;
    padding: 0.4rem;
  }

  .quantity-controls .input-group {
    width: 100px;
  }
}

@media (max-width: 576px) {
  .product-card {
    min-width: 160px;
    max-width: 160px;
  }

  .card-body {
    padding: 1rem;
  }

  .card-title {
    font-size: 0.95rem;
  }

  .card-text {
    font-size: 0.8rem;
    min-height: 36px;
  }

  .add-to-cart {
    font-size: 0.8rem;
  }

  .product-image-container {
    height: 130px;
  }
}

.product-card,
.card {
  height: 420px;
  display: flex;
  flex-direction: column;
}
</style>