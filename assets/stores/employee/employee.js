import {actionsItem, gettersItem} from '../tables/item'
import {defineStore} from 'pinia'

export default function generateEmployee(employee, root) {
    return defineStore(`customers/${employee.id}`, {
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
        state: () => ({root, ...employee})
    })()
}
