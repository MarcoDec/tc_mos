import {h, resolveComponent} from 'vue'
import AppTree from '../../components/tree/AppTree.vue'

function AppTreePage(props) {
    return [
        h(
            resolveComponent('AppRow'),
            () => h('h1', {class: 'col'}, [
                h(resolveComponent('Fa'), {class: 'me-3', icon: 'layer-group'}),
                props.title
            ])
        ),
        h(AppTree, {fields: props.fields, repo: props.repo})
    ]
}

AppTreePage.displayName = 'AppTreePage'
AppTreePage.props = {
    fields: {required: true, type: Array},
    repo: {required: true, type: Function},
    title: {required: true, type: String}
}

export default AppTreePage
