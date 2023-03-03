import {h} from 'vue'

function AppPaginationItem(props) {
    let css = 'page-item'
    if (props.index === props.store.current)
        css += ' active'
    return h('li', {class: css}, h('span', {class: 'page-link'}, props.index))
}

AppPaginationItem.props = {index: {required: true, type: Number}, store: {required: true, type: Object}}

export default AppPaginationItem
