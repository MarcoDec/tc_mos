import api from '../../../../api'
import {defineStore} from 'pinia'

const baseUrl = '/api/informatiques'
export const useInformatiqueStore = defineStore('informatiques', {
    actions: {
        async create(data) {
            const response = await api(baseUrl, 'POST', data)
            this.engine = await response
            this.isLoaded = true
        },
        async fetchAll() {
            const response = await api(baseUrl, 'GET')
            this.engines = await response
            this.isLoaded = true
        },
        async fetchOne(id) {
            const response = await api(`${baseUrl}/${id}`, 'GET')
            this.engine = await response
            this.isLoaded = true
        },
        async remove(id) {
            const response = await api(`${baseUrl}/${id}`, 'DELETE')
            this.engine = await response
            this.isLoaded = true
        },
        async update(data) {
            const response = await api(`${baseUrl}/${this.engine.id}`, 'PATCH', data)
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
