<script setup>
    import AppShowGuiGen from '../../../AppShowGuiGen.vue'
    import AppWorkstationFormShow from './AppWorkstationFormShow.vue'
    import {useRoute} from 'vue-router'
    import {useWorkstationsStore} from '../../../../../stores/production/engine/workstation/workstations'

    const route = useRoute()
    const idEngine = Number(route.params.id_engine)
    //region récupération information Workstations
    const useFetchWorkstationsStore = useWorkstationsStore()
    useFetchWorkstationsStore.fetchOne(idEngine)
    //endregion
</script>

<template>
    <AppShowGuiGen>
        <template #gui-header>
            <div v-if="useFetchWorkstationsStore.isLoaded" class="bg-white border-1 border-dark">
                <b>{{ useFetchWorkstationsStore.engine.code }}</b>: {{ useFetchWorkstationsStore.engine.name }}
            </div>
        </template>
        <template #gui-left>
            <AppSuspense><AppWorkstationFormShow v-if="useFetchWorkstationsStore.isLoaded"/></AppSuspense>
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
</template>

<style>
.border-dark {
    border-bottom: 1px solid grey;
}
</style>

