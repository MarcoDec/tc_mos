<script setup>
    import {computed, inject, reactive} from 'vue'

    const emit = defineEmits(['search', 'toggle'])
    const create = inject('create', false)
    const fields = inject('fields', [])
    const tableId = inject('table-id', 'table')
    const searchFields = computed(() => fields.map(field => ({
        ...field,
        id: `${tableId}-search-${field.name}`,
        type: field.type === 'boolean' ? 'search-boolean' : field.type
    })))

    const searchOptionsObject = {}
    for (const field of fields)
        if (field.filter)
            searchOptionsObject[field.name] = null
    const searchOptions = reactive({...searchOptionsObject})

    function search() {
        emit('search', searchOptions)
    }

    function reset() {
        for (const [key, value] of Object.entries(searchOptionsObject))
            searchOptions[key] = value
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
            <AppBtn icon="search" title="Rechercher" variant="secondary" @click="search"/>
            <AppBtn icon="times" title="Annuler" variant="danger" @click="reset"/>
        </td>
        <td v-for="field in searchFields" :key="field.name">
            <AppInputGuesser v-if="field.filter" v-model="searchOptions[field.name]" :field="field"/>
        </td>
    </tr>
</template>
