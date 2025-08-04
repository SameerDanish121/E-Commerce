import { defineStore } from 'pinia';
import { ref } from 'vue';
import axios from 'axios';

export const useAllCustomerStore = defineStore('AllCustomerStore', () => {
    const customers = ref([]);
    const isLoading = ref(false);
    const error = ref(null);

    async function fetchAllCustomers() {
        try {
            isLoading.value = true;
            const response = await axios.get('/all-customers');
            customers.value = response.data.customers;
        } catch (err) {
            if (err.response && err.response.data) {
                const serverError = err.response.data;
                error.value = serverError.message || 'Server Error';
                console.error('Server error:', serverError);
            } else {
                error.value = err.message || 'Network Error';
                console.error('Unexpected error:', err);
            }
        } finally {
            isLoading.value = false;
        }
    }

    return {
        customers,
        isLoading,
        error,
        fetchAllCustomers
    };
});
