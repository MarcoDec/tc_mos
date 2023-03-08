import {computed, h, ref, resolveComponent, watch} from 'vue'
import {tableLoading} from '../../machine'

export default {
    emits: ['update:modelValue'],
    props: {
        disabled: {type: Boolean},
        field: {required: true, type: Object},
        form: {required: true, type: String},
        id: {required: true, type: String},
        machine: {required: true, type: Object},
        modelValue: {},
        violation: {default: null, type: Object}
    },
    setup(props, context) {
        function input(newValue) {
            context.emit('update:modelValue', value.value = newValue)
        }

        const value = ref(props.modelValue)
        const attrs = computed(() => {
            const inputAttrs = {
                disabled: props.disabled || tableLoading.some(props.machine.state.value.matches),
                field: props.field,
                form: props.form,
                id: `${props.id}-input`,
                modelValue: value.value,
                'onUpdate:modelValue': input
            }
            if (props.violation)
                inputAttrs['class'] = 'is-invalid'
            return inputAttrs
        })

        watch(() => props.modelValue, newValue => {
            value.value = newValue
        })

        return () => {
            function inputSlot(inputAttrs) {
                return typeof context.slots['default'] === 'function'
                    ? context.slots['default'](inputAttrs)
                    : h(resolveComponent('AppInputGuesser'), inputAttrs)
            }
            const children = []
            if (props.violation) {
                children.push(inputSlot(attrs.value))
                children.push(h('div', {class: 'invalid-feedback'}, props.violation.message))
            } else
                children.push(inputSlot(attrs.value))
            return h('td', {id: props.id}, children)
        }
    }
}
