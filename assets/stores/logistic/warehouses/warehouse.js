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
                //this.warehouse.delete = true
            }
        },
        getters: {
            getFamilies: state => state.families.toString(),
            row: state => ({
                delete: false,
                famille: state.warehouse.family,
                id: 1,
                name: state.warehouse.name,
                update: false,
                update2: true
            })
        },
        state: () => ({...warehouse})
    })()
}

// export default function generateWarehouse(warehouse) {
//     return defineStore(`warehouse/${warehouse.id}`, () => {
//         const state = ref({...warehouse})
//         console.debug('state', state.value)
//         function setDelete() {
//             state.value['delete'] = true
//         }
//         async function remove() {
//             if (state.value['delete']) {
//                 window.alert(`Element déjà supprimé\n${state.value.toString()}`)
//             } else {
//                 await api(`/api/warehouses/${state.value.id}`, 'DELETE')
//                 setDelete()
//             }
//         }
//         function getFamilies() {
//             return state.value.families.toString()
//         }
//         return {getFamilies, remove, state}
//     })
// }
