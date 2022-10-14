<script setup>
    const props = defineProps({
        fields: {required: true, type: Object},
        id: {required: true, type: String},
        send: {required: true, type: Function},
        store: {required: true, type: Object}
    })

    async function cancel() {
        props.send('submit')
        await props.store.cancel()
        props.send('success')
    }

    async function search() {
        props.send('submit')
        await props.store.fetch()
        props.send('success')
    }
</script>

<template>
    <AppTableHeaderForm
        :id="id"
        v-model="store.search"
        :can-reverse="fields.create"
        :fields="fields"
        :send="send"
        :submit="search"
        mode="search">
        <AppBtn icon="times" label="Annuler" variant="danger" @click="cancel"/>
    </AppTableHeaderForm>
</template>
