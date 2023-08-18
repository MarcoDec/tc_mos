import api from '../../../api'
import {defineStore} from 'pinia'

const baseApi = '/api/engines'
export const useEngineStore = defineStore('engines', {
    actions: {
        async fetchAll(fetchCriteria = '') {
            const response = await api(baseApi + fetchCriteria, 'GET')
            this.setCollectionData(response)
            this.isLoaded = true
        },
        setCollectionData(data) {
            this.engines = data['hydra:member']
            this.totalItems = data['hydra:totalItems']
            this.view = data['hydra:view']
        },
    },
    getters: {
        currentPage: state => {
            if (state.engines.length > 0) {
                const result = /page=(\d+)/.exec(state.view['@id'])
                if (result === null) return 1
                return Number(result[0].substring(5))
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
        engines: {},
        isLoaded: false,
        totalItems: 0,
        view: null
    })
})
