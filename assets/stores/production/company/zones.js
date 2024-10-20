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
            async fetchAll(currentCompany = null) {
                let url = '/api/zones'
                if (currentCompany !== null) url = String(url).concat('?company=', currentCompany)
                this.isLoaded = false
                this.isLoading = true
                this.zones = []
                this.zones = await this.fetch(url)
                this.isLoading = false
                this.isLoaded = true
            },
            async fetchZones($filter = null) {
                let url = '/api/zones'
                if ($filter !== null) url = String(url).concat($filter)
                this.isLoaded = false
                this.isLoading = true
                this.zones = []
                this.zones = await this.fetch(url)
                this.isLoading = false
                this.isLoaded = true
            },
            async postNewZone(data) {
                return api('/api/zones', 'post', data)
            },
            async patchZone(zoneIri, data) {
                return api(zoneIri, 'patch', data)
            },
            async getZone(zoneIri) {
                return api(zoneIri).then(response => {
                    //console.log('response', response)
                    this.zone = response
                })
            },
            async deleteZone(zoneIri) {
                return api(zoneIri, 'DELETE')
            }
        },
        getters: {
        },
        state: () => ({
            isLoaded: false,
            isLoading: false,
            zones: [],
            zone: null,
            warehouseID: 0
        })
    })()
}
