import api from '../../api'
import {defineStore} from 'pinia'
import generateItem from './item'
import generateSupplier from '../supplier/supplier'

export const actionsItems = {
    async create(data, url = null) {
        this.reset()
        const response = await api(url ?? this.iri, 'POST', data)
        this.items.push(generateItem(this.iriType, response.content, this))
    },
    dispose() {
        this.reset()
        this.$dispose()
    },
    async fetch() {
        const response = [
            {
                etat: 'refus',
                id: 1,
                nom: '3M FRANCE'
            },
            {
                etat: 'attente',
                id: 2,
                nom: 'ABB'
            },
            {
                etat: 'valide',
                id: 3,
                nom: 'ABB'
            }
        ]
        for (const supplier of response)
            this.items.push(generateSupplier(supplier, this))
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
        for (const item of this.items) item.dispose()
    },
    async resetSearch() {
        this.search = {}
        await this.fetch()
    },
    async sort(field) {
        if (this.sorted === field.name) this.asc = !this.asc
        else {
            this.asc = true
            this.sorted = field.name
        }
        await this.fetch()
    }
}

export const gettersItems = {
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
        return state.sorted === null
            ? {}
            : {[`order[${state.sorted}]`]: this.orderParam}
    },
    orderParam: state => (state.asc ? 'asc' : 'desc')
}

export default function generateItems(iriType) {
    return defineStore(iriType, {
        actions: {...actionsItems},
        getters: {...gettersItems},
        state: () => ({asc: true, iriType, items: [], search: {}, sorted: null})
    })()
}
