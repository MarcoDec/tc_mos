<script setup>
    import AppComponentFormShow from '../../../../router/pages/component/AppComponentFormShow.vue'
    import useOptions from '../../../../stores/option/options'
    import AppShowGuiGen from '../../AppShowGuiGen.vue'
    import {useComponentListStore} from '../../../../stores/component/components'
    import {useRoute} from 'vue-router'

    const route = useRoute()
    const idComponent = Number(route.params.id_component)
    const fetchUnits = useOptions('units')
    const useFetchComponentStore = useComponentListStore()
    fetchUnits.fetchOp()
    useFetchComponentStore.fetchOne(idComponent)
</script>

<template>
    <AppShowGuiGen>
        <template #gui-header>
            <div class="bg-white border-1 border-dark">
                <b>{{ useFetchComponentStore.component.code }}</b>: {{ useFetchComponentStore.component.name }}
            </div>
        </template>
        <template #gui-left>
            <AppSuspense><AppComponentFormShow v-if="useFetchComponentStore.isLoaded && fetchUnits.isLoaded"/></AppSuspense>
        </template>
        <template #gui-bottom>
            <!--            <AppTabs id="gui-bottom">-->
            <!--                <AppTab id="gui-bottom-components" active icon="puzzle-piece" tabs="gui-bottom" title="Fournitures"/>-->
            <!--                <AppTab id="gui-bottom-receipts" icon="receipt" tabs="gui-bottom" title="Réceptions"/>-->
            <!--                <AppTab id="gui-bottom-orders" icon="shopping-cart" tabs="gui-bottom" title="Commandes"/>-->
            <!--            </AppTabs>-->
        </template>
        <template #gui-right/>
    </AppShowGuiGen>
</template>

<style>
    .border-dark {
        border-bottom: 1px solid grey;
    }
</style>
