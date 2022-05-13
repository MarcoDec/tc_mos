import AppOption from './AppOption'
import {generateField} from '../../../../validators'
import {h} from 'vue'

function AppSelect(props) {
    return h(
        'select',
        {
            class: 'form-select form-select-sm',
            disabled: props.disabled,
            form: props.form,
            id: props.id,
            name: props.field.name
        },
        props.field.options.map(option => h(AppOption, {key: option.value, option}))
    )
}

AppSelect.props = {
    disabled: {type: Boolean},
    field: generateField(),
    form: {required: true, type: String},
    id: {required: true, type: String}
}

export default AppSelect
