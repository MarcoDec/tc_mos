<script setup>
    import AppNavbarUser from './AppNavbarUser.vue'
    import useUser from '../../stores/security'

    const user = useUser()
</script>

<template>
    <div class="collapse navbar-collapse">
        <ul class="me-auto navbar-nav"/>
    </div>
    <AppNavbarUser v-if="user.isLogged"/>
</template>
