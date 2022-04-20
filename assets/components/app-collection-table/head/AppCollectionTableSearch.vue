<script setup>
    import {computed, inject} from 'vue'
    import AppCollectionTableSearchField from './AppCollectionTableSearchField.vue'

    defineProps({coll: {required: true, type: Object}})
    const emit = defineEmits(['search', 'toggle'])
    const create = inject('create', false)
    const fields = inject('fields', [])
    const tableId = inject('table-id', 'table')
    const form = computed(() => `${tableId}-search`)
    const searchFields = computed(() => fields.map(field => ({
        ...field,
        id: `${form.value}-${field.name}`,
        type: field.type === 'boolean' ? 'search-boolean' : field.type
    })))

    function search() {
        emit('search')
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
            <AppForm :id="form" inline @submit="search">
                <AppBtn icon="search" title="Rechercher" type="submit" variant="secondary"/>
            </AppForm>
            <AppBtn icon="times" title="Annuler" variant="danger"/>
        </td>
        <AppCollectionTableSearchField
            v-for="field in searchFields"
            :key="field.name"
            :coll="coll"
            :field="field"
            :form="form"/>
    </tr>
</template>
