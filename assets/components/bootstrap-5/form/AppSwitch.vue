<script setup>
    import {computed} from 'vue'

    const emit = defineEmits(['update:modelValue'])
    const props = defineProps({
        field: {required: true, type: Object},
        modelValue: {required: false, type: Boolean},
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
            :id="field.id"
            :checked="modelValue"
            :name="field.name"
            class="form-check-input"
            type="checkbox"
            @input="input"/>
        <label v-if="!noLabel" :for="field.id" class="form-check-label">{{ field.label }}</label>
    </div>
</template>
