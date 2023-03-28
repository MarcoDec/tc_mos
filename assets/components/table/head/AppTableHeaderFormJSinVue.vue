<script setup>
    import {generateTableFields, generateVariant} from '../../props'
    import AppBtnJS from '../../AppBtnJS'
    import AppFormJS from '../../form/AppFormJS'
    import AppTableFormField from '../../table/AppTableFormField.vue'
    import Fa from '../../Fa'

    const props = defineProps({
        fields: generateTableFields(),
        icon: {required: true, type: String},
        id: {required: true, type: String},
        label: {required: true, type: String},
        machine: {required: true, type: Object},
        modelValue: {default: () => ({}), type: Object},
        reverseIcon: {required: true, type: String},
        reverseLabel: {required: true, type: String},
        reverseMode: {required: true, type: String},
        store: {required: true, type: Object},
        submit: {required: true, type: Function},
        submitVariant: generateVariant('secondary'),
        type: {required: true, type: String},
        variant: generateVariant('dark'),
        violations: {default: () => [], type: Array}})

    const emit = defineEmits(['inputValue'])

    async function onSubmit(data) {
        props.machine.send('submit')
        await props.submit(data)
        props.machine.send('success')
    }
    function onUpdate(value) {
        emit('inputValue', {field, value})
    }
</script>

<template>
    <tr
        :id="id"
        :class="`table-${variant} text-center`">
        <td>
            <Fa :icon="icon"/>
            <AppBtnJS
                :icon="reverseIcon"
                :title="`Basculer en mode ${reverseLabel}`"
                variant="primary"
                @click="machine.send(reverseMode)"/>
        </td>
        <td>
            <AppFormJS
                :id="`${id}-form`"
                :fields="fields"
                :inline="true"
                :machine="machine"
                :no-content="true"
                :store="store"
                :submit-label="label"
                :variant="submitVariant"
                @submit="onSubmit">
                <AppBtnJS
                    disabled="true"
                    form="form"
                    icon=""
                    title=""
                    type=""
                    variant=""/>
            </AppFormJS>
        </td>
        <AppTableFormField
            v-for="(field, index) in fields"
            :id=" `${id}-${field.name}`"
            :key="`${id}-${field.name}-${index}`"
            :field="field"
            :form="`${id}-form`"
            :machine="machine"
            :model-value="modelValue[field.name]"
            :violation="violations.find(violation => violation.propertyPath === field.name)"
            :store="store"
            @update:model-value="onUpdate">
            <slot :field="field"/>
        </AppTableFormField>
    </tr>
</template>
