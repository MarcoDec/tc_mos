import api from '../../../api'
import {defineStore} from 'pinia'

export default function generateWarehouse(warehouse) {
    return defineStore(`warehouse/${warehouse.id}`, {
        actions: {
            dispose() {
                this.$reset()
                this.$dispose()
            },
            async remove() {
                await api(`/api/warehouses/${this.id}`, 'DELETE')
            }
        },
        getters: {
            getFamilies: state => state.families.toString()
        },
        state: () => ({...warehouse})
    })()
}
