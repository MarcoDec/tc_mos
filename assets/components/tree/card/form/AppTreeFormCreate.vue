<script setup>
    const props = defineProps({
        fields: {required: true, type: Object},
        machine: {required: true, type: Object},
        tree: {required: true, type: Object}
    })

    async function submit(data) {
        props.machine.send('submit')
        try {
            await props.tree.create(data)
            props.machine.send('success')
        } catch (violations) {
            props.machine.send('fail', {violations})
        }
    }
</script>

<template>
    <AppTreeForm :fields="fields" :machine="machine" submit-label="Créer" @submit="submit"/>
</template>
