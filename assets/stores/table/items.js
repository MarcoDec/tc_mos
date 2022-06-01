import {defineStore} from 'pinia'
import fetchApi from '../../api'
import generateItem from './item'

export default function generateItems(iriType) {
    return defineStore(iriType, {
        actions: {
            async create(data, url = null) {
                this.reset()
                const response = await fetchApi(url ?? this.iri, 'POST', data)
                if (response.status === 422)
                    throw response.content.violations
                this.items.push(generateItem(this.iriType, response.content, this))
            },
            dispose() {
                this.reset()
                this.$dispose()
            },
            async fetch(url = null) {
                this.resetItems()
                const response = await fetchApi(url ?? this.iri, 'GET', this.fetchBody)
                if (response.status === 200)
                    for (const item of response.content['hydra:member'])
                        this.items.push(generateItem(this.iriType, item, this))
            },
            remove(removed) {
                this.items = this.items.filter(item => item['@id'] !== removed)
            },
            replaceSearch(field, value) {
                this.search[field] = value
            },
            reset() {
                this.resetItems()
                this.$reset()
            },
            resetItems() {
                this.items = []
                for (const item of this.items)
                    item.dispose()
            },
            async resetSearch() {
                this.search = {}
                await this.fetch()
            },
            async sort(field) {
                if (this.sorted === field.name)
                    this.asc = !this.asc
                else {
                    this.asc = true
                    this.sorted = field.name
                }
                await this.fetch()
            }
        },
        getters: {
            ariaSort() {
                return field => (this.isSorter(field) ? this.order : 'none')
            },
            fetchBody() {
                return {...this.search, ...this.orderBody}
            },
            iri: state => `/api/${state.iriType}`,
            isSorter: state => field => field.name === state.sorted,
            length: state => state.items.length,
            order: state => (state.asc ? 'ascending' : 'descending'),
            orderBody(state) {
                return state.sorted === null ? {} : {[`order[${state.sorted}]`]: this.orderParam}
            },
            orderParam: state => (state.asc ? 'asc' : 'desc')
        },
        state: () => ({asc: true, iriType, items: [], search: {}, sorted: null})
    })()
}
