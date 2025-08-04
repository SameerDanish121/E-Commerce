<template>
  <div class="customer-layout">
    <AdminHeader />
    <main class="flex-grow-1">
      <router-view />
    </main>
    <AdminFooter />
  </div>
</template>

<script>
import AdminHeader from '../js/components/admin/AdminHeader.vue'
import AdminFooter from '../js/components/admin/AdminFooter.vue'

export default {
  name: 'CustomerLayout',
  components: {
    AdminHeader,
    AdminFooter
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