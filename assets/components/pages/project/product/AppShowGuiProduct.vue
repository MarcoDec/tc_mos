<script setup>
    import AppProductFormShow from './AppProductFormShow.vue'
    import AppShowGuiGen from '../../AppShowGuiGen.vue'
    import useOptions from '../../../../stores/option/options'
    import {useProductStore} from '../../../../stores/project/product/products'
    import {useRoute} from 'vue-router'

    const route = useRoute()
    const idProduct = Number(route.params.id_product)
    const fetchUnits = useOptions('units')
    const useFetchProductStore = useProductStore()
    fetchUnits.fetchOp()
    useFetchProductStore.fetchOne(idProduct)
</script>

<template>
    <AppShowGuiGen>
        <template #gui-header>
            <div v-if="useFetchProductStore.isLoaded" class="bg-white border-1 border-dark">
                <b>{{ useFetchProductStore.product.code }}</b>: {{ useFetchProductStore.product.name }}
            </div>
        </template>
        <template #gui-left>
            <AppSuspense><AppProductFormShow v-if="useFetchProductStore.isLoaded && fetchUnits.isLoaded"/></AppSuspense>
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

