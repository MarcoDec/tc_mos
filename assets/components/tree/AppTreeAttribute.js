import {h, resolveComponent} from 'vue'

function AppTreeAttribute(props) {
    return h(resolveComponent('AppFormGroup'), {
        field: {label: props.attribute.name, name: props.attribute['@id'], type: 'boolean'},
        form: props.form,
        id: `${props.form}-${props.attribute.id}`,
        key: `${props.family.id}-${props.attribute.id}`,
        labelCols: 'col-sm-8',
        modelValue: props.attribute.includes(props.family)
    })
}

AppTreeAttribute.props = {
    attribute: {required: true, type: Object},
    family: {required: true, type: Object},
    form: {required: true, type: String}
}

export default AppTreeAttribute
