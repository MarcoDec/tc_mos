import {h, resolveComponent} from 'vue'

function AppRouterLinkJS(props, context) {
    return h(
        resolveComponent('RouterLink'),
        {custom: true, to: {name: props.to}},
        {
            default({navigate: onClick}) {
                const attrs = {class: 'pointer', onClick}
                if (props.css)
                    attrs['class'] += ` ${props.css}`
                return h('span', attrs, context.slots['default']())
            }
        }
    )
}

AppRouterLinkJS.props = {css: {default: null, type: String}, to: {required: true, type: String}}

export default AppRouterLinkJS
