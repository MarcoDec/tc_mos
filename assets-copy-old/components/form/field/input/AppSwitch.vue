<script setup>
    import {ref, watch} from 'vue'
    import {generateField} from '../../../props'

    const emit = defineEmits(['update:modelValue'])
    const props = defineProps({
        disabled: {type: Boolean},
        field: generateField(),
        form: {required: false, type: String},
        id: {required: false, type: String},
        modelValue: {type: Boolean}
    })
    const checked = ref(props.modelValue)

    function input(e) {
        emit('update:modelValue', checked.value = e.target.checked)
    }

    watch(() => props.modelValue, value => {
        checked.value = value
    })
</script>

<template>
    <div class="form-check form-switch">
        <input :id="id" :checked="checked" :disabled="disabled" class="form-check-input" type="checkbox" @input="input"/>
        <input :disabled="disabled" :form="form" :name="field.name" :value="checked" type="hidden"/>
    </div>
</template>
