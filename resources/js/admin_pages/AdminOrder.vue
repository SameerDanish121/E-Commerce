<template>
  <div class="admin-orders-page">
    <section class="admin-header">
      <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center py-4">
          <h1 class="page-title">Order Processing</h1>
          <button class="btn btn-primary" @click="refreshOrders" :disabled="orderStore.isLoading">
            <i class="bi" :class="orderStore.isLoading ? 'bi-arrow-repeat spin' : 'bi-arrow-clockwise'"></i>
            Refresh Orders
          </button>
        </div>
      </div>
    </section>

    <section class="filters-section py-3 bg-light">
      <div class="container-fluid">
        <div class="row g-3">
          <div class="col-md-3">
            <label class="form-label">Search Customer</label>
            <input v-model="orderStore.filterCustomerName" type="text" class="form-control"
              placeholder="Search by customer name..."
              @input="orderStore.filterCustomerName = $event.target.value.replace(/[^a-zA-Z\s]/g, '').slice(0, 20)">

          </div>
          <div class="col-md-2">
            <label class="form-label">Status</label>
            <select v-model="orderStore.filterStatus" class="form-select">
              <option value="all">All Statuses</option>
              <option value="Pending">Pending</option>
              <option value="Dispatched">Dispatched</option>
              <option value="Completed">Completed</option>
              <option value="Canceled">Canceled</option>
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label">Order Date</label>
            <input :value="orderStore.filterOrderDate ?? ''" @input="orderStore.filterOrderDate = $event.target.value"
              type="date" class="form-control" :max="new Date().toISOString().split('T')[0]">
          </div>
          <div class="col-md-2">
            <label class="form-label">Min Amount</label>
            <input v-model="orderStore.filterMinPrice" type="number" class="form-control" placeholder="Min" min="0"
              max="1000000" oninput="if(this.value.length > 7) this.value = this.value.slice(0, 7);">
          </div>

          <div class="col-md-2">
            <label class="form-label">Max Amount</label>
            <input v-model="orderStore.filterMaxPrice" type="number" class="form-control" placeholder="Max" min="0"
              max="1000000" oninput="if(this.value.length > 7) this.value = this.value.slice(0, 7);">
          </div>

          <div class="col-md-1 d-flex align-items-end">
            <button class="btn btn-outline-secondary w-100" @click="resetFilters">
              Reset
              <i class="bi bi-arrow-counterclockwise"></i>
            </button>
          </div>
        </div>
      </div>
    </section>

    <section class="orders-table-section py-4">
      <div class="container-fluid">
        <div v-if="orderStore.isLoading" class="text-center py-5">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
        </div>

        <div v-else-if="orderStore.error" class="alert alert-danger">
          <i class="bi bi-exclamation-triangle-fill me-2"></i>
          {{ orderStore.error }}
          <button class="btn btn-sm btn-outline-danger ms-3" @click="orderStore.fetchAllOrders">Retry</button>
        </div>

        <div v-else-if="orderStore.filteredOrders.length === 0" class="text-center py-5">
          <h5>No orders found</h5>
          <p class="text-muted">Try adjusting your search or filter criteria</p>
        </div>

        <div v-else class="orders-list">
          <div v-for="order in orderStore.filteredOrders" :key="order.order_id" class="order-card mb-4">
            <div class="card">
              <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                  <div>
                    <h5 class="mb-0">Order #{{ order.order_id }}</h5>
                    <small class="text-muted">Placed on {{ formatDateTime(order.order_datetime) }}</small>
                  </div>
                  <div>
                    <span class="badge" :class="statusBadgeClass(order.status)">
                      {{ order.status }}
                    </span>
                  </div>
                </div>
              </div>

              <div class="card-body">
                <div class="row">

                  <div class="col-md-4 mb-4 mb-md-0">
                    <!-- <div class="customer-details">
                      <h6 class="section-title">Customer Details</h6>
                      <div class="d-flex align-items-center mb-3">
                        <div class="customer-avatar me-3">
                          <img :src="order.customer.profile_pic || '/images/default-profile.png'" class="rounded-circle"
                            width="50" height="50" alt="Customer">
                        </div>
                        <div>
                          <h6 class="mb-0">{{ order.customer.name }}</h6>
                          <small class="text-muted">{{ order.customer.phone_no }}</small>
                        </div>
                      </div>
                      <div class="customer-meta">
                        <p class="small mb-1">
                          <i class="bi bi-gender-ambiguous me-2"></i>
                          {{ order.customer.gender || 'Not specified' }}
                        </p>
                        <p class="small mb-1">
                          <i class="bi bi-calendar me-2"></i>
                          {{ order.customer.dob ? formatDate(order.customer.dob) : 'Not specified' }}
                        </p>
                        <p class="small mb-0">
                          <i class="bi bi-geo-alt me-2"></i>
                          {{ order.customer.address || 'Not specified' }}
                        </p>
                      </div>
                    </div> -->
                    <div class="customer-details">
                      <h6 class="section-title">Customer Details</h6>

                      <div class="d-flex align-items-center mb-3">
                        <div class="customer-avatar me-3">
                          <img :src="order.customer.profile_pic || '/images/default-profile.png'" class="rounded-circle"
                            width="50" height="50" alt="Customer Profile Picture">
                        </div>
                        <div>
                          <h6 class="mb-0">{{ order.customer.name }}</h6>
                          <small class="text-muted">{{ order.customer.phone_no }}</small>
                        </div>
                      </div>

                      <div class="customer-meta">
                        <!-- Gender -->
                        <p class="small mb-1">
                          <i v-if="order.customer.gender === 'Male'" class="bi bi-gender-male me-2 text-primary"></i>
                          <i v-else-if="order.customer.gender === 'Female'"
                            class="bi bi-gender-female me-2 text-danger"></i>
                          <i v-else class="bi bi-gender-ambiguous me-2 text-muted"></i>
                          <strong>Gender:</strong>
                          {{ order.customer.gender || 'Not specified' }}
                        </p>

                        <!-- Date of Birth -->
                        <p class="small mb-1">
                          <i class="bi bi-calendar-date me-2"></i>
                          <strong>Date of Birth:</strong>
                          {{ order.customer.dob ? formatDate(order.customer.dob) : 'Not specified' }}
                        </p>

                        <!-- Address -->
                        <p class="small mb-0">
                          <i class="bi bi-geo-alt-fill me-2"></i>
                          <strong>Address:</strong>
                          {{ order.customer.address || 'Not specified' }}
                        </p>
                      </div>
                    </div>

                  </div>
                  <div class="col-md-5">
                    <h6 class="section-title">Ordered Items</h6>
                    <div class="order-items">
                      <div v-for="(product, index) in order.products" :key="index" class="order-item d-flex py-2"
                        :class="{ 'border-top': index > 0 }">
                        <div class="flex-shrink-0 me-3">
                          <img :src="product.image || '/images/placeholder-product.png'" class="img-thumbnail"
                            width="60" height="60" alt="Product">
                        </div>
                        <div class="flex-grow-1">
                          <h6 class="mb-1">{{ product.product_name }}</h6>
                          <p class="small text-muted mb-1">Qty: {{ product.quantity }}</p>
                          <p class="small text-muted mb-0">Rs {{ product.price_at_purchase.toLocaleString('en-IN') }}
                            each</p>
                        </div>
                        <div class="flex-shrink-0 text-end">
                          <p class="mb-0 fw-bold">Rs {{ (product.price_at_purchase *
                            product.quantity).toLocaleString('en-IN') }}</p>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <h6 class="section-title">Order Summary</h6>
                    <div class="order-summary">
                      <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal:</span>
                        <span>Rs {{ order.total_payment.toLocaleString('en-IN') }}</span>
                      </div>
                      <div class="d-flex justify-content-between mb-2">
                        <span>Shipping:</span>
                        <span class="text-success">FREE</span>
                      </div>
                      <div class="d-flex justify-content-between fw-bold mb-3">
                        <span>Total:</span>
                        <span>Rs {{ order.total_payment.toLocaleString('en-IN') }}</span>
                      </div>

                      <div class="delivery-info mb-3">
                        <h6 class="section-title">Delivery Info</h6>
                        <p class="small mb-1">
                          <i class="bi bi-truck me-2"></i>
                          <span v-if="order.status === 'Dispatched' || order.status === 'Completed'">
                            Expected: {{ formatDate(order.expected_delivery_date) }}
                          </span>
                          <span v-else>Not dispatched yet</span>
                        </p>
                        <p class="small mb-0" v-if="order.status === 'Completed'">
                          <i class="bi bi-check-circle me-2"></i>
                          Delivered: {{ formatDate(order.actual_delivery_date) }}
                          <span v-if="order.delivered_on_time" class="badge bg-success ms-2">On Time</span>
                          <span v-else class="badge bg-warning text-dark ms-2">Delayed</span>
                        </p>
                      </div>

                      <div class="delivery-address">
                        <h6 class="section-title">Delivery Address</h6>
                        <p class="small">{{ order.delivery_address }}</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- <div class="card-footer">
                <div class="d-flex justify-content-between align-items-center">
                  <div>
                    <small class="text-muted">
                      Last updated: {{ formatDateTime(order.updated_at || order.order_datetime) }}
                    </small>
                  </div>
                  <div class="order-actions">
                    <template v-if="order.status === 'Pending'">
                      <button v-if="adminStore.hasPermission('Dispatch Order')"
                        class="btn btn-sm btn-outline-success me-2" @click="processOrder(order.order_id, 'Dispatched')">
                        <i class="bi bi-truck me-1"></i> Dispatch
                      </button>
                      <button v-if="adminStore.hasPermission('Cancel Order')" class="btn btn-sm btn-outline-danger"
                        @click="confirmCancelOrder(order.order_id)">
                        <i class="bi bi-x-circle me-1"></i> Cancel
                      </button>
                    </template>

<template v-else-if="order.status === 'Dispatched'">
                      <div class="d-flex align-items-center">
                        <div class="me-3" v-if="!isCompletingOrder[order.order_id]">
                          <label class="form-label small mb-0 me-2">Delivery Date:</label>
                         
                          <input type="date" v-model="deliveryDates[order.order_id]"
                            class="form-control form-control-sm" :max="new Date().toISOString().split('T')[0]"
                            :min="new Date(new Date().setFullYear(new Date().getFullYear() - 1)).toISOString().split('T')[0]"
                            @keydown.prevent>


                        </div>
                        <button v-if="adminStore.hasPermission('Complete Order')" class="btn btn-sm btn-success me-2"
                          @click="completeOrder(order.order_id)" :disabled="!deliveryDates[order.order_id]">
                          <i class="bi bi-check-circle me-1"></i>
                          {{ isCompletingOrder[order.order_id] ? 'Completing...' : 'Complete' }}
                        </button>
                        <button v-if="adminStore.hasPermission('Cancel Order')" class="btn btn-sm btn-outline-danger"
                          @click="confirmCancelOrder(order.order_id)">
                          <i class="bi bi-x-circle me-1"></i> Cancel
                        </button>
                      </div>
                    </template>

<template v-else>
                      <span class="text-muted small">No actions available</span>
                    </template>
</div>
</div>
</div> -->

              <div class="card-footer">
                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center">
                  <div class="mb-2 mb-sm-0 text-center text-sm-start">
                    <small class="text-muted">
                      Last updated: {{ formatDateTime(order.updated_at || order.order_datetime) }}
                    </small>
                  </div>
                  <div class="order-actions w-100 w-sm-auto">
                    <template v-if="order.status === 'Pending'">
                      <div class="d-flex justify-content-center justify-content-sm-end flex-wrap gap-2">
                        <button v-if="adminStore.hasPermission('Dispatch Order')"
                          class="btn btn-sm btn-outline-success me-sm-2">
                          <i class="bi bi-truck me-1"></i> Dispatch
                        </button>
                        <button v-if="adminStore.hasPermission('Cancel Order')" class="btn btn-sm btn-outline-danger">
                          <i class="bi bi-x-circle me-1"></i> Cancel
                        </button>
                      </div>
                    </template>

                    <template v-else-if="order.status === 'Dispatched'">

                      <div
                        class="d-flex flex-column flex-sm-row align-items-center justify-content-center justify-content-sm-end gap-2">
                        <div class="me-sm-3" v-if="!isCompletingOrder[order.order_id]">

                          <div class="d-flex align-items-center flex-wrap justify-content-center">
                            <input type="date" v-model="deliveryDates[order.order_id]"
                              class="form-control form-control-sm" :max="new Date().toISOString().split('T')[0]"
                              :min="new Date(new Date().setFullYear(new Date().getFullYear() - 1)).toISOString().split('T')[0]"
                              @keydown.prevent>
                          </div>
                        </div>
                        <div class="d-flex gap-2">
                          <button v-if="adminStore.hasPermission('Complete Order')"
                            class="btn btn-sm btn-success me-sm-2" :disabled="!deliveryDates[order.order_id]">
                            <i class="bi bi-check-circle me-1"></i>
                            {{ isCompletingOrder[order.order_id] ? 'Completing...' : 'Complete' }}
                          </button>
                          <button v-if="adminStore.hasPermission('Cancel Order')" class="btn btn-sm btn-outline-danger">
                            <i class="bi bi-x-circle me-1"></i> Cancel
                          </button>
                        </div>
                      </div>
                    </template>

                    <template v-else>
                      <span class="text-muted small d-block text-center text-sm-end">No actions available</span>
                    </template>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useOrderProcessingStore } from '../stores/OrderProcessingStore';
import { Modal } from 'bootstrap';
import Swal from 'sweetalert2';

import { useAdminStore } from '../stores/adminStore';
const adminStore = useAdminStore();
const orderStore = useOrderProcessingStore();
const deliveryDates = ref({});
const isCompletingOrder = ref({});

onMounted(async () => {
  await orderStore.fetchAllOrders();
});

const formatDate = (dateString) => {
  if (!dateString) return 'Not specified';
  const options = { year: 'numeric', month: 'short', day: 'numeric' };
  return new Date(dateString).toLocaleDateString(undefined, options);
};

const formatDateTime = (dateString) => {
  if (!dateString) return 'Not specified';
  const options = {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  };
  return new Date(dateString).toLocaleDateString(undefined, options);
};
const statusBadgeClass = (status) => {
  switch (status) {
    case 'Pending': return 'bg-warning text-dark';
    case 'Dispatched': return 'bg-info text-white';
    case 'Completed': return 'bg-success text-white';
    case 'Canceled': return 'bg-danger text-white';
    default: return 'bg-secondary text-white';
  }
};

const refreshOrders = async () => {
  await orderStore.fetchAllOrders();
};

const resetFilters = async () => {
  orderStore.filterStatus = 'all';
  orderStore.filterCustomerName = '';
  orderStore.filterMinPrice = null;
  orderStore.filterMaxPrice = null;
  orderStore.filterOrderDate = '';
  await orderStore.fetchAllOrders();
};
const processOrder = async (orderId, status) => {
  try {
    await orderStore.processOrder(orderId, status);
    Swal.fire({
      icon: 'success',
      title: 'Order Updated!',
      text: `Order status changed to ${status}`,
      timer: 1500,
      showConfirmButton: false
    });
    await orderStore.fetchAllOrders();
  } catch (error) {
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: error.message || 'Failed to update order status',
    });
  }
};
const completeOrder = async (orderId) => {
  if (!deliveryDates.value[orderId]) {
    Swal.fire({
      icon: 'error',
      title: 'Missing Date',
      text: 'Please select a delivery date',
    });
    return;
  }

  isCompletingOrder.value[orderId] = true;
  try {
    await orderStore.processOrder(orderId, 'Completed', deliveryDates.value[orderId]);
    Swal.fire({
      icon: 'success',
      title: 'Order Completed!',
      text: `Order marked as delivered on ${formatDate(deliveryDates.value[orderId])}`,
      timer: 2000,
      showConfirmButton: false
    });
    await orderStore.fetchAllOrders();
  } catch (error) {
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: error.message || 'Failed to complete order',
    });
  } finally {
    isCompletingOrder.value[orderId] = false;
  }
};
const confirmCancelOrder = (orderId) => {
  Swal.fire({
    title: 'Are you sure?',
    text: "You won't be able to revert this!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: 'Yes, cancel it!'
  }).then((result) => {
    if (result.isConfirmed) {
      processOrder(orderId, 'Canceled');
    }
  });
};
</script>

<style scoped>
.admin-orders-page {
  padding-top: 1rem;
}

.admin-header {
  border-bottom: 1px solid #eee;
  margin-bottom: 1.5rem;
}

.page-title {
  font-size: 1.8rem;
  font-weight: 600;
  color: #333;
  margin: 0;
}

.section-title {
  font-size: 0.9rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  color: #6c757d;
  margin-bottom: 1rem;
  padding-bottom: 0.5rem;
  border-bottom: 1px solid #eee;
}

.order-card {
  transition: all 0.3s ease;
}

.order-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.order-item {
  transition: background-color 0.2s ease;
}

.order-item:hover {
  background-color: rgba(0, 0, 0, 0.02);
}

.badge {
  font-size: 0.75rem;
  font-weight: 500;
  padding: 0.35em 0.65em;
}

.spin {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from {
    transform: rotate(0deg);
  }

  to {
    transform: rotate(360deg);
  }
}

@media (max-width: 992px) {
  .page-title {
    font-size: 1.5rem;
  }

  .order-card .row>div {
    margin-bottom: 1.5rem;
  }
}

@media (max-width: 768px) {
  .admin-header {
    flex-direction: column;
    align-items: flex-start;
  }

  .card-footer {
    flex-direction: column;
    gap: 1rem;
  }

  .order-actions {
    width: 100%;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
  }

  /* .order-actions .btn {
    width: 100%;
  } */

  .delivery-info {
    margin-top: 1rem;
  }
}

@media (max-width: 576px) {
  .filters-section .row>div {
    margin-bottom: 0.5rem;
  }

  .section-title {
    font-size: 0.8rem;
  }
}
</style>