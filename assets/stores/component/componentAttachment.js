import api from '../../api'
import {defineStore} from 'pinia'

export const useComponentAttachmentStore = defineStore('componentAttachment', {
    actions: {
        async ajout(data) {
            const form = new FormData()
            form.append('file', data.file)
            form.append('category', data.category)
            form.append('component', data.component)
            await api('/api/component-attachments', 'POST', form)
        },
        async fetch() {
            this.items = []
            const response = await api('/api/component-attachments', 'GET')
            this.componentAttachment = await response['hydra:member']
        }

    },
    getters: {

    },
    state: () => ({
        componentAttachment: []
    })
})
