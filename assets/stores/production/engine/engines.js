import api from '../../../api'
import {defineStore} from 'pinia'

const baseApi = '/api/engines'
export const useEngineStore = defineStore('engines', {
    actions: {
        async createCounterPart(data) {
            await api('/api/counter-parts', 'POST', data)
        },
        async createSparePart(data) {
            await api('/api/spare-parts', 'POST', data)
        },
        async createInfra(data) {
            await api('/api/infras', 'POST', data)
        },
        async createMachine(data) {
            await api('/api/machines', 'POST', data)
        },
        async createInformatique(data) {
            await api('/api/informatiques', 'POST', data)
        },
        async createTool(data) {
            await api('/api/tools', 'POST', data)
        },
        async createWorkstation(data) {
            await api('/api/workstations', 'POST', data)
        },
        async fetchAll(fetchCriteria = '') {
            const response = await api(baseApi + fetchCriteria, 'GET')
            this.items = await this.updatePagination(response)
            this.setCollectionData(response)
            this.isLoaded = true
        },
        async remove(id){
            await api(`${baseApi}/${id}`, 'DELETE')
        },
        updatePagination(response) {
            const responseData = response['hydra:member']
            let paginationView = {}
            if (Object.prototype.hasOwnProperty.call(response, 'hydra:view')) {
                paginationView = response['hydra:view']
            } else {
                paginationView = responseData
            }
            if (Object.prototype.hasOwnProperty.call(paginationView, 'hydra:first')) {
                this.pagination = true
                this.firstPage = Number(paginationView['hydra:first'] ? paginationView['hydra:first'].match(/page=(\d+)/)[1] : '1')
                this.lastPage = Number(paginationView['hydra:last'] ? paginationView['hydra:last'].match(/page=(\d+)/)[1] : paginationView['@id'].match(/page=(\d+)/)[1])
                this.nextPage = Number(paginationView['hydra:next'] ? paginationView['hydra:next'].match(/page=(\d+)/)[1] : paginationView['@id'].match(/page=(\d+)/)[1])
                this.currentPage = Number(paginationView['@id'].match(/page=(\d+)/)[1])
                this.previousPage = Number(paginationView['hydra:previous'] ? paginationView['hydra:previous'].match(/page=(\d+)/)[1] : paginationView['@id'].match(/page=(\d+)/)[1])
                return responseData
            }
            this.pagination = false
            return responseData
        },
        reset() {
            this.isLoaded = false
            this.isLoading = false
            this.items = []
            this.currentPage = 1
            this.pagination = true
        },
        setCollectionData(data) {
            this.engines = data['hydra:member']
            this.totalItems = data['hydra:totalItems']
            this.view = data['hydra:view']
        }
    },
    getters: {
    },
    state: () => ({
        items: [],
        currentPage: 1,
        pagination: true,
        engines: {},
        isLoaded: false,
        totalItems: 0,
        view: null
    })
})
