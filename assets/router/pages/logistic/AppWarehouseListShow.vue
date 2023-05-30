<script setup>
    import {computed, ref} from 'vue'
    import {useTableMachine} from '../../../machine'
    import {useWarehouseStocksItemsStore} from '../../../stores/production/warehouseStocksItems'
    import AppWarehouseListStockTable from './provisoir/AppWarehouseListStockTable.vue'
    import AppWarehouseListVolumeTable from './provisoir/AppWarehouseListVolumeTable.vue'
    import {useRoute} from 'vue-router'
    const route = useRoute()
    const machine = useTableMachine(route.name)
    const guiRatio = ref(0.5)
    const guiRatioPercent = computed(() => `${guiRatio.value * 100}%`)

    const storeWarehouseStocksItems = useWarehouseStocksItemsStore()
    storeWarehouseStocksItems.fetchItems()

    const optionRef = [
        {text: 'CAB-1000', value: 100},
        {text: 'CAB-100', value: 10},
        {text: '1188481x', value: 1000},
        {text: '1188481', value: 10000},
        {text: 'ywx', value: 20}
    ]

    const Volumefields = [
        {
            create: true,
            filter: true,
            label: 'Ref',
            name: 'ref',
            options: {label: value => optionRef.find(option => option.value === value)?.text ?? null, options: optionRef},
            sort: true,
            type: 'select',
            update: true
        },
        {
            create: true,
            filter: true,
            label: 'Quantité ',
            name: 'quantite',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: true,
            filter: true,
            label: 'Type',
            name: 'type',
            sort: true,
            type: 'text',
            update: true
        }
    ]
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
                <AppTableJS :id="route.name" :fields="Volumefields" :store="storeWarehouseStocksItems" items="Volumeitems" :machine="machine"/>
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
