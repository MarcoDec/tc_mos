import {computed, h} from 'vue'

export default {
    displayName: 'AppRadio',
    emits: ['update:modelValue'],
    name: 'AppRadio',
    props: {
        field: {required: true, type: Object},
        id: {required: true, type: String},
        modelValue: {type: [Boolean, Number, String]},
        option: {required: true, type: Object},
        size: {default: 'sm', type: String}
    },
    setup(props, {emit}) {
        const btn = computed(() => Boolean(props.field.btn))
        const checked = computed(() => props.modelValue === props.option.value)
        const inputClass = computed(() => ({
            [`btn-${props.size}`]: true,
            'btn-check': btn.value,
            'form-check-input': !btn.value
        }))
        const inputId = computed(() => `${props.id}-${String(props.option.value)}`)
        const label = computed(() => ({
            btn: btn.value,
            'btn-outline-primary': btn.value,
            'form-check-label': !btn.value
        }))

        function input(e) {
            emit('update:modelValue', e.target.value)
        }

        return () => {
            const radio = [
                h('input', {
                    autocomplete: 'off',
                    checked: checked.value,
                    class: inputClass.value,
                    id: inputId.value,
                    name: props.field.name,
                    onInput: input,
                    type: 'radio',
                    value: props.option.value
                }),
                h('label', {class: label.value, for: inputId.value}, props.option.text)
            ]
            return btn.value ? radio : h('div', {class: 'form-check'}, radio)
        }
    }
}
