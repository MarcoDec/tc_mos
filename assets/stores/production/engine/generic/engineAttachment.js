import api from '../../../../api'
import {defineStore} from 'pinia'

export function useGenEngineAttachmentStore(name='engineAttachment', baseUrl='/api/engine-attachments', linkedEntity='engine'){
    return defineStore(name, {
        actions: {
            async ajout(data) {
                const form = new FormData()
                form.append('file', data.file)
                form.append('category', data.category)
                form.append(linkedEntity, data.engine)
                await api(baseUrl, 'POST', form)
                await this.fetchByElement(this.id)
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
}
