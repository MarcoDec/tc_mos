<script setup>
    import {computed, inject} from 'vue'
    import AppCollectionTableCreateField from './AppCollectionTableCreateField.vue'

    defineProps({violations: {default: () => [], type: Array}})

    const emit = defineEmits(['create', 'toggle'])
    const fields = inject('fields', [])
    const tableId = inject('table-id', 'table')
    const formId = computed(() => `${tableId}-create`)

    function create(created) {
        emit('create', created)
    }

    function toggle() {
        emit('toggle')
    }
</script>

<template>
    <tr class="table-success text-center">
        <td>
            <Fa icon="plus-circle"/>
        </td>
        <td>
            <AppBtn icon="filter" title="Basculer en mode recherche" variant="secondary" @click="toggle"/>
            <AppForm :id="formId" inline @submit="create">
                <AppBtn icon="plus" title="Ajouter" type="submit" variant="success"/>
            </AppForm>
        </td>
        <AppCollectionTableCreateField
            v-for="field in fields"
            :key="field.name"
            :field="field"
            :form="formId"
            :violations="violations"/>
    </tr>
</template>
