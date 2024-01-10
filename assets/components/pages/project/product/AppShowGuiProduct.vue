<script setup>
    import {onBeforeUnmount} from 'vue'
    import AppProductFormShow from './AppProductFormShow.vue'
    import AppShowGuiGen from '../../AppShowGuiGen.vue'
    import useOptions from '../../../../stores/option/options'
    import {useProductStore} from '../../../../stores/project/product/products'
    import {useRoute} from 'vue-router'
    import AppProductShowInlist from './AppProductShowInlist.vue'
    const route = useRoute()
    const idProduct = Number(route.params.id_product)
    const fetchUnits = useOptions('units')
    const useFetchProductStore = useProductStore()
    try {
        const allFetchs = [useFetchProductStore.fetchOne(idProduct), fetchUnits.fetchOp()]
        Promise.allSettled(allFetchs)
    } catch (e) {
        console.error(e)
    }
    onBeforeUnmount(() => {
        useFetchProductStore.reset()
    })
</script>

<template>
    <AppShowGuiGen>
        <template #gui-header>
            <div class="bg-white border-1 border-dark">
                <b>{{ useFetchProductStore.product.code }}</b>: {{ useFetchProductStore.product.name }}
            </div>
        </template>
        <template #gui-left>
            <AppSuspense><AppProductFormShow v-if="useFetchProductStore.isLoaded && fetchUnits.isLoaded"/></AppSuspense>
        </template>
        <template #gui-bottom>
            <AppSuspense><AppProductShowInlist v-if="useFetchProductStore.isLoaded && fetchUnits.isLoaded"/></AppSuspense>
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

