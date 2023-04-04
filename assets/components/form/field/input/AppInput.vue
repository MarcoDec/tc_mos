<script setup>
    import {computed} from 'vue'

    const emit = defineEmits(['update:modelValue'])
    const props = defineProps({
        disabled: {type: Boolean},
        field: {required: true, type: Object},
        form: {required: true, type: String},
        id: {required: true, type: String},
        modelValue: {default: '', type: [Number, String]}
    })
    const type = computed(() => props.field.type ?? 'text')

    function input(e) {
        console.log('eeeeeeeeeeee', e.target.value)
        emit('update:modelValue', e.target.value)
    }
</script>

<template>
    <input
        :id="id"
        :disabled="disabled"
        :form="form"
        :name="field.name"
        :placeholder="field.label"
        :type="type"
        :value="modelValue"
        autocomplete="off"
        class="form-control form-control-sm"
        @input="input"/>
</template>
