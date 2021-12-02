import {h, resolveComponent} from 'vue'
import type {VNode} from 'vue'

function AppSupplierShow(): VNode {
    return h(resolveComponent('AppShowGui'))
}

AppSupplierShow.displayName = 'AppSupplierShow'

export default AppSupplierShow
