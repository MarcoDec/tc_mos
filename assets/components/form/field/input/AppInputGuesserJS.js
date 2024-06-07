import {h, resolveComponent} from 'vue'
import AppInputFile from './AppInputFile'
import AppInputMeasure from './AppInputMeasure.vue'
import AppInputMeasureSelect from './AppInputMeasureSelect.vue'
import AppInputNumber from './AppInputNumber.vue'
import AppMultiselect from './select/AppMultiselect.vue'
import AppRating from './AppRating.vue'
import AppMultiselectFetch from './select/AppMultiselectFetch.vue'
import AppSelect from './select/AppSelect.vue'
import AppSwitch from './AppSwitch.vue'
import AppTextArea from './AppTextArea'
import {generateField} from '../../../props'

function getType(field) {
    switch (field.type) {
    case 'boolean':
        return AppSwitch
    case 'number' || 'int':
        return AppInputNumber
    case 'file':
        return AppInputFile
    case 'select':
        return AppSelect
    case 'multiselect':
        return AppMultiselect
    case 'multiselect-fetch':
        return AppMultiselectFetch
    case 'measure':
        return AppInputMeasure
    case 'measureSelect':
        return AppInputMeasureSelect
    case 'rating':
        return AppRating
    case 'textarea':
        return AppTextArea
    default:
        return resolveComponent('AppInputJS')
    }
}

function AppInputGuesserJS(props, context) {
    return h(getType(props.field), {
        disabled: props.disabled,
        field: props.field,
        focusedField: props.focusedField,
        form: props.form,
        id: props.id,
        modelValue: props.modelValue,
        'onUpdate:modelValue': value => context.emit('update:modelValue', value),
        onFocusin: value => {
            // console.log('AppInputGuesserJS', value)
            context.emit('on-focusin', value)
        }
    })
}

AppInputGuesserJS.emits = ['update:modelValue', 'on-focusin']
AppInputGuesserJS.props = {
    disabled: {type: Boolean},
    field: generateField(),
    focusedField: {required: true, type: Object},
    form: {required: true, type: String},
    id: {required: true, type: String},
    modelValue: {}
}

export default AppInputGuesserJS
