import api from '../../../api'
import {defineStore} from 'pinia'

export const useComponentAttachmentStore = defineStore('componentAttachment', {
    actions: {
        async ajout(data) {
            const form = new FormData()
            form.append('file', data.file)
            form.append('category', data.category)
            form.append('component', data.component)
            await api('/api/component-attachments', 'POST', form)
            this.fetchByComponent(this.componentId)
        },
        async fetchByComponent(id = 1) {
            this.items = []
            this.componentId = id
            const response = await api(`/api/component-attachments?pagination=false&component=/api/components/${id}`, 'GET')
            this.componentAttachments = await response['hydra:member']
        }
    },
    getters: {

    },
    state: () => ({
        componentAttachments: [],
        componentId: 1
    })
})
