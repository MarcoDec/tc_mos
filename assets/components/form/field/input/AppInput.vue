<script setup>
    import {computed} from 'vue'

    const emit = defineEmits(['update:modelValue'])
    const props = defineProps({
        disabled: {type: Boolean},
        field: {required: true, type: Object},
        form: {required: true, type: String},
        id: {required: true, type: String},
        modelValue: {default: ''}
    })
    const theValue = computed(() => {
        if (typeof props.modelValue === 'boolean') {
            console.warn('AppInput.vue entrée booléenne détectée, remplacement valeur par chaine texte vide')
            return ''
        }
        return props.modelValue
    })
    const type = computed(() => props.field.type ?? 'text')
    const multiple = computed(() => props.field.multiple ?? true)
    function input(e) {
        emit('update:modelValue', e.target.value)
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
        @input="input"/>
</template>
