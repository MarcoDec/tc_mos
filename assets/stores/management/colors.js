import {defineStore} from 'pinia'
import fetchApi from '../../api'
import generateColor from './color'

export default defineStore('color', {
    actions: {
        dispose() {
            this.resetItems()
            this.$reset()
            this.$dispose()
        },
        async fetch() {
            this.resetItems()
            const response = await fetchApi('/api/colors', 'GET', this.search)
            if (response.status === 200)
                for (const color of response.content['hydra:member'])
                    this.items.push(generateColor(color, this))
        },
        replaceSearch(field, value) {
            this.search[field] = value
        },
        resetItems() {
            this.items = []
            for (const color of this.items)
                color.dispose()
        },
        async resetSearch() {
            this.search = {}
            await this.fetch()
        }
    },
    getters: {
        length: state => state.items.length
    },
    state: () => ({items: [], search: {}})
})
