<script lang="ts" setup>
    import type {Actions, Getters} from '../../../store/warehouseListItems'
    import type {TableField, TableItem} from '../../../types/app-collection-table'
    import {defineEmits, defineProps, onMounted} from 'vue'
    import {useNamespacedActions, useNamespacedGetters} from 'vuex-composition-helpers'
    import {useRoute, useRouter} from 'vue-router'
    import type {FormField} from '../../../types/bootstrap-5'

    defineProps<{fields: TableField[], icon: string, title: string}>()

    const route = useRoute()
    const router = useRouter()

    const formfields: FormField[] = [
        {label: 'Compagnie ', name: 'compagnie', options: [{text: 'MG2C', value: 'MG2C'}, {text: 'TCONCEPT', value: 'TCONCEPT'}, {text: 'TUNISIE CONCEPT', value: 'TUNISIECONCEPT'}, {text: 'WHETEC', value: 'WHETEC'}], type: 'select'},
        {label: 'Nom *', name: 'name', type: 'text'},
        {label: 'Famille ', name: 'famille', options: [{text: 'prison', value: 'prison'}, {text: 'production', value: 'production'}, {text: 'camion', value: 'camion'}, {text: 'expédition', value: 'expédition'}], type: 'select'}
    ]
    const emit = defineEmits<(e: 'update', item: TableItem) => void>()
    async function update(item: TableItem): Promise<void>{
        emit('update', item)
        await router.push({name: 'warehouse-show'})
    }

    const fetchItem = useNamespacedActions<Actions>('warehouseListItems', ['fetchItem']).fetchItem
    const {items} = useNamespacedGetters<Getters>('warehouseListItems', ['items'])
    onMounted(async () => {
        await fetchItem()
    })
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
        <AppForm :fields="formfields"/>
        <template #buttons>
            <AppBtn class="float-end" variant="success">
                créer
            </AppBtn>
        </template>
    </AppModal>
    <AppCollectionTable :id="route.name" :fields="fields" :items="items" pagination @update="update">
        <template #btn>
            <AppBtn variant="info" class="btnRight">
                Export Excel
            </AppBtn>
        </template>
    </AppCollectionTable>
</template>
