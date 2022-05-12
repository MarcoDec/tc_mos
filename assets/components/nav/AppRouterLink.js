import {h, resolveComponent} from 'vue'

function AppRouterLink(props, context) {
    return h(
        resolveComponent('RouterLink'),
        {custom: true, to: {name: props.to}},
        {
            default({navigate: onClick}) {
                const attrs = {class: 'router-link', onClick}
                if (props.css)
                    attrs['class'] += ` ${props.css}`
                return h('span', attrs, context.slots['default']())
            }
        }
    )
}

AppRouterLink.props = {css: {default: null, type: String}, to: {required: true, type: String}}

export default AppRouterLink
