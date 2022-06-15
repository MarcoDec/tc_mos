import {h, resolveComponent} from 'vue'
import AppDropdown from '../dropdown/AppDropdown.vue'
import {useSlots} from '../../../composition'

function AppNavbarItem(props, context) {
    return h(AppDropdown, {class: 'nav-item', id: props.id, tag: 'li'}, {
        default: () => useSlots(context),
        toggle: toggleProps => h(
            'a',
            {
                ariaExpanded: 'false',
                class: 'dropdown-toggle nav-link',
                'data-bs-toggle': 'dropdown',
                href: '#',
                id: toggleProps.id,
                role: 'button'
            },
            [
                h(resolveComponent('Fa'), {class: 'me-1', icon: props.icon}),
                props.title
            ]
        )
    })
}

AppNavbarItem.displayName = 'AppNavbarItem'
AppNavbarItem.props = {
    icon: {required: true, type: String},
    id: {required: true, type: String},
    title: {required: true, type: String}
}

export default AppNavbarItem
