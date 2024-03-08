<script setup>
    import {computed, defineEmits, defineProps} from 'vue'
    import {generateField} from '../../../props'

    const emit = defineEmits(['update:modelValue'])

    const props = defineProps({
        field: generateField(),
        modelValue: {type: Boolean}
    })

    const options = computed(() => props.field.options ?? [])

    function input(value) {
        emit('update:modelValue', value)
    }
</script>

<template>
    <div class="btn-group">
        <AppRadio
            v-for="option in options"
            :id="field.id"
            :key="option.value"
            :field="field"
            :model-value="modelValue"
            :option="option"
            @update:model-value="input"/>
    </div>
</template>
