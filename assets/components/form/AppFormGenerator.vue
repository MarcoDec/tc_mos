<script setup>
    import {reactive, ref, watch, toRefs} from 'vue'
    import AppFormGroup from './field/AppFormGroup.vue'
    import {cloneDeep} from 'lodash'

    const emit = defineEmits(['submit'])
    const form = ref()
    const {disabled, fields, id, modelValue, submitLabel, violations} = toRefs(defineProps({
        disabled: {type: Boolean},
        fields: {required: true, type: Object},
        id: {required: true, type: String},
        modelValue: {default: () => ({}), type: Object},
        submitLabel: {default: 'Modifier', type: String},
        violations: {default: () => [], type: Array}
    }))
    const value = reactive(cloneDeep(modelValue.value))

    function input(field, v) {
        value[field.name] = v
    }

    function submit() {
        let normalizedValue = null
        if (fields.value.file)
            normalizedValue = new FormData(form.value.$el)
        else {
            normalizedValue = {}
            for (const field of fields.value.fields)
                normalizedValue[field.name] = value[field.name]
        }
        emit('submit', normalizedValue)
    }

    watch(() => modelValue.value, newValue => {
        const keys = Object.keys(value)
        for (const key of keys)
            delete value[key]
        for (const [key, v] of Object.entries(newValue))
            value[key] = v
    })
</script>

<template>
    <AppForm :id="id" ref="form" @submit="submit">
        <AppFormGroup
            v-for="field in fields.fields"
            :key="field.name"
            :disabled="disabled"
            :field="field"
            :form="id"
            :model-value="value"
            :violations="violations"
            @input="input"/>
        <div class="row">
            <div class="col d-inline-flex justify-content-end">
                <slot :disabled="disabled" :label="submitLabel" type="submit">
                    <AppBtn :disabled="disabled" :label="submitLabel" type="submit"/>
                </slot>
            </div>
        </div>
    </AppForm>
</template>
