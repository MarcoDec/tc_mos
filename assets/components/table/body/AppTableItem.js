import AppTableItemField from './AppTableItemField'
import {generateTableFields} from '../../validators'
import {h} from 'vue'

function AppTableItem(props, context) {
    return h('tr', [
        h('td', {class: 'text-center'}, props.index + 1),
        h('td', {class: 'text-center'}),
        props.fields.map(field => {
            const slot = context.slots[`cell(${field.name})`]
            return h(
                AppTableItemField,
                {field, item: props.item, key: field.name},
                typeof slot === 'function' ? {default: args => slot(args)} : null
            )
        })
    ])
}

AppTableItem.props = {
    fields: generateTableFields(),
    index: {required: true, type: Number},
    item: {required: true, type: Object}
}

export default AppTableItem
