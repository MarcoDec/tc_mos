import {h, resolveComponent} from 'vue'
import AppTable from '../../components/table/AppTable'
import {generateTableFields} from '../../components/validators'
import {useRoute} from 'vue-router'

function AppTablePage(props) {
    return h(resolveComponent('AppOverlay'), {id: useRoute().name}, () => [
        h('div', {class: 'row'}, h('h1', {class: 'col'}, [
            h(resolveComponent('Fa'), {icon: props.icon}),
            h('span', {class: 'ms-2'}, props.title)
        ])),
        h(AppTable, {fields: props.fields})
    ])
}

AppTablePage.displayName = 'AppTablePage'
AppTablePage.props = {
    fields: generateTableFields(),
    icon: {required: true, type: String},
    title: {required: true, type: String}
}

export default AppTablePage
