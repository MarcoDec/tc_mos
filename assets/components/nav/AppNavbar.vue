<script setup>
    import AppCollapse from './AppCollapse.vue'
    import AppNavbarItem from './AppNavbarItem.vue'
    import AppNavbarLink from './AppNavbarLink'
    import AppNavbarUser from './AppNavbarUser.vue'
    import {computed} from 'vue'
    import {generateVariant} from '../validators'
    import useUserStore from '../../stores/hr/employee/user'

    const props = defineProps({variant: generateVariant('dark')})
    const user = useUserStore()
    const css = computed(() => `bg-${props.variant} navbar-${props.variant}`)
</script>

<template>
    <nav :class="css" class="mb-1 navbar navbar-expand-sm">
        <AppContainer fluid>
            <span class="m-0 navbar-brand p-0">
                <AppRouterLink to="home">T-Concept</AppRouterLink>
            </span>
            <AppCollapse/>
            <div id="top-navbar" class="collapse navbar-collapse">
                <ul class="me-auto navbar-nav">
                    <AppNavbarItem v-if="user.isPurchaseReader" id="purchase" icon="shopping-bag" title="Achats">
                        <template v-if="user.isPurchaseAdmin">
                            <AppDropdownItem disabled variant="warning">
                                Administrateur
                            </AppDropdownItem>
                            <AppNavbarLink disabled icon="magnet" to="attributes" variant="danger">
                                Attributs
                            </AppNavbarLink>
                            <AppNavbarLink icon="layer-group" to="component-families" variant="warning">
                                Familles de composants
                            </AppNavbarLink>
                        </template>
                    </AppNavbarItem>
                    <AppNavbarItem v-if="user.isManagementReader" id="management" icon="sitemap" title="Direction">
                        <template v-if="user.isManagementAdmin">
                            <AppDropdownItem disabled variant="warning">
                                Administrateur
                            </AppDropdownItem>
                            <AppNavbarLink icon="palette" to="colors" variant="warning">
                                Couleurs
                            </AppNavbarLink>
                        </template>
                    </AppNavbarItem>
                </ul>
            </div>
            <AppNavbarUser v-if="user.isLogged"/>
        </AppContainer>
    </nav>
</template>
