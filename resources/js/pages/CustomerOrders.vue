<template>
  <div class="orders-page">
    <section class="orders-header py-4 bg-light">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-md-6">
            <h2 class="mb-0">Your Orders</h2>
            <p class="text-muted small mb-0">View your order history and status</p>
          </div>
          <div class="col-md-6 text-md-end">
            <div class="dropdown d-inline-block me-2">
              <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="statusFilter" data-bs-toggle="dropdown" aria-expanded="false">
                {{ selectedStatus === 'all' ? 'All Statuses' : selectedStatus }}
              </button>
              <ul class="dropdown-menu" aria-labelledby="statusFilter">
                <li><button class="dropdown-item" :class="{ 'active': selectedStatus === 'all' }" @click="selectedStatus = 'all'">All Statuses</button></li>
                <li v-for="status in orderStatuses" :key="status">
                  <button class="dropdown-item" :class="{ 'active': selectedStatus === status }" @click="selectedStatus = status">{{ status }}</button>
                </li>
              </ul>
            </div>
            <button class="btn btn-primary" @click="refreshOrders" :disabled="orderStore.isLoading">
              <i class="bi" :class="orderStore.isLoading ? 'bi-arrow-repeat spin' : 'bi-arrow-clockwise'"></i> Refresh
            </button>
          </div>
        </div>
      </div>
    </section>

    <section class="orders-content py-4">
      <div class="container">
        <div v-if="orderStore.isLoading" class="text-center py-5">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
          <p class="mt-3">Loading your orders...</p>
        </div>

        <div v-else-if="orderStore.error" class="alert alert-danger">
          <i class="bi bi-exclamation-triangle-fill me-2"></i>
          {{ orderStore.error }}
          <button class="btn btn-sm btn-outline-danger ms-3" @click="orderStore.fetchOrders">Retry</button>
        </div>

        <div v-else-if="filteredOrders.length === 0" class="empty-orders text-center py-5">
          <div class="empty-icon mb-4">
            <i class="bi bi-box-seam" style="font-size: 3rem; color: #6c757d;"></i>
          </div>
          <h3>No orders found</h3>
          <p class="text-muted mb-4">You haven't placed any orders yet</p>
          <router-link to="/home" class="btn btn-primary px-4">
            <i class="bi bi-bag me-2"></i> Start Shopping
          </router-link>
        </div>

        <div v-else class="orders-list">
          <div v-for="order in filteredOrders" :key="order.id" class="order-card mb-4">
            <div class="card">
              <div class="card-header bg-white">
                <div class="row align-items-center">
                  <div class="col-md-6">
                    <h5 class="mb-0">
                      Order #{{ order.id }}
                      <span class="badge ms-2" :class="statusBadgeClass(order.status)">
                        {{ order.status }}
                      </span>
                    </h5>
                  </div>
                  <div class="col-md-6 text-md-end">
                    <small class="text-muted">
                      Ordered on {{ formatDate(order.order_datetime) }}
                      <span v-if="order.expected_delivery_date">
                        • Expected by {{ formatDate(order.expected_delivery_date) }}
                      </span>
                    </small>
                  </div>
                </div>
              </div>
              <div class="card-body">
                <div class="order-items">
                  <div v-for="item in order.items" :key="item.id" class="order-item row mb-3 pb-3 border-bottom">
                    <div class="col-3 col-md-2">
                      <img :src="item.image_url" :alt="item.name" class="img-fluid rounded border" style="max-height: 80px;">
                    </div>
                    <div class="col-5 col-md-6">
                      <h6 class="mb-1">{{ item.name }}</h6>
                      <p class="small text-muted mb-1">{{ item.category }}</p>
                      <p class="small text-muted mb-1">Quantity: {{ item.quantity }}</p>
                      <p class="small text-muted mb-0">Price: Rs {{ item.price_at_purchase.toLocaleString('en-IN') }}</p>
                    </div>
                    <div class="col-4 col-md-4 text-end">
                      <p class="mb-1 fw-bold">Rs {{ (item.price_at_purchase * item.quantity).toLocaleString('en-IN') }}</p>
                    </div>
                  </div>
                </div>

                <div class="order-summary mt-4 pt-3 border-top">
                  <div class="row">
                    <div class="col-md-6">
                      <h6>Delivery Address</h6>
                      <p class="small">{{ order.delivery_address }}</p>
                    </div>
                    <div class="col-md-6 text-md-end">
                      <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal:</span>
                        <span>Rs {{ order.total_payment.toLocaleString('en-IN') }}</span>
                      </div>
                      <div class="d-flex justify-content-between mb-2">
                        <span>Shipping:</span>
                        <span class="text-success">FREE</span>
                      </div>
                      <div class="d-flex justify-content-between fw-bold fs-5">
                        <span>Total:</span>
                        <span>Rs {{ order.total_payment.toLocaleString('en-IN') }}</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <!-- <div class="card-footer bg-light" v-if="order.status === 'Completed'">
                <div class="form-check">
                  <input 
                    class="form-check-input" 
                    type="checkbox" 
                    :id="`deliveredOnTime-${order.id}`"
                    v-model="order.delivered_on_time"
                    @change="updateDeliveryStatus(order)"
                  >
                  <label class="form-check-label small" :for="`deliveredOnTime-${order.id}`">
                    Delivered on time
                  </label>
                </div>
              </div> -->
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useOrderStore } from '../stores/orderStore';
import axios from 'axios';
import Swal from 'sweetalert2';

const orderStore = useOrderStore();

const orderStatuses = ['Pending', 'Dispatched', 'Completed', 'Canceled'];
const selectedStatus = ref('all');


onMounted(async () => {
  await orderStore.fetchOrders();
});

// Filter orders based on selected status
const filteredOrders = computed(() => {
  if (selectedStatus.value === 'all') {
    return orderStore.orders;
  }
  return orderStore.orders.filter(order => order.status === selectedStatus.value);
});

// Format date for display
const formatDate = (dateString) => {
  if (!dateString || dateString === 'N/A') return 'Not specified';
  
  const options = { year: 'numeric', month: 'short', day: 'numeric' };
  return new Date(dateString).toLocaleDateString(undefined, options);
};

// Determine badge class based on status
const statusBadgeClass = (status) => {
  switch (status) {
    case 'Pending': return 'bg-warning text-dark';
    case 'Dispatched': return 'bg-info text-white';
    case 'Completed': return 'bg-success text-white';
    case 'Canceled': return 'bg-danger text-white';
    default: return 'bg-secondary text-white';
  }
};

// Refresh orders
const refreshOrders = async () => {
  await orderStore.fetchOrders();
};

// Update delivery status
const updateDeliveryStatus = async (order) => {
  try {
    await axios.patch(`/orders/${order.id}`, {
      delivered_on_time: order.delivered_on_time ? 'Yes' : 'No'
    });
    Swal.fire({
      icon: 'success',
      title: 'Updated!',
      text: 'Delivery status updated',
      timer: 1500,
      showConfirmButton: false
    });
  } catch (error) {
    console.error('Error updating delivery status:', error);
    order.delivered_on_time = !order.delivered_on_time; // Revert the change
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: 'Failed to update delivery status',
    });
  }
};
</script>

<style scoped>
.orders-page {
  background-color: #f8f9fa;
  min-height: 100vh;
}

.order-card {
  transition: all 0.3s ease;
}

.order-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.order-item {
  transition: background-color 0.2s ease;
}

.order-item:hover {
  background-color: rgba(0,0,0,0.02);
}

.badge {
  font-size: 0.75rem;
  font-weight: 500;
  padding: 0.35em 0.65em;
}

.empty-orders {
  background: white;
  border-radius: 10px;
  padding: 3rem;
  box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.spin {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

@media (max-width: 768px) {
  .orders-header {
    text-align: center;
  }
  
  .orders-header .text-md-end {
    text-align: center !important;
    margin-top: 1rem;
  }
  
  .order-item {
    padding-bottom: 1rem;
    margin-bottom: 1rem;
  }
  
  .order-summary .text-md-end {
    text-align: left !important;
    margin-top: 1rem;
  }
  
  .card-footer .text-md-end {
    text-align: left !important;
    margin-top: 1rem;
  }
}
</style>