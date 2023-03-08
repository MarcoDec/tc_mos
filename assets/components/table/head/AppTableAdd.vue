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
        } catch (violations) {
            props.machine.send('fail', {violations})
        }
    }
</script>

<template>
    <AppTableHeaderForm
        :id="id"
        v-model="store.createBody"
        :fields="fields"
        :send="machine.send"
        :store="store"
        :submit="create"
        :violations="machine.state.value.context.violations"
        can-reverse
        class="table-success"
        icon="plus"
        label="Ajouter"
        mode="create"
        reverse-icon="search"
        reverse-label="recherche"
        reverse-mode="search"
        variant="success">
        <template #form="args">
            <slot name="btn" v-bind="args"/>
        </template>
        <template v-for="f in fields.fields" :key="f.name" #[f.name]="args">
            <slot :name="f.name" v-bind="args"/>
        </template>
    </AppTableHeaderForm>
</template>
