<script setup>
    import AppLabel from './AppLabel.vue'
    import {computed} from 'vue'

    const emit = defineEmits(['input', 'update:modelValue'])
    const props = defineProps({
        disabled: {type: Boolean},
        field: {required: true, type: Object},
        form: {required: true, type: String},
        modelValue: {default: () => ({}), type: Object},
        violations: {default: () => [], type: Array}
    })
    const inputId = computed(() => `${props.form}-${props.field.name}`)
    const value = computed(() => props.modelValue[props.field.name])
    const violation = computed(() => props.violations.find(v => v.propertyPath === props.field.name)?.message)
    const hasViolation = computed(() => Boolean(violation.value))
    const css = computed(() => ({'is-invalid': hasViolation.value}))

    function input(v) {
        emit('input', props.field, v)
        emit('update:modelValue', {...props.modelValue, [props.field.name]: v})
    }
</script>

<template>
    <div class="mb-3 row">
        <AppLabel :field="field" :input="inputId"/>
        <div class="col">
            <AppInputGuesser
                :id="inputId"
                :class="css"
                :disabled="disabled"
                :field="field"
                :form="form"
                :model-value="value"
                @update:model-value="input"/>
            <div v-if="hasViolation" class="invalid-feedback">
                {{ violation }}
            </div> 
        </div>  
    </div>
</template>
