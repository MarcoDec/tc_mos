<script setup>
    import {useRouter, useRoute} from 'vue-router'
    import AppSuspense from '../../../AppSuspense.vue'
    import AppShowGuiGen from '../../AppShowGuiGen.vue'
    import AppWarehouseFormShow from './AppWarehouseFormShow.vue'
    import {useWarehouseListStore} from './provisoir/warehouseList'
    import AppWarehouseListShow from './AppWarehouseListShow.vue'

    defineProps({
        icon: {required: true, type: String},
        title: {required: true, type: String}
    })
    const route = useRoute()
    const idWarehouse = Number(route.params.id_warehouse)

    const storeWarehouse = useWarehouseListStore()
    storeWarehouse.fetchOne(idWarehouse)

    const router = useRouter()
    function goBack() {
        router.push({name: 'warehouse-list'})
    }
</script>

<template>
    <AppSuspense>
        <AppShowGuiGen>
            <template #gui-header>
                <div class="bg-white">
                    <h1>
                        <button class="text-dark" @click="goBack">
                            <Fa :icon="icon"/>
                        </button>
                        {{ title }} - {{ storeWarehouse.warehouse.name }}
                    </h1>
                </div>
            </template>
            <template #gui-left>
                <AppSuspense><AppWarehouseFormShow/></AppSuspense>
            </template>
            <template #gui-right>
                <!--            {{ route.params.id_employee }}-->
            </template>
            <template #gui-bottom>
                <AppSuspense><AppWarehouseListShow/></AppSuspense>
            </template>
        </AppShowGuiGen>
    </AppSuspense>
</template>
