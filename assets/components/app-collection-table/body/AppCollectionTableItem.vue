<script setup>
    import {computed, inject, onMounted, onUnmounted} from 'vue'
    import AppCollectionTableItemField from './AppCollectionTableItemField.vue'
    import AppCollectionTableItemInput from './AppCollectionTableItemInput.vue'
    import emitter from '../../../emitter'
    import {useRoute} from 'vue-router'

    const props = defineProps({
        index: {required: true, type: Number},
        item: {required: true, type: Object},
        updated: {required: true, type: Number},
        violations: {default: () => [], type: Array}
    })
    const fields = inject('fields', [])
    const route = useRoute()
    const tableId = inject('table-id', 'table')

    const formId = computed(() => `${tableId}-create`)
    const formattedIndex = computed(() => props.index + 1)
    const show = computed(() => props.updated !== props.index)
    const td = computed(() => (show.value ? AppCollectionTableItemField : AppCollectionTableItemInput))
    const emit = defineEmits(['show', 'toggle', 'update'])

    function showHandler() {
        emit('show', props.item)
    }

    function toggle() {
        emit('toggle', props.index)
    }

    function update(item) {
        emit('update', item)
    }

    onMounted(() => {
        emitter.on(`${route.name}-update-${props.item.id}`, toggle)
    })

    onUnmounted(() => {
        emitter.off(`${route.name}-update-${props.item.id}`, toggle)
    })
</script>

<template>
    <tr>
        <td class="text-center">
            {{ formattedIndex }}
        </td>
        <td v-if="show" class="text-center">
            <AppBtn v-if="item.update" icon="pencil-alt" title="Modifier" variant="primary" @click="toggle"/>
            <AppBtn v-if="item.show" icon="eye" title="Voir" variant="secondary" @click="showHandler"/>
            <AppBtn v-if="item['delete']" icon="trash" title="Supprimer" variant="danger"/>
        </td>
        <td v-else class="text-center">
            <AppForm :id="formId" inline @submit="update">
                <input :value="item.id" name="id" type="hidden"/>
                <AppBtn icon="check" title="Modifier" type="submit" variant="success"/>
            </AppForm>
            <AppBtn icon="times" title="Annuler" variant="danger" @click="toggle"/>
        </td>
        <component
            :is="td"
            v-for="field in fields"
            :key="field.name"
            :field="field"
            :form="formId"
            :item="item"
            :violations="violations"/>
    </tr>
</template>
