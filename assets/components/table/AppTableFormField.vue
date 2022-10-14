<script setup>
    import {cloneDeep, get, set} from 'lodash'
    import {computed, onMounted, onUnmounted, readonly, ref, shallowRef} from 'vue'
    import {Tooltip} from 'bootstrap'

    const el = ref()
    const emit = defineEmits(['update:modelValue'])
    const props = defineProps({
        field: {required: true, type: Object},
        form: {required: true, type: String},
        label: {default: 'rechercher', type: String},
        mode: {default: null, type: String},
        modelValue: {default: () => ({}), type: Object},
        violations: {default: () => [], type: Array}
    })
    const tooltip = shallowRef(null)
    const tooltipIgnore = readonly(['boolean', 'color', 'select'])
    const hasContent = computed(() => props.mode === null || props.field[props.mode])
    const hasTooltip = computed(() => !tooltipIgnore.includes(props.field.type))
    const tip = computed(() => `<i class="enter-key-icon"></i> pour ${props.label}`)
    const inputId = computed(() => `${props.form}-${props.field.name}`)
    const value = computed(() => get(props.modelValue, props.field.name))
    const violation = computed(() => props.violations.find(v => v.propertyPath === props.field.name)?.message)
    const hasViolation = computed(() => Boolean(violation.value))
    const css = computed(() => ({'is-invalid': hasViolation.value}))

    function dispose() {
        if (tooltip.value !== null) {
            tooltip.value.dispose()
            tooltip.value = null
        }
    }

    function input(v) {
        emit('update:modelValue', set(cloneDeep(props.modelValue), props.field.name, v))
    }

    function instantiate() {
        if (typeof el.value === 'undefined')
            return
        dispose()
        if (hasTooltip.value)
            tooltip.value = new Tooltip(el.value.$el)
    }

    onMounted(instantiate)
    onUnmounted(dispose)
</script>

<template>
    <td>
        <template v-if="hasContent">
            <AppInputGuesser
                :id="inputId"
                ref="el"
                :class="css"
                :field="field"
                :form="form"
                :model-value="value"
                :title="tip"
                data-bs-html="true"
                data-bs-placement="top"
                data-bs-toogle="tooltip"
                @update:model-value="input"/>
            <div v-if="hasViolation" class="invalid-feedback">
                {{ violation }}
            </div>
        </template>
    </td>
</template>
