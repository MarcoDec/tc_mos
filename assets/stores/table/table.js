import api from '../../api'
import {defineStore} from 'pinia'
import flat from 'flat'
import useRow from './row'

export default function useTable(id) {
    return defineStore(`table${id}`, {
        actions: {
            async cancel() {
                this.id = id
                this.search = {}
                await this.fetch()
            },
            async create() {
                const fieldType = this.getApiTypedRoutes.field
                if (fieldType) {
                    const fieldValue = this.createBody[fieldType]
                    const urls = this.getApiTypedRoutes.routes
                    const urlLength = urls.length
                    let url = ''
                    for (let i = 0; i < urlLength; i++) {
                        const theValue = urls[i].valeur
                        if (theValue === fieldValue) {
                            url = urls[i].url
                        }
                    }
                    const response = await api(url, 'POST', this.createBody)
                    this.resetItems()
                    this.rows.push(useRow(response, this))
                } else {
                    const response = await api(this.url, 'POST', this.createBody)
                    this.resetItems()
                    this.rows.push(useRow(response, this))
                }
            },
            dispose() {
                for (const row of this.rows)
                    row.dispose()
                this.$dispose()
            },
            async fetch() {
                if (this.readFilterPrevious !== this.readFilter) {
                    this.page = 1
                }
                if (this.readFilter === '') this.readFilter = `?page=${this.page}`
                else if (this.readFilter.includes('page=')) {
                    const previousPage = this.readFilter.match(/page=\d+/)[0]
                    this.readFilter = this.readFilter.replace(previousPage, `page=${this.page}`)
                } else {
                    this.readFilter = `&page=${this.page}`
                }
                this.readFilterPrevious = this.readFilter
                const response = await api(this.url + this.readFilter, 'GET', this.fetchBody)
                this.resetItems()
                for (const row of response['hydra:member'])
                    this.rows.push(useRow(row, this))
                this.totalItems = response['hydra:totalItems']
                this.perPage = response['hydra:member'].length
                this.hydraId = response['hydra:view']['@id']
                this.hydraFirst = response['hydra:view']['hydra:first'] ?? this.hydraId
                this.hydraLast = response['hydra:view']['hydra:last'] ?? this.hydraId
                this.hydraNext = response['hydra:view']['hydra:next'] ?? this.hydraId
                this.hydraPrevious = response['hydra:view']['hydra:previous'] ?? this.hydraId
                if (this.hydraLast.includes('page=')) this.lastPage = /page=(\d+)/.exec(this.hydraLast)[1]
                else this.lastPage = 1
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
            getApiTypedRoutes: state => state.apiTypedRoutes,
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
            apiTypedRoutes: null,
            asc: true,
            company: '',
            createBody: {},
            enableShow: false,
            id,
            isCompanyFiltered: false,
            readFilter: '',
            readFilterPrevious: '',
            rows: [],
            search: {},
            showRouteName: null,
            sortName: null,
            sorted: null,
            pagination: true,
            page: 1,
            perPage: 15,
            lastPage: 1,
            totalItems: 0,
            hydraId: '',
            hydraFirst: '',
            hydraLast: '',
            hydraNext: '',
            hydraPrevious: ''
        })
    })()
}
