import {defineStore} from 'pinia'
import api from '../../../api'

export const useNomenclatureStore = defineStore('nomenclatures', {
    actions: {
        async fetchOne(id = 1) {
            this.nomenclatureItem = await api(`/api/nomenclatures/${id}`, 'GET')
            this.isLoaded = true
        },
        async fetchAll(filter = '') {
            this.isLoading = true
            this.nomenclatures = (await api(`/api/nomenclatures${filter}`, 'GET'))['hydra:member']
            this.isLoading = false
            this.isLoaded = true
        }
    },
    state: () => ({
        isLoaded: false,
        isLoading: false,
        nomenclatureItem: {},
        nomenclatures: []
    })
})
