import api from '../../api'
import {defineStore} from 'pinia'

export const useCustomerAttachmentStore = defineStore('customerAttachment', {
    actions: {
        async ajout(data) {
            const form = new FormData()
            form.append('file', data.file)
            form.append('category', data.category)
            form.append('customer', data.customer)
            await api('/api/customer-attachments', 'POST', form)
            this.fetchOne()
        },
        async fetchOne() {
            this.items = []
            const response = await api('/api/customer-attachments?pagination=false', 'GET')
            this.customerAttachment = await response['hydra:member']
        }

    },
    getters: {

    },
    state: () => ({
        customerAttachment: []
    })
})
