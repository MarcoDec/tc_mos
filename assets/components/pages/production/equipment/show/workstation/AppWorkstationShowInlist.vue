<script setup>
    //inlist
    // import generateItems from '../../../../../../stores/table/items'
    // import {useRoute} from 'vue-router'
    // import {useTableMachine} from '../../../../../../machine'
    import AppSuspense from '../../../../../AppSuspense.vue'
    import AppWorkstationTabMaintenance from './tabs/AppWorkstationTabMaintenance.vue'
    import AppWorkstationTabHistMaintenance from './tabs/AppWorkstationTabHistMaintenance.vue'
    // import AppWorkstationTabRechange from './tabs/AppWorkstationTabRechange.vue'
    // import AppWorkstationTabOF from './tabs/AppWorkstationTabOF.vue'
    // const products = generateItems('products')
    // const route = useRoute()
    // const machine = useTableMachine(route.name)
</script>

<template>
    <div>
        <AppTabs id="tabs-ws-inlist" class="gui-start-content">
            <AppTab id="gui-start-maintenances" active icon="paint-roller" title="Types de maintenances" tabs="tabs-ws-inlist">
                <AppSuspense><AppWorkstationTabMaintenance/></AppSuspense>
            </AppTab>
            <AppTab id="gui-start-history" icon="history" title="Historique des maintenances" tabs="tabs-ws-inlist">
                <AppSuspense><AppWorkstationTabHistMaintenance/></AppSuspense>
            </AppTab>
            <!--            <AppTab id="gui-start-Pieces" icon="retweet" title="Pièces de rechange" tabs="tabs-ws-inlist">-->
            <!--                <AppSuspense><AppWorkstationTabRechange/></AppSuspense>-->
            <!--            </AppTab>-->
            <!--            <AppTab id="gui-start-of" icon="file-invoice" title="OF" tabs="tabs-ws-inlist">-->
            <!--                <AppSuspense><AppWorkstationTabOF/></AppSuspense>-->
            <!--            </AppTab>-->
        </AppTabs>
    </div>
</template>
