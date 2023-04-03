import api from '../../api'
import {defineStore} from 'pinia'

export const useComponentAttachmentStore = defineStore('componentAttachment', {
    actions: {
        async fetch() {
            this.items = []
            const response = await api('/api/component-attachments', 'GET')
            this.componentAttachment = await response['hydra:member']
        },
        async ajout(data) {
            const response = await api('/api/component-attachments', 'POST', data)
            console.log('response attachments', response)
        }
    },
    getters: {

    },
    state: () => ({
        componentAttachment: []
    })
})
