import api from '../../../../api'
import {defineStore} from 'pinia'

const baseApi = '/api/manufacturer-engines'
export const useManufacturerEngineStore = defineStore('manufacturer-engines', {
    actions: {
        async create(data) {
            const response = await api(baseApi, 'POST', data)
            this.engine = await response
            this.isLoaded = true
        },
        async fetchAll(fetchCriteria = '') {
            const response = await api(baseApi + fetchCriteria, 'GET')
            this.setCollectionData(response)
            this.isLoaded = true
        },
        async fetchOne(id) {
            const response = await api(`${baseApi}/${id}`, 'GET')
            this.engine = await response
            this.isLoaded = true
        },
        async remove(id) {
            const response = await api(`${baseApi}/${id}`, 'DELETE')
            this.engine = await response
            this.isLoaded = true
        },
        setCollectionData(data) {
            this.engines = data['hydra:member']
            this.totalItems = data['hydra:totalItems']
            this.view = data['hydra:view']
        },
        async update(data) {
            const response = await api(`${baseApi}/${id}`, 'PATCH', data)
            this.engine = await response
        }
    },
    getters: {
        currentPage: state => {
            if (state.engines.length > 0) {
                const result = /page=(\d+)/.exec(state.view['@id'])
                console.log(state.view['@id'], result)
                if (result === null) return 'page=1'
                return result[0]
            }
            return 0
        },
        pagination: state => {
            if (state.engines.length > 0) {
                const result = /page=(\d+)/.exec(state.view['@id'])
                if (result === null) return false
                return true
            }
            return false
        }
    },
    state: () => ({
        engine: {},
        engines: [],
        isLoaded: false,
        totalItems: 0,
        view: null
    })
})
