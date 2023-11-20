<script setup>
    import {cloneDeep, get, set} from 'lodash'
    import {computed, onMounted, onUnmounted, readonly, ref, shallowRef, toRefs} from 'vue'
    import {Tooltip} from 'bootstrap'

    const el = ref()
    const emit = defineEmits(['update:modelValue'])
    const {field, form, initialField, label, mode, modelValue, store, violations} = toRefs(defineProps({
        field: {required: true, type: Object},
        form: {required: true, type: String},
        initialField: {required: true, type: Object},
        label: {default: 'rechercher', type: String},
        mode: {default: null, type: String},
        modelValue: {default: () => ({}), type: Object},
        store: {required: true, type: Object},
        violations: {default: () => [], type: Array}
    }))
    const tooltip = shallowRef(null)
    const tooltipIgnore = readonly(['boolean', 'color', 'multiselect', 'select'])
    const hasContent = computed(() => mode.value === null || field.value[mode.value])
    const hasTooltip = computed(() => !tooltipIgnore.includes(field.value.type))
    const tip = computed(() => `<i class="enter-key-icon"></i> pour ${label.value}`)
    const inputId = computed(() => `${form.value}-${field.value.name}`)
    const value = computed(() => {
        if (field.value.type === 'select') {
            //console.log(props.field.name, props.modelValue[props.field.name])
            const res = field.value.options.options.find(e => {
                const iri = e.value ? e.value : e['@id']
                const searchedIri = typeof get(modelValue.value, field.value.name) === 'object' ? get(modelValue.value, field.value.name)['@id'] : get(modelValue.value, field.value.name)
                return iri === searchedIri
            })
            //console.log('res', res)
            if (typeof res === 'undefined') return get(modelValue.value, field.value.name)
            if (typeof res.value === 'undefined') {
                //console.log('res.value not defined', res['@id'])
                return res['@id']
            }
            //console.log('res.value defined', res.value)
            return res.value
        }
        return get(modelValue.value, field.value.name)
    })
    const violation = computed(() => violations.value.find(v => v.propertyPath === field.value.name)?.message)
    const hasViolation = computed(() => Boolean(violation.value))
    const css = computed(() => ({'is-invalid': hasViolation.value}))

    const localField = ref(field.value)
    if (props.initialField.type === 'multiselect-fetch') {
        localField.value = {...localField.value, ...initialField.value}
        console.log('localField', localField.value)
    }
    function dispose() {
        if (tooltip.value !== null) {
            tooltip.value.dispose()
            tooltip.value = null
        }
    }

    function input(v) {
        const cloned = set(cloneDeep(modelValue.value), field.value.name, v)
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
