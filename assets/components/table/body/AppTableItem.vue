<script setup>
    import AppTableItemField from './AppTableItemField.vue'
    import {computed} from 'vue'

    const props = defineProps({
        fields: {required: true, type: Array},
        index: {required: true, type: Number},
        item: {required: true, type: Object},
        send: {required: true, type: Function}
    })
    const normalizedIndex = computed(() => props.index + 1)

    async function remove() {
        props.send('submit')
        await props.item.remove()
        props.send('success')
    }
</script>

<template>
    <tr>
        <td>
            <AppBtn icon="trash" label="Supprimer" variant="danger" @click="remove"/>
        </td>
        <td>{{ normalizedIndex }}</td>
        <AppTableItemField v-for="field in fields" :key="field.name" :field="field" :item="item"/>
    </tr>
</template>
