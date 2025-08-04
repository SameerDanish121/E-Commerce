import { defineStore } from 'pinia';
import { ref } from 'vue';
import axios from 'axios';

export const useAdminProductStore = defineStore('adminProducts', () => {
  const adminProducts = ref([]);
  const isLoading = ref(false);
  const error = ref(null);

  async function fetchProducts() {
    try {
      isLoading.value = true;
      const response = await axios.get('/Getproducts');
      adminProducts.value = response.data;
    } catch (err) {
      error.value = err.message;
      console.error('Failed to fetch products:', err);
    } finally {
      isLoading.value = false;
    }
  }

  async function addProduct(formData) {
    try {
      isLoading.value = true;
  console.log([...formData.entries()]);
      const response = await axios.post('/product/add', formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      });
      adminProducts.value.unshift(response.data.product);
      return response.data.product;
    } catch (err) {
      error.value = err.message;
      throw err;
    } finally {
      isLoading.value = false;
    }
  }

  async function updateProduct(id, formData) {
    try {
      isLoading.value = true;
      const response = await axios.post(`/product/update/${id}`, formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      });

      const index = adminProducts.value.findIndex(p => p.id === id);
      if (index !== -1) {
        adminProducts.value[index] = response.data.product;
      }

      return response.data.product;
    } catch (err) {
      error.value = err.message;
      throw err;
    } finally {
      isLoading.value = false;
    }
  }

  // ✅ Toggle active/inactive status
  async function toggleProductStatus(id) {
    try {
      const response = await axios.post(`/product/toggle-status/${id}`);
      const updated = response.data.product;

      const index = adminProducts.value.findIndex(p => p.id === id);
      if (index !== -1) {
        adminProducts.value[index] = updated;
      }

      return updated;
    } catch (err) {
      error.value = err.message;
      throw err;
    }
  }

  // ✅ Re-stock product
  async function restockProduct(id, quantity) {
    try {
      const response = await axios.post(`/product/restock/${id}`, { quantity });

      const updated = response.data.product;
      const index = adminProducts.value.findIndex(p => p.id === id);
      if (index !== -1) {
        adminProducts.value[index] = updated;
      }

      return updated;
    } catch (err) {
      error.value = err.message;
      throw err;
    }
  }
  function deleteProductLocally(id) {
    adminProducts.value = adminProducts.value.filter(p => p.id !== id);
  }

  return {
    adminProducts,
    isLoading,
    error,
    fetchProducts,
    addProduct,
    updateProduct,
    toggleProductStatus,
    restockProduct,
    deleteProductLocally
  };
});
