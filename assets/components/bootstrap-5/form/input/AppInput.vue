<script setup>
    import {computed} from 'vue'

    const emit = defineEmits(['update:modelValue'])
    const props = defineProps({
        field: {required: true, type: Object},
        id: {required: true, type: String},
        modelValue: {default: null},
        size: {default: 'sm', type: String}
    })
    const sizeClass = computed(() => `form-control-${props.size}`)
    const type = computed(() => props.field.type ?? 'text')

    function input(e) {
        emit('update:modelValue', e.target.value)
    }
</script>

<template>
    <input
        :id="id"
        :class="sizeClass"
        :name="field.name"
        :placeholder="field.label"
        :type="type"
        :value="modelValue"
        autocomplete="off"
        class="form-control"
        @input="input"/>
</template>
