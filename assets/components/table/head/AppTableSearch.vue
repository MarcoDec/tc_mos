<script setup>
    import AppTableFormField from '../AppTableFormField.vue'
    import {computed} from 'vue'

    const props = defineProps({
        fields: {required: true, type: Array},
        id: {required: true, type: String},
        send: {required: true, type: Function},
        store: {required: true, type: Object}
    })
    const form = computed(() => `${props.id}-form`)

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
    <tr :id="id">
        <td class="text-center">
            <Fa icon="search"/>
        </td>
        <td>
            <AppForm :id="form" class="d-inline m-0 p-0" @submit="search">
                <AppBtn icon="search" label="Rechercher" type="submit" variant="secondary"/>
            </AppForm>
            <AppBtn icon="times" label="Annuler" variant="danger" @click="cancel"/>
        </td>
        <AppTableFormField v-for="field in fields" :key="field.name" :field="field" :form="form" :store="store"/>
    </tr>
</template>
