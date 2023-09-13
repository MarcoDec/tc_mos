import {computed, h, ref, resolveComponent, watch} from 'vue'
import {generateTableField} from '../../../props'
import {get} from '../../../../utils'

function AppTableItemUpdateFieldJS(props, context) {
    const originalValue = computed(() => get(props.item, props.field.name))
    const value = ref(originalValue.value)
    const violation = computed(() => props.machine.state.value.context.violations.find(v => v.propertyPath === props.field.name))
    function input(newValue) {
        value.value = newValue
    }
    watch(originalValue, input)
    return () => {
        const slot = context.slots['default']
        return h(
            resolveComponent('AppTableFormFieldJS'),
            {
                field: props.field,
                form: props.form,
                id: props.id,
                item: props.item,
                machine: props.machine,
                modelValue: value.value,
                'onUpdate:modelValue': input,
                violation: violation.value
            },
            typeof slot === 'function' ? args => slot(args) : null
        )
    }
}
AppTableItemUpdateFieldJS.props = {
    field: generateTableField(),
    form: {required: true, type: String},
    id: {required: true, type: String},
    item: {required: true, type: Object},
    machine: {required: true, type: Object}
}
export default AppTableItemUpdateFieldJS
