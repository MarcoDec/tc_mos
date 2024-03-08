<script setup>
    import AppSuspense from '../../../../../AppSuspense.vue'
    import AppMachineTabMaintenance from './tabs/AppMachineTabMaintenance.vue'
    import AppMachineTabHistMaintenance from './tabs/AppMachineTabHistMaintenance.vue'
</script>

<template>
    <div>
        <AppTabs id="tabs-ws-inlist" class="gui-start-content">
            <AppTab id="gui-start-maintenances" active icon="paint-roller" title="Types de maintenances" tabs="tabs-ws-inlist">
                <AppSuspense><AppMachineTabMaintenance/></AppSuspense>
            </AppTab>
            <AppTab id="gui-start-history" icon="history" title="Historique des maintenances" tabs="tabs-ws-inlist">
                <AppSuspense><AppMachineTabHistMaintenance/></AppSuspense>
            </AppTab>
        </AppTabs>
    </div>
</template>
