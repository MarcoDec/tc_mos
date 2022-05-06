<script setup>
    import {computed} from 'vue'

    const emit = defineEmits(['update:modelValue'])
    const props = defineProps({
        disabled: {type: Boolean},
        field: {required: true, type: Object},
        id: {required: true, type: String},
        modelValue: {type: Boolean},
        noLabel: {required: false, type: Boolean}
    })
    const checkClass = computed(() => ({'form-check': !props.noLabel}))

    function input(e) {
        emit('update:modelValue', e.target.value)
    }
</script>

<template>
    <div :class="checkClass" class="form-switch">
        <input
            :id="id"
            :checked="modelValue"
            :disabled="disabled"
            :name="field.name"
            class="form-check-input"
            type="checkbox"
            @input="input"/>
        <label v-if="!noLabel" :for="id" class="form-check-label">{{ field.label }}</label>
    </div>
</template>
