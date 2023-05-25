import api from '../../api'
import {defineStore} from 'pinia'

export const useSupplierAttachmentStore = defineStore('supplierAttachment', {
    actions: {
        async ajout(data) {
            const form = new FormData()
            form.append('file', data.file)
            form.append('category', data.category)
            form.append('supplier', data.supplier)
            await api('/api/supplier-attachments', 'POST', form)
            this.fetchOne()
        },
        async fetchOne() {
            const response = await api('/api/supplier-attachments?pagination=false', 'GET')
            this.supplierAttachment = await response['hydra:member']
        }

    },
    getters: {
    },
    state: () => ({
        supplierAttachment: []
    })
})
