import api from '../../../api'
import {defineStore} from 'pinia'

export default function useZonesStore() {
    return defineStore('zones', {
        actions: {
            dispose() {
                for (const engine of this.zones)
                    if (typeof engine.dispose === 'function')
                        engine.$dispose()
                this.$dispose()
            },
            async fetch(url) {
                const response = await api(url)
                const items = []
                for (const item of response['hydra:member'])
                    items.push(item)
                return items
            },
            async fetchAll() {
                this.isLoaded = false
                this.isLoading = true
                this.zones = []
                this.zones = await this.fetch('/api/zones')
                this.isLoading = false
                this.isLoaded = true
            }
        },
        getters: {
        },
        state: () => ({
            isLoaded: false,
            isLoading: false,
            zones: []
        })
    })()
}
