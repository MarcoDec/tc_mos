<<<<<<< HEAD
import type {VNode} from '@vue/runtime-core'
import {h} from 'vue'

export default function AppHome(): VNode {
    return h('div')
}
=======
import type {VNode} from 'vue'
import {h} from 'vue'

function AppHome(): VNode {
    return h('div')
}

AppHome.displayName = 'AppHome'

export default AppHome
>>>>>>> develop
