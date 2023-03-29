import api from '../../api'
import {defineStore} from 'pinia'

export const useSocietyListStore = defineStore('societyList', {
    actions: {
        async addSociety(payload){
            //console.log('payload', payload)
            await api('/api/societies', 'POST', payload)
            //console.log('response', response)
        },
        async delated(payload){
            //console.log('payload', payload)
            await api(`/api/societies/${payload}`, 'DELETE')
            // this.societies = this.societies.filter((society) => Number(society['@id'].match(/\d+/)[0]) !== payload)
            await this.fetch()
        },
        async fetch() {
            const response = await api('/api/societies', 'GET')
            // this.societies = this.updatePagination(response)
            console.log(response);
            await this.updatePagination(response)
        },
        async updateSociety (payload){
            console.log('payloadid', payload.id);
            console.log('payloaditemsUpdateData', payload.itemsUpdateData);
            const response = await api(`/api/societies/${payload.id}`, 'PATCH', payload.itemsUpdateData)
            console.log('response', response);
            if (!response.ok) {
                const error = new Error(
                    responseData.message
                  )
                  throw error
            }
        },
        async itemsPagination(nPage) {
            const response = await api(`/api/societies?page=${nPage}`, 'GET')
            await this.updatePagination(response)
        },
        async updatePagination(response) {
            const responseData = await response['hydra:member']
            const paginationView = response['hydra:view']
            this.firstPage = paginationView['hydra:first'].match(/\d+/)[0]
            this.lastPage = paginationView['hydra:last'] ? paginationView['hydra:last'].match(/\d+/)[0] : paginationView['@id'].match(/\d+/)[0]
            this.nextPage = paginationView['hydra:next'] ? paginationView['hydra:next'].match(/\d+/)[0] : paginationView['@id'].match(/\d+/)[0]
            this.currentPage = paginationView['@id'].match(/\d+/)[0]
            this.previousPage = paginationView['hydra:previous'] ? paginationView['hydra:previous'].match(/\d+/)[0] : paginationView['@id'].match(/\d+/)[0]
            this.societies = responseData
            // return responseData
        }
    },
    getters: {

    },
    state: () => ({
        currentPage: '',
        firstPage: '',
        lastPage: '',
        nextPage: '',
        previousPage: '',
        societies: []
    })
})
