import api from '../../api'
import {defineStore} from 'pinia'

export const useEmployeeAttachmentStore = defineStore('employeeAttachment', {
    actions: {
        async ajout(data) {
            const form = new FormData()
            form.append('file', data.file)
            form.append('category', data.category)
            form.append('employee', data.employee)
            const res = await api('/api/employee-attachments', 'POST', form)
            console.log('res', res)
        },
        async fetch() {
            const response = await api('/api/employee-attachments', 'GET')
            this.employeeAttachment = await response['hydra:member']
            console.log('employeeAttachment', this.employeeAttachment)
        }

    },
    getters: {
    },
    state: () => ({
        employeeAttachment: []
    })
})
