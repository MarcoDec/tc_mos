import {actionsItem, gettersItem} from '../tables/item'
import {defineStore} from 'pinia'

export default function generateProduct(product, root) {
    return defineStore(`products/${product.id}`, {
        actions: {
            ...actionsItem,
            dispose() {
                this.$reset()
                this.$dispose()
            }
        },
        getters: {
            ...gettersItem,
            option: state => ({text: state.ref, value: state.ref})
        },
        state: () => ({root, ...product})
    })()
}
