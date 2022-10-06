<script setup>
    import AppNavbarUser from './AppNavbarUser.vue'
    import AppNotifications from './notification/AppNotifications.vue'
</script>

<template>
    <div class="collapse navbar-collapse">
        <ul class="me-auto navbar-nav"/>
    </div>
    <Suspense>
        <template #fallback>
            <span class="spinner-border text-white" role="status"/>
        </template>
        <AppNotifications/>
    </Suspense>
    <AppNavbarUser/>
</template>
