import api from '../../../../api'
import {defineStore} from 'pinia'

export const useManufacturerEngineAttachmentStore = defineStore('manufacturerEngineAttachment', {
    actions: {
        async ajout(data) {
            const form = new FormData()
            form.append('file', data.file)
            form.append('category', data.category)
            form.append('engine', data.engine)
            await api('/api/manufacturer-engine-attachments', 'POST', form)
            this.fetchByElement(this.id)
        },
        async fetchByElement(id) {
            const response = await api(`/api/manufacturer-engine-attachments?pagination=false&engine=/api/manufacturer-engines/${id}`, 'GET')
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
