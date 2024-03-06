<script setup>
    import {useWarehouseStocksItemsStore} from '../../../../stores/production/warehouseStocksItems'
    import AppWarehouseListStockTable from './tabs/WarehouseStocks/AppWarehouseListStockTable.vue'
    import AppTab from '../../../tab/AppTab.vue'
    import AppWarehouseListZoneTable from './tabs/WarehouseZones/AppWarehouseListZoneTable.vue'
    import {useWarehouseShowStore as warehouseStore} from '../../../../stores/logistic/warehouses/warehouseShow'
    import AppTabFichiers from '../../../tab/AppTabFichiers.vue'
    import {useRoute} from 'vue-router'
    import {useWarehouseAttachmentStore} from '../../../../stores/logistic/warehouses/warehouseAttachements'

    const storeWarehouseStocksItems = useWarehouseStocksItemsStore()
    const maRoute = useRoute()
    const warehouseId = Number(maRoute.params.id_warehouse)
    const warehouseAttachmentStore = useWarehouseAttachmentStore()
    warehouseAttachmentStore.fetchByElement(warehouseId)
    storeWarehouseStocksItems.fetchItems()
</script>

<template>
    <AppTabs id="gui-start-bottom" class="gui-start-content-bottom">
        <AppTab id="gui-start-files" active title="Fichiers" icon="folder" tabs="gui-start-bottom">
            <AppSuspense>
                <AppTabFichiers
                    attachment-element-label="warehouse"
                    :element-api-url="`/api/warehouses/${warehouseId}`"
                    :element-attachment-store="warehouseAttachmentStore"
                    :element-id="warehouseId"
                    element-parameter-name="WAREHOUSE_ATTACHMENT_CATEGORIES"
                    :element-store="warehouseStore"/>
            </AppSuspense>
        </AppTab>
        <AppTab id="gui-start-stock" title="Stock" icon="cubes-stacked" tabs="gui-start-bottom">
            <AppSuspense>
                <AppWarehouseListStockTable/>
            </AppSuspense>
        </AppTab>

        <AppTab id="gui-start-zones" title="Zones" tabs="gui-start-bottom" icon="map-marked">
            <AppSuspense>
                <AppWarehouseListZoneTable/>
            </AppSuspense>
        </AppTab>
        <!--        <AppTab id="gui-start-volume" title="Volume" icon="ruler-vertical" tabs="gui-start-bottom">-->
        <!--            <AppSuspense>-->
        <!--                <AppWarehouseListVolumeTable/>-->
        <!--            </AppSuspense>-->
        <!--        </AppTab>-->
    </AppTabs>
</template>

<style scoped>
    .active { position: relative; z-index: 0; overflow: scroll; max-height: 100%; min-width: 100vw; padding-bottom: 100px}
</style>
