import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import axios from 'axios';

export const useProductStore = defineStore('products', () => {
  const products = ref([]);
  const categories = ref([]);
  const searchQuery = ref('');
  const selectedCategory = ref('all');
  const isLoading = ref(false);
  const error = ref(null);

  const filteredProducts = computed(() => {
    let filtered = products.value;
    
    if (searchQuery.value) {
      filtered = filtered.filter(product => 
        product.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
        product.description.toLowerCase().includes(searchQuery.value.toLowerCase())
      );
    }
    
    if (selectedCategory.value !== 'all') {
      filtered = filtered.filter(product => 
        product.category === selectedCategory.value
      );
    }
    
    return filtered;
  });

  async function fetchProducts() {
    try {
      isLoading.value = true;
      const response = await axios.get('/products');
      products.value = response.data.map(product => ({
        ...product,
        image_url: product.image ? `${product.image}` : '/images/temp.jpg'
      }));
    } catch (err) {
      error.value = err.message;
      console.error('Error fetching products:', err);
    } finally {
      isLoading.value = false;
    }
  }

  return {
    products,
    categories,
    filteredProducts,
    searchQuery,
    selectedCategory,
    isLoading,
    error,
    fetchProducts
  };
});