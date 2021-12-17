import type {FormField, FormOption, FormValue} from '../../../types/bootstrap-5'
import type {PropType, SetupContext, VNode} from 'vue'
import {computed, h} from 'vue'

type Props = {
    field: FormField
    id: string
    modelValue?: FormValue
    option: FormOption
}

export default {
    emits: ['update:modelValue'],
    props: {
        field: {required: true, type: Object as PropType<FormField>},
        id: {required: true, type: String},
        modelValue: {type: Object as PropType<FormValue>},
        option: {required: true, type: Object as PropType<FormOption>}
    },
    setup(props: Props, {emit}: SetupContext): () => VNode | VNode[] {
        const btn = computed(() => Boolean(props.field.btn))
        const checked = computed(() => props.modelValue === props.option.value)
        const inputClass = computed(() => ({
            'btn-check': btn.value,
            'form-check-input': !btn.value
        }))
        const inputId = computed(() => `${props.id}-${props.option.value}`)
        const label = computed(() => ({
            btn: btn.value,
            'btn-outline-primary': btn.value,
            'form-check-label': !btn.value
        }))

        function input(e: InputEvent): void {
            emit('update:modelValue', (e.target as HTMLInputElement).value)
        }

        return (): VNode | VNode[] => {
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
