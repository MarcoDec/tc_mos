<script setup>
    const props = defineProps({
        fields: {required: true, type: Array},
        id: {required: true, type: String},
        send: {required: true, type: Function},
        store: {required: true, type: Object}
    })

    async function create() {
        props.send('submit')
        try {
            await props.store.create()
            props.send('success')
        } catch (e) {
            if (e.status === 422)
                props.send('fail', {violations: e.content})
        }
    }
</script>

<template>
    <AppTableHeaderForm
        :id="id"
        v-model="store.createBody"
        :fields="fields"
        :send="send"
        :submit="create"
        class="table-success"
        icon="plus"
        label="Ajouter"
        reverse-icon="search"
        reverse-label="recherche"
        reverse-mode="search"
        variant="success"/>
</template>
