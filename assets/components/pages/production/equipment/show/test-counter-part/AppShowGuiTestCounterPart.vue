<script setup>
    import AppShowGuiGen from '../../../../AppShowGuiGen.vue'
    import AppTestCounterPartFormShow from './AppTestCounterPartFormShow.vue'
    import {useCounterPartStore} from '../../../../../../stores/production/engine/test-counter-part/testCounterPart'
    import useOptions from '../../../../../../stores/option/options'
    import {useRoute} from 'vue-router'

    const route = useRoute()
    const idEngine = Number(route.params.id_engine)
    const fetchUnits = useOptions('units')
    const useFetchCounterPartStore = useCounterPartStore()
    fetchUnits.fetchOp()
    useFetchCounterPartStore.fetchOne(idEngine)
</script>

<template>
    <AppSuspense>
        <AppShowGuiGen>
            <template #gui-header>
                <div v-if="useFetchCounterPartStore.isLoaded" class="bg-white border-1 border-dark">
                    <b>{{ useFetchCounterPartStore.engine.code }}</b>: {{ useFetchCounterPartStore.engine.name }}
                </div>
            </template>
            <template #gui-left>
                <AppSuspense><AppTestCounterPartFormShow v-if="useFetchCounterPartStore.isLoaded && fetchUnits.isLoaded"/></AppSuspense>
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

