

import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

export const useCartStore = defineStore('cart', () => {
  const items = ref([])
  
  const totalItems = computed(() => {
    return items.value.reduce((total, item) => total + item.quantity, 0)
  })
  
  const subtotal = computed(() => {
    return items.value.reduce((total, item) => total + (item.price * item.quantity), 0)
  })
  
  const total = computed(() => {
    return subtotal.value
  })
  
  function addToCart(product) {
    const existingItem = items.value.find(item => item.id === product.id)
    
    if (existingItem) {
      const newQuantity = existingItem.quantity + product.quantity
      
      // Check if we have enough stock
      if (newQuantity <= product.stock + existingItem.quantity) {
        existingItem.quantity = newQuantity
      } else {
        // If not enough stock, set to max available
        existingItem.quantity = product.stock + existingItem.quantity
      }
    } else {
      if (product.stock > 0) {
        items.value.push({
          ...product,
          quantity: product.quantity
        })
      }
    }
  }
  
  function removeFromCart(productId) {
    items.value = items.value.filter(item => item.id !== productId)
  }
  
  function updateQuantity(productId, newQuantity) {
    const item = items.value.find(item => item.id === productId)
    if (item && newQuantity > 0 && newQuantity <= item.stock) {
      item.quantity = newQuantity
    }
  }
  
  function clearCart() {
    items.value = []
  }
  
  return {
    items,
    totalItems,
    subtotal,
    total,
    addToCart,
    removeFromCart,
    updateQuantity,
    clearCart
  }
})