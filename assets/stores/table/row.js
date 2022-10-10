import {defineStore} from 'pinia'

export default function useRow(row, table) {
    return defineStore(`${table.id}/${row.id}`, {
        actions: {
            dispose() {
                this.table.removeRow(this)
                this.$reset()
                this.$dispose()
            }
        },
        state: () => ({...row, table})
    })()
}
