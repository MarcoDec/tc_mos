import api from '../../../api'
import {defineStore} from 'pinia'

export const useCustomerAttachmentStore = defineStore('customerAttachment', {
    actions: {
        async ajout(data) {
            const form = new FormData()
            form.append('file', data.file)
            form.append('category', data.category)
            form.append('customer', data.customer)
            await api('/api/customer-attachments', 'POST', form)
            this.fetchByElement(this.id)
        },
        async fetchByElement(id) {
            const response = await api(`/api/customer-attachments?pagination=false&employee=/api/customers/${id}`, 'GET')
            this.elementAttachments = await response['hydra:member']
            this.id = id
        }

    },
    getters: {

    },
    state: () => ({
        elementAttachments: [],
        id: 1
    })
})
