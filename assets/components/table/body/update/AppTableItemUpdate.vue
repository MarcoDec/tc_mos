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
        } catch (violations) {
            props.machine.send('fail', {violations})
        }
    }
    const machineViolations = computed(() => props.machine.state.value.context.violations)
    const fieldViolations = computed(() => Array.isArray(machineViolations.value) ? machineViolations.value : [])
    const isMachineViolationGlobalError = computed(() => !Array.isArray(machineViolations.value))
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
            v-for="(field, i) in fields.fields"
            :key="field.name"
            v-model="item.updated"
            :field="field"
            :initial-field="fields.initialFields[i]"
            :form="form"
            :item="item"
            :row="id"
            :violations="fieldViolations"/>
    </tr>
    <tr v-if="isMachineViolationGlobalError">
        <td :colspan="fields.fields.length + 2" class="bg-warning text-center">
            <strong>{{ machineViolations }}</strong> - <small class="bg-white text-danger">Please take a screenshot of the page and send it to your IT administrator.</small>
        </td>
    </tr>
</template>
