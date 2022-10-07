<script setup>
    import AppNavbarItem from './AppNavbarItem.vue'
    import AppNavbarLink from './link/AppNavbarLink.vue'
    import AppNavbarUser from './AppNavbarUser.vue'
    import AppNotifications from './notification/AppNotifications.vue'
    import useUser from '../../stores/security'

    const user = useUser()
</script>

<template>
    <div class="collapse navbar-collapse">
        <ul class="me-auto navbar-nav">
            <AppNavbarItem v-if="user.isManagementReader" id="management" icon="sitemap" title="Direction">
                <template v-if="user.isManagementAdmin">
                    <AppDropdownItem disabled variant="warning">
                        Administrateur
                    </AppDropdownItem>
                    <AppNavbarLink icon="comments-dollar" to="vat-messages" variant="warning">
                        Messages TVA
                    </AppNavbarLink>
                </template>
            </AppNavbarItem>
        </ul>
    </div>
    <Suspense>
        <template #fallback>
            <span class="spinner-border text-white" role="status"/>
        </template>
        <AppNotifications/>
    </Suspense>
    <AppNavbarUser/>
</template>
