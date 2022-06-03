import {defineStore} from 'pinia'
import fetchApi from '../../api'
import generateColor from './color'

export default defineStore('color', {
    actions: {
        async create(data) {
            this.reset()
            const response = await fetchApi('/api/colors', 'POST', data)
            if (response.status === 422)
                throw response.content.violations
            this.items.push(generateColor(response.content, this))
        },
        dispose() {
            this.reset()
            this.$dispose()
        },
        async fetch() {
            this.resetItems()
            const response = await fetchApi('/api/colors', 'GET', this.fetchBody)
            if (response.status === 200)
                for (const color of response.content['hydra:member'])
                    this.items.push(generateColor(color, this))
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
            for (const color of this.items)
                color.dispose()
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
