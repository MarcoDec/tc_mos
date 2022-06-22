import {h, resolveComponent} from 'vue'
import AppTablePage from '../AppTablePage'
import {useRoute} from 'vue-router'

function AppTablePageColor(props) {
    return h(AppTablePage, props, {
        'cell(rgb)': ({field, item, value}) => h('span', {class: 'd-flex'}, [
            h('span', {class: 'me-2'}, value),
            h(resolveComponent('AppInputGuesser'), {
                disabled: true,
                field,
                form: 'none',
                id: `${useRoute().name}-${item.id}-${field.name}`,
                modelValue: value
            })
        ])
    })
}

AppTablePageColor.displayName = 'AppTablePageColor'
AppTablePageColor.props = {
    fields: {required: true, type: Object},
    icon: {required: true, type: String},
    title: {required: true, type: String}
}

export default AppTablePageColor
