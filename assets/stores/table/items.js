import api from '../../api'
import {defineStore} from 'pinia'
import generateItem from './item'

function extractPage(view, hydra) {
    if (typeof view !== 'object')
        return 1
    const subject = view[`hydra:${hydra}`]
    if (typeof subject === 'undefined')
        return 1
    const extracted = subject.match(/page=(\d+)/)
    return parseInt(extracted[1] ?? 1)
}

export default function generateItems(iriType) {
    return defineStore(iriType, {
        actions: {
            async create(fields, data, url = null) {
                this.reset()
                const response = await api(fields).fetchOne(url ?? this.iri, 'POST', data)
                this.items.push(generateItem(this.iriType, response, this))
            },
            dispose() {
                this.reset()
                this.$dispose()
            },
            async fetchOne(url = null) {
                this.resetItems()
                const response = await api.fetchOne(url ?? this.iri, 'GET', this.fetchBody)
                const view = response['hydra:view']
                this.first = extractPage(view, 'first')
                this.last = extractPage(view, 'last')
                this.next = extractPage(view, 'next')
                this.prev = extractPage(view, 'prev')
                this.total = response['hydra:totalItems']
                for (const item of response['hydra:member'])
                    this.items.push(generateItem(this.iriType, item, this))
                if (this.current > this.pages) {
                    this.current = this.pages
                    if (this.current > 0)
                        await this.fetchOne(url)
                    else
                        this.current = 1
                }
            },
            async goTo(index, url = null) {
                this.current = index
                await this.fetchOne(url)
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
                await this.fetchOne()
            },
            async sort(field) {
                if (this.sorted === field.name)
                    this.asc = !this.asc
                else {
                    this.asc = true
                    this.sorted = field.name
                    this.sortName = field.sortName ?? field.name
                }
                await this.fetchOne()
            }
        },
        getters: {
            ariaSort() {
                return field => (this.isSorter(field) ? this.order : 'none')
            },
            fetchBody() {
                return {page: this.current, ...this.search, ...this.orderBody}
            },
            iri: state => `/api/${state.iriType}`,
            isSorter: state => field => field.name === state.sorted,
            length: state => state.items.length,
            order: state => (state.asc ? 'ascending' : 'descending'),
            orderBody(state) {
                return state.sortName === null ? {} : {[`order[${state.sortName}]`]: this.orderParam}
            },
            orderParam: state => (state.asc ? 'asc' : 'desc'),
            pages: state => Math.ceil(state.total / 15)
        },
        state: () => ({
            asc: true,
            current: 1,
            first: 1,
            iriType,
            items: [],
            last: 1,
            next: 1,
            prev: 1,
            search: {},
            sortName: null,
            sorted: null,
            total: 0
        })
    })()
}
