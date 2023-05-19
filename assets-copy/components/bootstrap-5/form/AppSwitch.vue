<script lang="ts" setup>
    import type {FormField, FormValue} from '../../../types/bootstrap-5'
    import {computed, defineEmits, defineProps} from 'vue'
    import type {PropType} from 'vue'

    const emit = defineEmits<(e: 'update:modelValue', value: FormValue) => void>()
    const props = defineProps({
        disabled: {type: Boolean},
        field: {required: true, type: Object as PropType<FormField>},
        modelValue: {default: null, type: [Boolean, Number, String] as PropType<FormValue>},
        noLabel: {required: false, type: Boolean}
    })
    const checkClass = computed(() => ({'form-check': !props.noLabel}))

    function input(e: Readonly<Event>): void {
        emit('update:modelValue', (e.target as HTMLInputElement).value)
    }
</script>

<template>
    <div :class="checkClass" class="form-switch">
        <input
            :id="field.id"
            :name="field.name"
            :value="modelValue"
            class="form-check-input"
            type="checkbox"
            :disabled="disabled"
            @input="input"/>
        <label v-if="!noLabel" :for="field.id" class="form-check-label"/>
    </div>
</template>
