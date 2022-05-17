import {h, resolveComponent} from 'vue'
import {useRoute} from 'vue-router'

function AppCollectionTablePage(props) {
    return h(resolveComponent('AppOverlay'), {id: useRoute().name}, () => [
        h('div', {class: 'row'}, h('h1', {class: 'col'}, [
            h(resolveComponent('Fa'), {icon: props.icon}),
            h('span', {class: 'ms-2'}, props.title)
        ]))
    ])
}

AppCollectionTablePage.displayName = 'AppCollectionTablePage'
AppCollectionTablePage.props = {icon: {required: true, type: String}, title: {required: true, type: String}}

export default AppCollectionTablePage
