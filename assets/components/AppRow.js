import {h} from 'vue'

function AppRow(props, context) {
    return h('div', {...props, class: 'row'}, context.slots['default']())
}

AppRow.displayName = 'AppRow'

export default AppRow
