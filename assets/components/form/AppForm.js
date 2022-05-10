import {h, resolveComponent} from 'vue'
import AppFormGroup from './field/AppFormGroup'
import {fieldValidator} from '../validators'

function AppForm(props, context) {
    const groups = []
    for (const field of props.fields)
        groups.push(h(AppFormGroup, {disabled: props.disabled, field, form: props.id, key: field.name}))
    groups.push(h(
        'div',
        {class: 'row'},
        h(
            'div',
            {class: 'col d-inline-flex justify-content-end'},
            h(
                resolveComponent('AppBtn'),
                {disabled: props.disabled, form: props.id, type: 'submit'},
                () => 'Connexion'
            )
        )
    ))
    return h('form', {
        autocomplete: 'off',
        enctype: 'multipart/form-data',
        id: props.id,
        method: 'POST',
        onSubmit(e) {
            e.preventDefault()
            context.emit('submit', new FormData(e.target))
        }
    }, groups)
}

AppForm.emits = ['submit']
AppForm.props = {
    disabled: {type: Boolean},
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
