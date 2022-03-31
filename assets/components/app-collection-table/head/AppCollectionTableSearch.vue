<script setup>
    import {computed, inject} from 'vue'

    const emit = defineEmits(['toggle'])
    const create = inject('create', false)
    const fields = inject('fields', [])
    const tableId = inject('table-id', 'table')
    const searchFields = computed(() => fields.map(field => ({
        ...field,
        id: `${tableId}-search-${field.name}`,
        type: field.type === 'boolean' ? 'search-boolean' : field.type
    })))

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
            <AppBtn v-if="create" icon="plus-circle" variant="success" @click="toggle"/>
            <AppBtn icon="search" variant="secondary"/>
            <AppBtn icon="times" variant="danger"/>
        </td>
        <td v-for="field in searchFields" :key="field.name">
            <AppInputGuesser v-if="field.filter" :field="field"/>
        </td>
    </tr>
</template>
