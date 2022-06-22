import {h, resolveComponent} from 'vue'

function AppCurrencySearch(props) {
    let css = 'd-flex pe-2 ps-2'
    if (props.field.type !== 'boolean')
        css += ' w-100'
    return h('div', {class: css}, [
        h('label', {class: 'col-form-label me-2', for: props.id}, props.field.label),
        h(resolveComponent('AppInputGuesser'), {
            field: props.field,
            form: 'currencies-search',
            id: props.id,
            modelValue: props.store[props.field.name],
            'onUpdate:modelValue'(value) {
                props.store[props.field.name] = value
            }
        })
    ])
}

AppCurrencySearch.props = {
    field: {required: true, type: Object},
    id: {required: true, type: String},
    store: {required: true, type: Object}
}

export default AppCurrencySearch
