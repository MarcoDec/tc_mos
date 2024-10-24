import api from '../../../api'
import {defineStore} from 'pinia'

export const useWarehouseAttachmentStore = defineStore('warehouseAttachment', {
    actions: {
        async ajout(data) {
            const form = new FormData()
            form.append('file', data.file)
            form.append('category', data.category)
            form.append('warehouse', data.warehouse)
            await api('/api/warehouse-attachments', 'POST', form)
        },
        async fetchByElement(id) {
            const response = await api(`/api/warehouse-attachments?pagination=false&warehouse=/api/warehouses/${id}`, 'GET')
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
