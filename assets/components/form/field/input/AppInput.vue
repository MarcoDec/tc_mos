<script setup>
    import {computed} from 'vue'

    const emit = defineEmits(['update:modelValue', 'focusout'])
    const props = defineProps({
        disabled: {type: Boolean},
        field: {required: true, type: Object},
        form: {required: true, type: String},
        id: {required: true, type: String},
        modelValue: {default: ''}
    })
    const theValue = computed(() => {
        if (typeof props.modelValue === 'boolean') {
            return ''
        }
        return props.modelValue
    })
    const type = computed(() => props.field.type ?? 'text')
    const multiple = computed(() => props.field.multiple ?? true)
    function input(e) {
        emit('update:modelValue', e.target.value)
    }
    function change(e) {
        emit('update:modelValue', e.target.value)
    }
    function keyup(e) {
        e.preventDefault()
        e.stopPropagation()
    }
    function onFocusout() {
        emit('focusout', props.field.name)
    }
</script>

<template>
    <input
        :id="id"
        :disabled="disabled || field.disabled"
        :form="form"
        :name="field.name"
        :multiple="multiple"
        :placeholder="field.label"
        :readonly="field.readonly"
        :type="type"
        :value="theValue"
        autocomplete="off"
        :step="field.step ? field.step : .01"
        class="form-control form-control-sm"
        @focusout="onFocusout"
        @blur="change"
        @change="change"
        @keyup.enter="keyup"
        @input="input"/>
</template>
