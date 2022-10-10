import api from '../../api'
import {defineStore} from 'pinia'

export default function useRow(row, table) {
    return defineStore(`${table.id}/${row.id}`, {
        actions: {
            dispose() {
                this.table.removeRow(this)
                this.$reset()
                this.$dispose()
            },
            async remove() {
                await api(this.url, 'DELETE')
                this.dispose()
            }
        },
        getters: {
            url: state => `${state.table.url}/${state.id}`
        },
        state: () => ({...row, table})
    })()
}
