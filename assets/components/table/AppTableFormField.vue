<script setup>
    import {computed, onMounted, onUnmounted, ref, shallowRef} from 'vue'
    import {Tooltip} from 'bootstrap'

    const el = ref()
    const emit = defineEmits(['update:modelValue'])
    const props = defineProps({
        field: {required: true, type: Object},
        form: {required: true, type: String},
        label: {default: 'rechercher', type: String},
        modelValue: {default: () => ({}), type: Object}
    })
    const tip = computed(() => `<i class="enter-key-icon"></i> pour ${props.label}`)
    const tooltip = shallowRef(null)
    const inputId = computed(() => `${props.form}-${props.field.name}`)
    const value = computed(() => props.modelValue[props.field.name])

    function dispose() {
        if (tooltip.value !== null) {
            tooltip.value.dispose()
            tooltip.value = null
        }
    }

    function input(v) {
        emit('update:modelValue', {...props.modelValue, [props.field.name]: v})
    }

    function instantiate() {
        if (typeof el.value === 'undefined')
            return
        dispose()
        tooltip.value = new Tooltip(el.value)
    }

    onMounted(instantiate)
    onUnmounted(dispose)
</script>

<template>
    <td>
        <AppInputGuesser
            :id="inputId"
            ref="el"
            :field="field"
            :form="form"
            :model-value="value"
            :title="tip"
            data-bs-html="true"
            data-bs-placement="top"
            data-bs-toogle="tooltip"
            @update:model-value="input"/>
    </td>
</template>
