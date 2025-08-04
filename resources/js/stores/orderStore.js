import { defineStore } from 'pinia';
import { ref } from 'vue';
import axios from 'axios';
import { useCustomerStore } from './customerStore';

export const useOrderStore = defineStore('orders', () => {
  const orders = ref([]);
  const isLoading = ref(false);
  const error = ref(null);

  async function fetchOrders() {
    const customerStore = useCustomerStore();
    const customerId = customerStore.id;

    if (!customerId) {
      error.value = 'Please login to view your orders';
      return;
    }

    try {
      isLoading.value = true;
      error.value = null;
      
      const response = await axios.get(`/customer-orders/${customerId}`);
      
      if (response.data?.success) {
        orders.value = response.data.orders.map(order => ({
          id: order.order_id,
          order_datetime: order.order_datetime,
          status: order.status,
          total_payment: order.total_payment,
          delivered_on_time: order.delivered_on_time === 'Yes',
          delivery_address: order.delivery_address,
          expected_delivery_date: order.expected_delivery_date,
          actual_delivery_date: order.actual_delivery_date,
          items: order.products.map(product => ({
            id: product.product_id,
            product_id: product.product_id,
            name: product.name,
            category: product.category,
            price_at_purchase: product.price_at_purchase,
            quantity: product.quantity,
            image_url: product.image_url || '/images/default-product.png'
          }))
        }));
      } else {
        error.value = response.data?.message || 'Failed to fetch orders';
        orders.value = [];
      }
    } catch (err) {
      error.value = err.response?.data?.message || err.message || 'Failed to fetch orders';
      orders.value = [];
      console.error('Error fetching orders:', err);
    } finally {
      isLoading.value = false;
    }
  }

  function clearOrders() {
    orders.value = [];
    error.value = null;
  }

  return {
    orders,
    isLoading,
    error,
    fetchOrders,
    clearOrders
  };
});