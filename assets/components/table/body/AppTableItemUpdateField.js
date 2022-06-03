import {computed, h, resolveComponent} from 'vue'
import {generateTableField} from '../../props'
import {get} from '../../../utils'

export default {
    props: {
        field: generateTableField(),
        form: {required: true, type: String},
        id: {required: true, type: String},
        item: {required: true, type: Object},
        machine: {required: true, type: Object}
    },
    setup(props, context) {
        const value = computed(() => get(props.item, props.field.name))
        const violations = computed(() => props.machine.state.value.context.violations.find(violation => violation.propertyPath === props.field.name))
        return () => {
            const slot = context.slots['default']
            return h(
                resolveComponent('AppTableFormField'),
                {
                    field: props.field,
                    form: props.form,
                    id: props.id,
                    item: props.item,
                    machine: props.machine,
                    modelValue: value.value,
                    violations: violations.value
                },
                typeof slot === 'function' ? args => slot(args) : null
            )
        }
    }
}
