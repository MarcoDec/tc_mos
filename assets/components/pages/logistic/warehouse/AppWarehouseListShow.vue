<script setup>
    import {useWarehouseStocksItemsStore} from '../../../../stores/production/warehouseStocksItems'
    //import AppWarehouseListStockTable from './provisoir/AppWarehouseListStockTable.vue'
    import AppWarehouseListStockTable from './tabs/WarehouseStocks/AppWarehouseListStockTable.vue'
    //import AppWarehouseListVolumeTable from './provisoir/AppWarehouseListVolumeTable.vue'
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
        <!--        <AppTab id="gui-start-volume" title="Volume" icon="ruler-vertical" tabs="gui-start-bottom">-->
        <!--            <AppSuspense>-->
        <!--                <div>DE LU</div>-->
        <!--                &lt;!&ndash;                <AppWarehouseListVolumeTable/>&ndash;&gt;-->
        <!--            </AppSuspense>-->
        <!--        </AppTab>-->
    </AppTabs>
</template>

<style scoped>
    .active { position: relative; z-index: 0; overflow: scroll; max-height: 100%; min-width: 100vw; padding-bottom: 100px}
</style>
