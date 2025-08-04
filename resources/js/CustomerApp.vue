<!-- <template>
  <div>
    <h2>Customer Panel</h2>
    <CustomerHeader />
    
    <router-view></router-view>

    <CustomerFooter />
  </div>
</template>

<script setup>
import CustomerHeader from '../js/components/customer/CustomerHeader.vue'
import CustomerFooter from '../js/components/customer/CustomerFooter.vue'
</script> -->

<template>
  <div class="customer-layout">
    <CustomerHeader />
    <main class="flex-grow-1">
      <router-view />
    </main>
    <CustomerFooter />
  </div>
</template>

<script>
import CustomerHeader from '../js/components/customer/CustomerHeader.vue'
import CustomerFooter from '../js/components/customer/CustomerFooter.vue'

export default {
  name: 'CustomerLayout',
  components: {
    CustomerHeader,
    CustomerFooter
  }
}
</script>

<style>
.customer-layout {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}

main {
  flex: 1;
}
</style>