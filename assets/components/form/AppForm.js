import AppFormGroup from './field/AppFormGroup'
import {fieldValidator} from '../validators'
import {h} from 'vue'

function AppForm(props) {
    const groups = []
    for (const field of props.fields)
        groups.push(h(AppFormGroup, {field, form: props.id, key: field.name}))
    groups.push(h(
        'div',
        {class: 'row'},
        h(
            'div',
            {class: 'col d-inline-flex justify-content-end'},
            h('button', {class: 'btn btn-primary', form: props.id, type: 'submit'}, 'Connexion')
        )
    ))
    return h('form', {autocomplete: 'off', id: props.id}, groups)
}

AppForm.props = {
    fields: {
        required: true,
        type: Array,
        validator(value) {
            if (value.length === 0)
                return false
            for (const field of value)
                if (!fieldValidator(field))
                    return false
            return true
        }
    },
    id: {required: true, type: String}
}

export default AppForm
