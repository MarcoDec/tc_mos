import api from '../../api'
import {defineStore} from 'pinia'

export const useSupplierContactsStore = defineStore('items', {
    actions: {
        async fetchBySociety(id) {
            const response = await api(`/api/supplier-contacts?society=${id}`, 'GET')
            this.items = response['hydra:member']
            console.log('response', response);
        },

    },
    getters: {
    },
    state: () => ({
        items: [],
    })
})
