import api from '../../../../api'
import {defineStore} from 'pinia'

export const useToolsStore = defineStore('tools', {
    actions: {
        async create(data) {
            const response = await api('/api/tools', 'POST', data)
            this.engine = await response
            this.isLoaded = true
        },
        async fetchAll() {
            const response = await api('/api/tools', 'GET')
            this.engines = await response
            this.isLoaded = true
        },
        async fetchOne(id) {
            const response = await api(`/api/tools/${id}`, 'GET')
            this.engine = await response
            this.isLoaded = true
        },
        async remove(id) {
            const response = await api(`/api/tools/${id}`, 'DELETE')
            this.engine = await response
            this.isLoaded = true
        },
        async update(data) {
            const response = await api(`/api/tools/${this.engine.id}`, 'PATCH', data)
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
