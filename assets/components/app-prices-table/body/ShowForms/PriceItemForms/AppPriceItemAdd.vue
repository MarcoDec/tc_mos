<script setup>
import {defineProps, defineEmits, reactive} from 'vue'

const props = defineProps({
    defaultAddFormValues: {required: true, type: Object},
    fields: {required: true, type: Array},
    form: {required: true, type: String}
})
// console.log('defaultAddFormValues', props.defaultAddFormValues)
const emit = defineEmits(['addItem'])

const formData = reactive({})
//On initialise formData avec les valeurs contenues dans defaultAddFormValues
for (const field in props.defaultAddFormValues) {
    formData[field] = props.defaultAddFormValues[field]
}

function onUpdateModelValue(event, fieldName) {
    console.log('onUpdateModelValue', event, fieldName)
    if (typeof event === 'object' && event !== null) {
        formData[fieldName] = {...formData[fieldName], ...event}
    } else {
        formData[fieldName] = event
    }
    console.log('formData', formData)
}
function addItem() {
    console.log('addItem', formData)
    emit('addItem', formData)
}
</script>

<template>
    <tr class="text-center">
        <td>
            <button class="btn btn-icon btn-success btn-sm mx-2" @click="addItem">
                <Fa icon="plus"/>
            </button>
        </td>
        <template v-for="field in fields" :key="field.name">
            <td v-if="field.type !== null && field.name !== 'prices' ">
                <AppInputGuesser
                    :id="field.name"
                    :disabled="field.readonly"
                    :form="form"
                    :field="field"
                    :model-value="defaultAddFormValues[field.name]"
                    no-label
                    @update:model-value="onUpdateModelValue($event, field.name)"/>
            </td>
            <!--            <td v-else colspan="4"/>-->
        </template>
    </tr>
</template>

<style scoped>
td {
    font-size: xx-small !important;
}
</style>