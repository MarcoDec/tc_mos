import api from '../../api'
import {defineStore} from 'pinia'

export const useEmployeeAttachmentStore = defineStore('employeeAttachment', {
    actions: {
        async ajout(data) {
            const form = new FormData()
            form.append('file', data.file)
            form.append('category', data.category)
            form.append('employee', data.employee)
            await api('/api/employee-attachments', 'POST', form)
            this.fetchByElement(this.id)
        },
        async fetchByElement(id) {
            console.log(id)
            const response = await api(`/api/employee-attachments?pagination=false&employee=/api/employees/${id}`, 'GET')
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
