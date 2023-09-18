import {defineStore} from 'pinia'
import api from '../../../api'

export const useCompanyStore = defineStore('companyStore', {
    actions: {
        async fetch(criteria = '?pagination=false') {
            const response = await api(`/api/companies${criteria}`, 'GET')
            this.companies = response['hydra:member']
        },
        async fetchById(id) {
            this.company = await api(`/api/companies/${id}`, 'GET')
        },
        async update(data, id) {
            await api(`/api/companies/${id}`, 'PATCH', data)
        },
        async remove(id) {
            await api(`/api/companies/${id}`, 'DELETE')
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
        company: null
    })
})
