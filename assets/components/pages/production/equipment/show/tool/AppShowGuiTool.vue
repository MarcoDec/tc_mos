<script setup>
    import AppShowGuiGen from '../../../../AppShowGuiGen.vue'
    import AppSuspense from '../../../../../AppSuspense.vue'
    import AppToolFormShow from './AppToolFormShow.vue'
    //import useOptions from '../../../../../stores/option/options'
    import {useRoute} from 'vue-router'
    import {useToolsStore} from '../../../../../../stores/production/engine/tool/tools'

    const route = useRoute()
    const idEngine = Number(route.params.id_engine)
    //region récupération information Outils
    const useFetchToolsStore = useToolsStore()
    useFetchToolsStore.fetchOne(idEngine)
    //endregion
    // //region récupéation information unité ??
    // const fetchUnits = useOptions('units')
    // fetchUnits.fetchOp()
    // //endregion
</script>

<template>
    <AppSuspense>
        <AppShowGuiGen>
            <template #gui-header>
                <div v-if="useFetchToolsStore.isLoaded" class="bg-white border-1 border-dark">
                    <b>{{ useFetchToolsStore.engine.code }}</b>: {{ useFetchToolsStore.engine.name }}
                </div>
            </template>
            <template #gui-left>
                <AppSuspense><AppToolFormShow v-if="useFetchToolsStore.isLoaded"/></AppSuspense>
            </template>
            <template #gui-bottom>
                <!--            <AppTabs id="gui-bottom">-->
                <!--                <AppTab id="gui-bottom-components" active icon="puzzle-piece" tabs="gui-bottom" title="Fournitures"/>-->
                <!--                <AppTab id="gui-bottom-receipts" icon="receipt" tabs="gui-bottom" title="Réceptions"/>-->
                <!--                <AppTab id="gui-bottom-orders" icon="shopping-cart" tabs="gui-bottom" title="Commandes"/>-->
                <!--            </AppTabs>-->
            </template>
            <template #gui-right>
                <!--            {{ route.params.id_product }}-->
            </template>
        </AppShowGuiGen>
    </AppSuspense>
</template>

<style>
.border-dark {
    border-bottom: 1px solid grey;
}
</style>

