import api from '../../../api'
import {defineStore} from 'pinia'

export const useCompanyStore = defineStore('companies', {
    actions: {
        async fetch(criteria = '?pagination=false') {
            this.societies = []
            const response = await api(`/api/companies${criteria}`, 'GET')
            this.companies = response['hydra:member']
        },
        async fetchById(id) {
            const response = await api(`/api/companies/${id}`, 'GET')
            this.society = response
        },
        async update(data, id) {
            await api(`/api/companies/${id}`, 'PATCH', data)
            this.fetchById(id)
        }
    },
    getters: {
    },
    state: () => ({
        currentPage: '',
        firstPage: '',
        lastPage: '',
        nextPage: '',
        pagination: false,
        previousPage: '',
        companies: [],
        company: {}
    })
})
