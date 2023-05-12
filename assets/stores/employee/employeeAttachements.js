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
            this.fetch()
        },
        async fetch() {
            const response = await api('/api/employee-attachments?pagination=false', 'GET')
            this.employeeAttachment = await response['hydra:member']
        }

    },
    getters: {
    },
    state: () => ({
        employeeAttachment: []
    })
})
