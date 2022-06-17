import {h, resolveComponent} from 'vue'

function AppCurrency(props) {
    return h('div', {class: '"border border-dark col d-flex flex-column m-1'}, [
        h('div', {class: 'd-flex'}, [
            h(resolveComponent('AppInputGuesser'), {
                field: {label: 'Active', name: 'active', type: 'boolean'},
                form: 'none',
                id: `${props.currency.code?.toLowerCase()}-active`,
                modelValue: props.currency.active,
                'onUpdate:modelValue': async active => {
                    await props.store.update(props.currency, active)
                }
            }),
            props.currency.code
        ]),
        h('span', `1 € = ${props.currency.base} ${props.currency.symbol}`),
        h('span', props.currency.name)
    ])
}

AppCurrency.props = {currency: {required: true, type: Object}, store: {required: true, type: Object}}

export default AppCurrency
