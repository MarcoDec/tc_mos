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
            <AppNavbarItem v-if="user.isPurchaseReader" id="purchase" icon="shopping-bag" title="Achats">
                <template v-if="user.isPurchaseAdmin">
                    <AppDropdownItem disabled variant="warning">
                        Administrateur
                    </AppDropdownItem>
                    <AppNavbarLink icon="magnet" to="attributes" variant="warning">
                        Attributs
                    </AppNavbarLink>
                </template>
            </AppNavbarItem>
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
    <AppSuspense variant="white">
        <AppNotifications/>
    </AppSuspense>
    <AppNavbarUser/>
</template>
