<script setup>
    import AppTableItemUpdateField from './AppTableItemUpdateField.vue'
    import {computed} from 'vue'

    const props = defineProps({
        fields: {required: true, type: Object},
        id: {required: true, type: String},
        index: {required: true, type: Number},
        item: {required: true, type: Object},
        machine: {required: true, type: Object}
    })
    const form = computed(() => `${props.id}-form`)
    const normalizedIndex = computed(() => props.index + 1)

    props.item.initUpdate(props.fields)

    function cancel() {
        props.machine.send('search')
    }

    async function update() {
        props.machine.send('submit')
        try {
            await props.item.update()
            props.machine.send('success')
            props.machine.send('search')
        } catch (e) {
            if (e.status === 422)
                props.machine.send('fail', {violations: e.content})
        }
    }
</script>

<template>
    <tr :id="id" class="table-success">
        <td class="text-center">
            <AppForm :id="form" class="d-inline m-0 p-0" @submit="update">
                <AppBtn icon="check" label="Modifier" type="submit" variant="success"/>
            </AppForm>
            <AppBtn icon="times" label="Annuler" variant="danger" @click="cancel"/>
        </td>
        <td class="text-center">
            {{ normalizedIndex }}
        </td>
        <AppTableItemUpdateField
            v-for="field in fields"
            :key="field.name"
            v-model="item.updated"
            :field="field"
            :form="form"
            :item="item"
            :violations="machine.state.value.context.violations"/>
    </tr>
</template>
