import api from '../../../../api'
import {defineStore} from 'pinia'

const baseUrl = '/api/engine-attachments'
const linkedEntity = 'engine'
export const useEngineAttachmentStore = defineStore('informatiqueAttachment', {
    actions: {
        async ajout(data) {
            const form = new FormData()
            form.append('file', data.file)
            form.append('category', data.category)
            form.append(linkedEntity, data.informatique)
            await api(baseUrl, 'POST', form)
            this.fetchByElement(this.id)
        },
        async fetchByElement(id) {
            const response = await api(`${baseUrl}?pagination=false&${linkedEntity}=/api/${linkedEntity}s/${id}`, 'GET')
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
