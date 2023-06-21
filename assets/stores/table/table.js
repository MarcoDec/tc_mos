import api from '../../Api'
import {defineStore} from 'pinia'
import flat from 'flat'
import useRow from './row'

export default function useTable(id) {
    return defineStore(id, {
        actions: {
            async cancel() {
                this.id = id
                this.search = {}
                await this.fetch()
            },
            async create() {
                const response = await api(this.url, 'POST', this.createBody)
                this.resetItems()
                this.rows.push(useRow(response, this))
            },
            dispose() {
                for (const row of this.rows)
                    row.dispose()
                this.$dispose()
            },
            async fetch() {
                const response = await api(this.url + this.readFilter, 'GET', this.fetchBody)
                this.resetItems()
                for (const row of response['hydra:member'])
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
            baseUrl() {
                return this.url
            },
            fetchBody() {
                return {...this.orderBody, ...this.flatSearch}
            },
            flatSearch: state => flat(state.search),
            isSorter: state => field => field.name === state.sorted,
            order() {
                return `${this.orderParam}ending`
            },
            orderBody(state) {
                return state.sortName === null ? {} : {[`order[${state.sortName}]`]: this.orderParam}
            },
            orderParam: state => (state.asc ? 'asc' : 'desc'),
            url: state => `/api/${state.apiBaseRoute}`
        },
        state: () => ({
            apiBaseRoute: '',
            asc: true,
            createBody: {},
            id,
            readFilter: '',
            rows: [],
            search: {},
            sortName: null,
            sorted: null
        })
    })()
}
