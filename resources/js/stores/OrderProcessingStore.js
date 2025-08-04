import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import axios from 'axios';

export const useOrderProcessingStore = defineStore('OrderProcessingStore', () => {
    const orders = ref([]);
    const isLoading = ref(false);
    const error = ref(null);
    const filterStatus = ref('all');
    const filterCustomerName = ref('');
    const filterMinPrice = ref(null);
    const filterMaxPrice = ref(null);
    const filterOrderDate = ref('');
    async function fetchAllOrders() {
        try {
            isLoading.value = true;
            const response = await axios.get('/orders/details');
            orders.value = response.data.data;
        } catch (err) {
            error.value = err.message;
            console.error('Failed to fetch orders:', err);
        } finally {
            isLoading.value = false;
        }
    }
    async function processOrder(orderId, status, actual_delivery_date = null) {
        try {
            isLoading.value = true;

            const payload = { status };
            if (status === 'Completed' && actual_delivery_date) {
                payload.actual_delivery_date = actual_delivery_date;
            }

            const response = await axios.post(`/process-order/${orderId}`, payload);
            const updatedOrder = response.data.data;

            const index = orders.value.findIndex(order => order.order_id === orderId);
            if (index !== -1) {
                orders.value[index] = updatedOrder;
            }
            return updatedOrder;
        } catch (err) {
            if (err.response && err.response.data) {
                const serverError = err.response.data;
                if (serverError.errors) {
                    error.value = Object.values(serverError.errors).flat().join(', ');
                } else if (serverError.message) {
                    error.value = serverError.message;
                } else {
                    error.value = 'An unknown error occurred.';
                }

                console.error('Server error:', serverError);
            } else {
                error.value = err.message || 'Network error';
                console.error('Unexpected error:', err);
            }

            throw err;
        } finally {
            isLoading.value = false;
        }

    }

    function removeOrderLocally(orderId) {
        orders.value = orders.value.filter(order => order.order_id !== orderId);
    }


    const filteredOrders = computed(() => {
        return orders.value.filter(order => {
            const matchesStatus =
                filterStatus.value === 'all' || order.status === filterStatus.value;

            const matchesName =
                !filterCustomerName.value.trim() ||
                order.customer?.name
                    ?.toLowerCase()
                    .includes(filterCustomerName.value.toLowerCase());

            const totalPrice = order.total_payment;

            const matchesMinPrice =
                filterMinPrice.value === null || totalPrice >= filterMinPrice.value;

            const matchesMaxPrice =
                filterMaxPrice.value === null || totalPrice <= filterMaxPrice.value;

            const matchesDate =
                !filterOrderDate.value ||
                order.order_datetime?.startsWith(filterOrderDate.value);

            return (
                matchesStatus &&
                matchesName &&
                matchesMinPrice &&
                matchesMaxPrice &&
                matchesDate
            );
        });
    });

    return {
        orders,
        isLoading,
        error,
        filterStatus,
        filterCustomerName,
        filterMinPrice,
        filterMaxPrice,
        filterOrderDate,
        fetchAllOrders,
        processOrder,
        removeOrderLocally,
        filteredOrders
    };
});
