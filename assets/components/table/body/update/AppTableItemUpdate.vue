<script setup>
    import AppTableItemUpdateField from './AppTableItemUpdateField.vue'
    import {computed, toRefs} from 'vue'

    const {fields, id, index, item, machine} = toRefs(defineProps({
        fields: {required: true, type: Object},
        id: {required: true, type: String},
        index: {required: true, type: Number},
        item: {required: true, type: Object},
        machine: {required: true, type: Object}
    }))
    const form = computed(() => `${id.value}-form`)
    const normalizedIndex = computed(() => index.value + 1)

    item.value.initUpdate(fields.value)

    function cancel() {
        machine.value.send('search')
    }

    async function update() {
        machine.value.send('submit')
        try {
            await item.value.update()
            machine.value.send('success')
            machine.value.send('search')
        } catch (violations) {
            machine.value.send('fail', {violations})
        }
    }
    const machineViolations = computed(() => machine.value.state.value.context.violations)
    const fieldViolations = computed(() => (Array.isArray(machineViolations.value) ? machineViolations.value : []))
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
