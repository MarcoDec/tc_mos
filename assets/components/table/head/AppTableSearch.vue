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
        await props.store.fetchOne()
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
        :store="store"
        :submit="search"
        mode="search">
        <template v-for="f in fields.fields" :key="f.name" #[f.name]="args">
            <slot :name="f.name" v-bind="args"/>
        </template>
        <AppBtn icon="times" label="Annuler" variant="danger" @click="cancel"/>
    </AppTableHeaderForm>
</template>
