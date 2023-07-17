import api from '../../api'
import {defineStore} from 'pinia'

export default function generateEmployeeContact(employeeContacts) {
    return defineStore(`employee-contacts/${employeeContacts.id}`, {
        actions: {
            dispose() {
                this.$reset()
                this.$dispose()
            },
            async updateContactEmp(data) {
                const response = await api(`/api/employee-contacts/${employeeContacts.id}`, 'PATCH', data)
                this.$state = {...response}
            }
        },
        getters: {

        },
        state: () => ({...employeeContacts})
    })()
}
