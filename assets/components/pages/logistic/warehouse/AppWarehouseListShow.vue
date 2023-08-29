<script setup>
    import {computed, ref} from 'vue'
    import {useWarehouseStocksItemsStore} from '../../../../stores/production/warehouseStocksItems'
    import AppWarehouseListStockTable from './provisoir/AppWarehouseListStockTable.vue'
    import AppWarehouseListVolumeTable from './provisoir/AppWarehouseListVolumeTable.vue'
    const guiRatio = ref(0.5)
    const guiRatioPercent = computed(() => `${guiRatio.value * 100}%`)

    const storeWarehouseStocksItems = useWarehouseStocksItemsStore()
    storeWarehouseStocksItems.fetchItems()
</script>

<template>
    <AppTabs id="gui-start-bottom" class="gui-start-content-bottom">
        <AppTab id="gui-start-stock" active title="Stock" icon="cubes-stacked" tabs="gui-start-bottom">
            <AppSuspense>
                <AppWarehouseListStockTable/>
            </AppSuspense>
        </AppTab>
        <AppTab id="gui-start-volume" title="Volume" icon="ruler-vertical" tabs="gui-start-bottom">
            <AppSuspense>
                <AppWarehouseListVolumeTable/>
            </AppSuspense>
        </AppTab>
    </AppTabs>
</template>

<style scoped>
    .gui {
        --gui-ratio: v-bind(guiRatioPercent);
    }
</style>
