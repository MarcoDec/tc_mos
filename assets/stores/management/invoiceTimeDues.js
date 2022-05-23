import {defineStore} from 'pinia'
import fetchApi from '../../api'
import generateInvoiceTimeDue from './invoiceTimeDue'

export default defineStore('invoice-time-due', {
    actions: {
        async create(data) {
            this.reset()
            const response = await fetchApi('/api/invoice-time-dues', 'POST', data)
            if (response.status === 422)
                throw response.content.violations
            this.items.push(generateInvoiceTimeDue(response.content, this))
        },
        dispose() {
            this.reset()
            this.$dispose()
        },
        async fetch() {
            this.resetItems()
            const response = await fetchApi('/api/invoice-time-dues', 'GET', this.fetchBody)
            if (response.status === 200)
                for (const invoiceTimeDue of response.content['hydra:member'])
                    this.items.push(generateInvoiceTimeDue(invoiceTimeDue, this))
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
            for (const invoiceTimeDue of this.items)
                invoiceTimeDue.dispose()
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
        isSorter: state => field => field.name === state.sorted,
        length: state => state.items.length,
        order: state => (state.asc ? 'ascending' : 'descending'),
        orderBody(state) {
            return state.sorted === null ? {} : {[`order[${state.sorted}]`]: this.orderParam}
        },
        orderParam: state => (state.asc ? 'asc' : 'desc')
    },
    state: () => ({asc: true, items: [], search: {}, sorted: null})
})
