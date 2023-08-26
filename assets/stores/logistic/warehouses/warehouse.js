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
            getFamilies: state => state.families.toString(),
            row: state => ({
                delete: false,
                famille: state.family,
                id: 1,
                name: state.name,
                update: false,
                update2: true
            })
        },
        state: () => ({...warehouse})
    })()
}
