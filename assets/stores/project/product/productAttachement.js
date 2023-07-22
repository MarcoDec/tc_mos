import api from '../../../api'
import {defineStore} from 'pinia'

export const useProductAttachmentStore = defineStore('productAttachment', {
    actions: {
        async ajout(data) {
            const form = new FormData()
            form.append('file', data.file)
            form.append('category', data.category)
            form.append('product', data.product)
            await api('/api/product-attachments', 'POST', form)
            this.fetchByElement(this.id)
        },
        async fetchByElement(id) {
            const response = await api(`/api/product-attachments?pagination=false&product=/api/products/${id}`, 'GET')
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
