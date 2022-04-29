<script setup>
    import {computed, inject} from 'vue'
    import AppCollectionTableSearchField from './AppCollectionTableSearchField.vue'
    import {CollectionRepository} from '../../../store/modules'
    import {useRepo} from '../../../composition'

    const props = defineProps({coll: {required: true, type: Object}})
    const emit = defineEmits(['search', 'toggle'])
    const create = inject('create', false)
    const fields = inject('fields', [])
    const tableId = inject('table-id', 'table')
    const form = computed(() => `${tableId}-search`)
    const repo = useRepo(CollectionRepository)

    function search() {
        emit('search')
    }

    function reset() {
        repo.resetSearch(props.coll)
        search()
    }

    function toggle() {
        emit('toggle')
    }
</script>

<template>
    <tr class="text-center">
        <td>
            <Fa icon="filter"/>
        </td>
        <td>
            <AppBtn v-if="create" icon="plus-circle" title="Basculer en mode ajout" variant="success" @click="toggle"/>
            <AppForm :id="form" :fields="fields" inline @submit="search">
                <AppBtn icon="search" title="Rechercher" type="submit" variant="secondary"/>
            </AppForm>
            <AppBtn icon="times" title="Annuler" variant="danger" @click="reset"/>
        </td>
        <AppCollectionTableSearchField
            v-for="field in fields"
            :key="field.name"
            :coll="coll"
            :field="field"
            :form="form"/>
    </tr>
</template>
