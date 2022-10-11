import api from '../../api'
import {defineStore} from 'pinia'

export default function useRow(row, table) {
    let initialState = {...row}
    return defineStore(`${table.id}/${row.id}`, {
        actions: {
            dispose() {
                this.table.removeRow(this)
                this.$reset()
                this.$dispose()
            },
            initUpdate(fields) {
                this.updated = {}
                for (const field of fields)
                    this.updated[field.name] = this[field.name]
            },
            async remove() {
                await api(this.url, 'DELETE')
                this.dispose()
            },
            async update() {
                const response = await api(this.url, 'PATCH', this.updated)
                initialState = response.content
                this.$reset()
            }
        },
        getters: {
            url: state => `${state.table.url}/${state.id}`
        },
        state: () => ({...initialState, table, updated: {}})
    })()
}
