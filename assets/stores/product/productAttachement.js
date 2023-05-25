import api from '../../api'
import {defineStore} from 'pinia'

export const useProductAttachmentStore = defineStore('productAttachment', {
    actions: {
        async ajout(data) {
            const form = new FormData()
            form.append('file', data.file)
            form.append('category', data.category)
            form.append('product', data.product)
            await api('/api/product-attachments', 'POST', form)
            this.fetchOne()
        },
        async fetchOne() {
            const response = await api('/api/product-attachments?pagination=false', 'GET')
            this.productAttachment = await response['hydra:member']
        }
    },
    getters: {
    },
    state: () => ({
        productAttachment: []
    })
})
