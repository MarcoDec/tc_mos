import {actionsItem, gettersItem} from '../tables/item'
import {defineStore} from 'pinia'

export default function generateManufacturingOrders(order, root) {
    return defineStore(`manufacturingOrders/${order.id}`, {
        actions: {
            ...actionsItem,
            dispose() {
                this.$reset()
                this.$dispose()
            }
        },
        getters: {
            ...gettersItem,
            option: state => ({text: state.numero, value: state.numero})
        },
        state: () => ({root, ...order})
    })()
}
