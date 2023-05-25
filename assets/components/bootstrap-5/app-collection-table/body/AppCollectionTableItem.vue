<script setup>
    const props = defineProps({
        fields: {required: true, type: Array},
        item: {required: true, type: Object}
    })

    const emit = defineEmits(['deleted', 'ajoute'])
    function ajoute() {
        emit('ajoute')
    }
    function deleted() {
        const id = Number(props.item['@id'].match(/\d+/)[0])
        emit('deleted', id)
    }
</script>

<template>
    <td>
        <AppBtn icon="pencil-alt" label="Modifier" @click="ajoute"/>
        <AppBtn icon="trash" label="Supprimer" variant="danger" @click="deleted"/>
    </td>
    <td v-for="field in fields" :key="field.name">
        {{ item[field.name] }}
    </td>
</template>
