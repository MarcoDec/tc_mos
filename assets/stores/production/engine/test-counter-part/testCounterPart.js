import api from '../../../../api'
import {defineStore} from 'pinia'

export const useCounterPartStore = defineStore('counter-parts', {
    actions: {
        async create(data) {
            const response = await api('/api/counter-parts', 'POST', data)
            this.engine = await response
            this.isLoaded = true
        },
        async fetchAll() {
            const response = await api('/api/counter-parts', 'GET')
            this.engines = await response
            this.isLoaded = true
        },
        async fetchOne(id) {
            const response = await api(`/api/counter-parts/${id}`, 'GET')
            this.engine = await response
            this.isLoaded = true
        },
        async remove(id) {
            const response = await api(`/api/counter-parts/${id}`, 'DELETE')
            this.engine = await response
            this.isLoaded = true
        },
        async update(data) {
            const response = await api(`/api/counter-parts/${this.engine.id}`, 'PATCH', data)
            this.engine = await response
        }
    },
    getters: {

    },
    state: () => ({
        engine: {},
        engines: [],
        isLoaded: false
    })
})
