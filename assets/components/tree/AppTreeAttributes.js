import {h, resolveComponent} from 'vue'
import AppTreeAttribute from './AppTreeAttribute'

function AppTreeAttributes(props) {
    return h('div', {class: 'col tree-card-attributes'}, [
        h(
            'form',
            {
                class: 'd-inline-flex justify-content-end mb-2 w-100',
                id: props.form,
                async onSubmit(e) {
                    e.preventDefault()
                    props.machine.send('submit')
                    const attributes = []
                    for (const [attribute, checked] of new FormData(e.target))
                        if (checked === 'true')
                            attributes.push(attribute)
                    const data = new FormData()
                    data.append('attributes', JSON.stringify(attributes))
                    const updated = await props.family.updateAttributes(data)
                    props.attributes.update(updated, props.family)
                    props.machine.send('success')
                }
            },
            h(resolveComponent('AppBtn'), {type: 'submit'}, () => 'Enregistrer')
        ),
        h(
            'div',
            {class: 'h-100 overflow-auto'},
            props.attributes.items.map(attribute => h(
                AppTreeAttribute,
                {attribute, family: props.family, form: props.form, key: attribute.id}
            ))
        )
    ])
}

AppTreeAttributes.props = {
    attributes: {required: true, type: Object},
    family: {required: true, type: Object},
    form: {required: true, type: String},
    machine: {required: true, type: Object}
}

export default AppTreeAttributes
