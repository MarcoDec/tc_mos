import Api from '../../Api'
import {defineStore} from 'pinia'
import generateMessage from './vatMessage'

export default defineStore('vat-message', {
    actions: {
        async create(fields, data) {
            this.reset()
            const response = await new Api(fields).fetch('/api/vat-messages', 'POST', data)
            if (response.status === 422)
                throw response.content.violations
            this.items.push(generateMessage(response.content, this))
        },
        dispose() {
            this.reset()
            this.$dispose()
        },
        async fetch() {
            this.resetItems()
            const response = await new Api().fetch('/api/vat-messages', 'GET', this.fetchBody)
            if (response.status === 200)
                for (const message of response.content['hydra:member'])
                    this.items.push(generateMessage(message, this))
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
            for (const message of this.items)
                message.dispose()
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
