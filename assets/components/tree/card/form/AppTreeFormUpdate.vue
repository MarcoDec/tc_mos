<script setup>
    const props = defineProps({
        fields: {required: true, type: Object},
        machine: {required: true, type: Object},
        tree: {required: true, type: Object}
    })
    props.tree.selected.initUpdate(props.fields)

    async function submit(data) {
        props.machine.send('submit')
        try {
            await props.tree.selected.update(data)
            props.tree.selected.initUpdate(props.fields)
            props.machine.send('success')
        } catch (violations) {
            console.error(violations)
            // props.machine.send('fail', {violations})
        }
    }
</script>

<template>
    <AppTreeForm
        :fields="fields"
        :machine="machine"
        :model-value="props.tree.selected.updated"
        submit-label="Modifier"
        @submit="submit"/>
</template>
