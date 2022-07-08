import {actionsItem, gettersItem} from '../tables/item'
import {defineStore} from 'pinia'

export default function generateComponent(component, root) {
    return defineStore(`components/${component.id}`, {
        actions: {
            ...actionsItem,
            dispose() {
                this.$reset()
                this.$dispose()
            }
        },
        getters: {
            ...gettersItem,
            option: state => ({text: state.nom, value: state.nom})
        },
        state: () => ({root, ...component})
    })()
}
