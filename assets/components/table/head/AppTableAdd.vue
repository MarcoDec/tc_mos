<script setup>
    const props = defineProps({
        fields: {required: true, type: Object},
        id: {required: true, type: String},
        machine: {required: true, type: Object},
        store: {required: true, type: Object}
    })

    async function create() {
        props.machine.send('submit')
        try {
            await props.store.create()
            props.machine.send('success')
        } catch (e) {
            if (e.status === 422)
                props.machine.send('fail', {violations: e.content})
        }
    }
</script>

<template>
    <AppTableHeaderForm
        :id="id"
        v-model="store.createBody"
        :fields="fields"
        :send="machine.send"
        :submit="create"
        :violations="machine.state.value.context.violations"
        class="table-success"
        icon="plus"
        label="Ajouter"
        mode="create"
        reverse-icon="search"
        reverse-label="recherche"
        reverse-mode="search"
        variant="success"/>
</template>
