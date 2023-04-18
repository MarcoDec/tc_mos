import api from '../../api'
import {defineStore} from 'pinia'

export const useSupplierAttachmentStore = defineStore('supplierAttachment', {
    actions: {
        async ajout(data) {
            const form = new FormData()
            form.append('file', data.file)
            form.append('category', data.category)
            form.append('supplier', data.supplier)
            const res = await api('/api/supplier-attachments', 'POST', form)
        },
        async fetch() {
            const response = await api('/api/supplier-attachments', 'GET')
            this.supplierAttachment = await response['hydra:member']
        }

    },
    getters: {
    },
    state: () => ({
        supplierAttachment: []
    })
})
