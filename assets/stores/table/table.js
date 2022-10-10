import api from '../../api'
import {defineStore} from 'pinia'
import useRow from './row'

export default function useTable(id) {
    return defineStore(id, {
        actions: {
            async cancel() {
                this.search = {}
                await this.fetch()
            },
            dispose() {
                for (const row of this.rows)
                    row.dispose()
                this.$reset()
                this.$dispose()
            },
            async fetch() {
                const response = await api(this.url, 'GET', this.fetchBody)
                this.resetItems()
                for (const row of response.content['hydra:member'])
                    this.rows.push(useRow(row, this))
            },
            removeRow(removed) {
                this.rows = this.rows.filter(row => row.id !== removed.id)
            },
            resetItems() {
                const rows = [...this.rows]
                this.rows = []
                for (const row of rows)
                    row.dispose()
            },
            async sort(field) {
                if (this.sorted === field.name)
                    this.asc = !this.asc
                else {
                    this.asc = true
                    this.sorted = field.name
                    this.sortName = field.sortName ?? field.name
                }
                await this.fetch()
            }
        },
        getters: {
            ariaSort() {
                return field => (this.isSorter(field) ? this.order : 'none')
            },
            fetchBody() {
                return {...this.orderBody, ...this.search}
            },
            isSorter: state => field => field.name === state.sorted,
            order() {
                return `${this.orderParam}ending`
            },
            orderBody(state) {
                return state.sortName === null ? {} : {[`order[${state.sortName}]`]: this.orderParam}
            },
            orderParam: state => (state.asc ? 'asc' : 'desc'),
            url: state => `/api/${state.id}`
        },
        state: () => ({
            asc: true,
            id,
            rows: [],
            search: {},
            sortName: null,
            sorted: null
        })
    })()
}
