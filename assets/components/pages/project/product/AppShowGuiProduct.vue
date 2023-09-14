<script setup>
    import AppProductFormShow from './AppProductFormShow.vue'
    import AppShowGuiGen from '../../AppShowGuiGen.vue'
    import useOptions from '../../../../stores/option/options'
    import {useProductStore} from '../../../../stores/project/product/products'
    import {useRoute} from 'vue-router'
    import AppProductShowInlist from './bottom/AppProductShowInlist.vue'
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
            <AppSuspense><AppProductShowInlist/></AppSuspense>
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

