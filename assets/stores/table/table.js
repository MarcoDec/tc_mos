import api from '../../api'
import {defineStore} from 'pinia'
import useRow from './row'

export default function useTable(id) {
    return defineStore(id, {
        actions: {
            dispose() {
                for (const row of this.rows)
                    row.dispose()
                this.$reset()
                this.$dispose()
            },
            async fetch() {
                const response = await api(this.url)
                for (const row of response.content['hydra:member'])
                    this.rows.push(useRow(row, this))
            },
            removeRow(removed) {
                this.rows = this.rows.filter(row => row.id !== removed.id)
            }
        },
        getters: {
            ariaSort() {
                return field => (this.isSorter(field) ? this.order : 'none')
            },
            isSorter: state => field => field.name === state.sorted,
            order: state => (state.asc ? 'ascending' : 'descending'),
            url: state => `/api/${state.id}`
        },
        state: () => ({
            asc: true,
            id,
            rows: [],
            sorted: null
        })
    })()
}
