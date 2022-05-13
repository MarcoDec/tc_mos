import AppInputGuesser from './input/AppInputGuesser'
import AppLabel from './AppLabel'
import {generateField} from '../../validators'
import {h} from 'vue'

function AppFormGroup(props, context) {
    const id = `${props.form}-${props.field.name}`
    return h('div', {class: 'row mb-3'}, [
        h(AppLabel, {field: props.field, for: id}),
        h(
            'div',
            {class: 'col'},
            h(AppInputGuesser, {
                disabled: props.disabled,
                field: props.field,
                form: props.form,
                id,
                modelValue: props.modelValue,
                'onUpdate:modelValue': value => context.emit('update:modelValue', value)
            })
        )
    ])
}

AppFormGroup.emits = ['update:modelValue']
AppFormGroup.props = {
    disabled: {type: Boolean},
    field: generateField(),
    form: {required: true, type: String},
    modelValue: {}
}

export default AppFormGroup
