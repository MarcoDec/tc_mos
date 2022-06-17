import type {BootstrapSize, FormField, FormOption, FormValue} from '../../../types/bootstrap-5'
import type {PropType, SetupContext, VNode} from 'vue'
import {computed, h} from 'vue'
import type {DeepReadonly} from '../../../types/types'

type Props = {
    field: FormField
    id: string
    modelValue?: FormValue
    option: FormOption
    size: BootstrapSize
}

export default {
    emits: ['update:modelValue'],
    props: {
        field: {required: true, type: Object as PropType<FormField>},
        id: {required: true, type: String},
        modelValue: {type: [Boolean, Number, String] as PropType<FormValue>},
        option: {required: true, type: Object as PropType<FormOption>},
        size: {default: 'sm', type: String as PropType<BootstrapSize>}
    },
    setup(props: DeepReadonly<Props>, {emit}: DeepReadonly<SetupContext>): () => VNode | VNode[] {
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

        function input(e: Readonly<InputEvent>): void {
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
