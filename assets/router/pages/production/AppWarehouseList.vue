<script setup>
    import {defineEmits, defineProps} from 'vue'
    import {useRoute, useRouter} from 'vue-router'
    import {useTableMachine} from '../../../machine'
    import {useWarehouseListItemsStore} from '../../../stores/production/warehouseListItems'

    defineProps({
        fields: {default: () => [], type: Array},
        icon: {required: true, type: String},
        title: {required: true, type: String}
    })

    const route = useRoute()
    const router = useRouter()

    const options = [
        {text: 'MG2C', value: 'MG2C'},
        {text: 'TCONCEPT', value: 'TCONCEPT'},
        {text: 'TUNISIE CONCEPT', value: 'TUNISIECONCEPT'},
        {text: 'WHETEC', value: 'WHETEC'}
    ]
    const optionsFamille = [
        {text: 'prison', value: 'prison'},
        {text: 'production', value: 'production'},
        {text: 'camion', value: 'camion'},
        {text: 'expédition', value: 'expédition'}
    ]

    const formfields = [
        {label: 'Compagnie ', name: 'compagnie', options: {label: value => options.find(option => option.type === value)?.text ?? null, options}, type: 'select'},
        {label: 'Nom *', name: 'name', type: 'text'},
        {label: 'Famille ', name: 'famille', options: {label: value => optionsFamille.find(option => option.type === value)?.text ?? null, options: optionsFamille}, type: 'select'}
    ]
    const emit = defineEmits(['update'])
    async function update(item) {
        emit('update', item)
        await router.push({name: 'warehouse-show'})
    }
    const machine = useTableMachine(route.name)
    const storeWarehouseListItems = useWarehouseListItemsStore()
    storeWarehouseListItems.fetchItems()
</script>

<template>
    <h1>
        <Fa :icon="icon"/>
        {{ title }}
        <AppBtn variant="success" class="btnRight" data-bs-toggle="modal" data-bs-target="#split">
            créer
        </AppBtn>
        <AppBtn variant="secondary" class="btnRight">
            Flux d'entrepôts
        </AppBtn>
    </h1>
    <AppModal id="split" title="Créer un entrepot">
        <AppForm id="addEntrepots" :fields="formfields"/>
        <template #buttons>
            <AppBtn class="float-end" variant="success">
                créer
            </AppBtn>
        </template>
    </AppModal>
    <AppTable
        :id="route.name" :fields="fields" :store="storeWarehouseListItems" :machine="machine" @update="update"/>
</template>

<style scoped>
.btnRight {
  float: right;
  margin-right: 5px;
  margin-left: 5px;
}
</style>
