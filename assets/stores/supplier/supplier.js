import {actionsItem, gettersItem} from '../tables/item'
import {defineStore} from 'pinia'

export default function generateSupplier(supplier, root) {
    return defineStore(`suppliers/${supplier.id}`, {
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
        state: () => ({root, ...supplier})
    })()
}
