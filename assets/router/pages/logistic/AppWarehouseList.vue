<script setup>
    import {defineEmits, defineProps} from 'vue'
    import {useRoute, useRouter} from 'vue-router'
    import AppBtnJS from '../../../components/AppBtnJS'
    import AppFormJS from '../../../components/form/AppFormJS'
    import AppModal from '../../../components/modal/AppModal.vue'
    import AppTableJS from '../../../components/table/AppTableJS'
    import Fa from '../../../components/Fa'
    import {useTableMachine} from '../../../machine'
    import {useWarehouseListItemsStore} from '../../../stores/logistic/warehouses/warehouseListItems'

    const props = defineProps({
        fields: {default: () => [], type: Array},
        icon: {required: true, type: String},
        title: {required: true, type: String}
    })
    console.debug(props.fields, props.icon, props.title)
    const route = useRoute()
    const router = useRouter()

    const formfields = [
        {label: 'Nom *', name: 'name', type: 'text'},
        {label: 'Famille ', name: 'getFamilies()', type: 'text'}
    ]
    const emit = defineEmits(['update'])
    async function update(item) {
        emit('update', item)
        await router.push({name: 'warehouse-show'})
    }
    const machine = useTableMachine(route.name)
    const storeWarehouseListItems = useWarehouseListItemsStore()
    storeWarehouseListItems.fetchOne()
</script>

<template>
    <h1>
        <Fa :icon="icon"/>
        {{ title }}
        <AppBtnJS variant="success" class="btnRight" data-bs-toggle="modal" data-bs-target="#split">
            créer
        </AppBtnJS>
        <AppBtnJS variant="secondary" class="btnRight">
            Flux d'entrepôts
        </AppBtnJS>
    </h1>
    <AppModal id="split" title="Créer un entrepot">
        <AppFormJS id="addEntrepots" :fields="formfields"/>
        <template #buttons>
            <AppBtnJS class="float-end" variant="success">
                créer
            </AppBtnJS>
        </template>
    </AppModal>
    <AppTableJS
        :id="route.name" :fields="fields" :store="storeWarehouseListItems" :machine="machine" @update="update"/>
</template>

<style scoped>
    .btnRight {
      float: right;
      margin-right: 5px;
      margin-left: 5px;
    }
</style>
