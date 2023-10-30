<script setup>
    import {cloneDeep, get, set} from 'lodash'
    import {computed, onMounted, onUnmounted, readonly, ref, shallowRef} from 'vue'
    import {Tooltip} from 'bootstrap'

    const el = ref()
    const emit = defineEmits(['update:modelValue'])
    const props = defineProps({
        field: {required: true, type: Object},
        form: {required: true, type: String},
        initialField: {required: true, type: Object},
        label: {default: 'rechercher', type: String},
        mode: {default: null, type: String},
        modelValue: {default: () => ({}), type: Object},
        store: {required: true, type: Object},
        violations: {default: () => [], type: Array}
    })
    const tooltip = shallowRef(null)
    const tooltipIgnore = readonly(['boolean', 'color', 'multiselect', 'select'])
    const hasContent = computed(() => props.mode === null || props.field[props.mode])
    const hasTooltip = computed(() => !tooltipIgnore.includes(props.field.type))
    const tip = computed(() => `<i class="enter-key-icon"></i> pour ${props.label}`)
    const inputId = computed(() => `${props.form}-${props.field.name}`)
    const value = computed(() => get(props.modelValue, props.field.name))
    const violation = computed(() => props.violations.find(v => v.propertyPath === props.field.name)?.message)
    const hasViolation = computed(() => Boolean(violation.value))
    const css = computed(() => ({'is-invalid': hasViolation.value}))

    const localField = ref(props.field)
    if (props.initialField.type === 'multiselect-fetch') {
        localField.value = {...localField.value, ...props.initialField}
        console.log('localField', localField.value)
    }
    function dispose() {
        if (tooltip.value !== null) {
            tooltip.value.dispose()
            tooltip.value = null
        }
    }

    function input(v) {
        const cloned = set(cloneDeep(props.modelValue), props.field.name, v)
        emit('update:modelValue', cloned)
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
            <slot :id="inputId" :css="css" :field="localField" :form="form" :store="store">
                <AppInputGuesser
                    :id="inputId"
                    ref="el"
                    :class="css"
                    :field="localField"
                    :form="form"
                    :model-value="value"
                    :title="tip"
                    data-bs-html="true"
                    data-bs-placement="top"
                    data-bs-toogle="tooltip"
                    data-bs-trigger="hover"
                    @update:model-value="input"/>
            </slot>
            <div v-if="hasViolation" class="invalid-feedback">
                {{ violation }}
            </div>
        </template>
    </td>
</template>
