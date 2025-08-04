<template>
    <div class="auth-wrapper">
        <router-view></router-view>
    </div>
</template>

<script>
export default {
    name: 'App',
}
</script>

<style scoped>
.auth-wrapper {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #f8f9fa;
}
</style>