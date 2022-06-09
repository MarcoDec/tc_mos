import {h, resolveComponent} from 'vue'

function AppTabBtn(props) {
    let css = 'nav-link'
    if (props.tab.active)
        css += ' active'
    const children = [h(resolveComponent('Fa'), {class: 'me-1', icon: props.tab.icon})]
    if (!props.icon)
        children.push(props.tab.title)
    return h('li', {class: 'nav-item', role: 'presentation', title: props.tab.title}, h(
        'button',
        {
            ariaControls: props.tab.id,
            class: css,
            dataBsTarget: props.tab.target,
            dataBsToggle: 'tab',
            id: props.tab.labelledby,
            role: 'button',
            type: 'button'
        },
        children
    ))
}

AppTabBtn.props = {icon: {type: Boolean}, tab: {required: true, type: Object}}

export default AppTabBtn
