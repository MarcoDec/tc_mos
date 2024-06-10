<script setup>
    import {computed} from 'vue'

    const emit = defineEmits(['update:modelValue', 'onFocusin'])
    const props = defineProps({
        disabled: {type: Boolean},
        field: {required: true, type: Object},
        focusedField: {required: true, type: Object},
        form: {required: true, type: String},
        id: {required: true, type: String},
        modelValue: {default: ''}
    })
    console.log('AppInput.vue props.focusedField', props.focusedField)
    const theValue = computed(() => {
        if (typeof props.modelValue === 'boolean') {
            // console.warn('AppInput.vue entrée booléenne détectée, remplacement valeur par chaine texte vide', props.field)
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
        // console.log('change', e.target.value, e.relatedTarget, e.key)
        emit('update:modelValue', e.target.value)
    }
    function keyup(e) {
        e.preventDefault()
        e.stopPropagation()
        // console.log(`keyup ${props.id}`, e.target.value)
    }
    function onFocusin() {
        //On récupère la référence au champ actif
        const $refs = document.getElementById(props.id)
        // console.log('AppInput:onFocusin', $refs)
        emit('onFocusin', $refs)
    }
    function onFocusout() {
        // console.log('onFocusout')
        emit('onFocusin', null)
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
        @focusin="onFocusin"
        @focusout="onFocusout"
        @blur="change"
        @change="change"
        @keyup.enter="keyup"
        @input="input"/>
</template>
