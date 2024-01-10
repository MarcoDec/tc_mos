import api from '../../api'
import {defineStore} from 'pinia'

export const useVatMessagesStore = defineStore('vatMessages', {
    actions: {
        async fetchVatMessage() {
            const response = await api('/api/vat-messages', 'GET')
            this.vatMessage = response['hydra:member']
        }

    },
    getters: {},
    state: () => ({
        vatMessage: []
    })
})
