import {defineStore} from 'pinia'
import fetchApi from '../api'

export default function generateOptions(type) {
    return defineStore(`options/${type}`, {
        actions: {
            dispose() {
                this.$reset()
                this.$dispose()
            },
            async fetch() {
                this.$reset()
                const response = await fetchApi(this.url)
                this.items = response.content['hydra:member']
            }
        },
        getters: {
            label: state => value => state.items.find(item => item['@id'] === value)?.text ?? null,
            options: state => state.items.map(item => ({text: item.text, value: item['@id']})),
            url: state => `/api/${state.type}/options`
        },
        state: () => ({items: [], type})
    })()
}
