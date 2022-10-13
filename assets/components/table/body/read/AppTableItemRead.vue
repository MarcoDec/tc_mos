<script setup>
    import {computed} from 'vue'

    const props = defineProps({
        fields: {required: true, type: Object},
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

    function update() {
        props.send('update', {updated: props.item['@id']})
    }
</script>

<template>
    <tr>
        <td class="text-center">
            <AppBtn icon="pencil-alt" label="Modifier" @click="update"/>
            <AppBtn icon="trash" label="Supprimer" variant="danger" @click="remove"/>
        </td>
        <td class="text-center">
            {{ normalizedIndex }}
        </td>
        <AppTableItemField v-for="field in fields" :key="field.name" :field="field" :item="item"/>
    </tr>
</template>
