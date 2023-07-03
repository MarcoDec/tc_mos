import api from '../../api'
import {defineStore} from 'pinia'
import generateEmployeeContact from './employeeContact'

export const useEmployeeContactsStore = defineStore('employeeContacts', {
    actions: {
        async ajout(data, id) {
            await api('/api/employee-contacts', 'POST', data)
            this.$reset()
            this.fetchContactsEmpl(id)
        },
        async deleted(payload){
            await api(`/api/employee-contacts/${payload}`, 'DELETE')
            this.$reset()
            this.fetchContactsEmpl(payload)
        },
        async fetchContactsEmpl(id) {
            const response = await api(`/api/employee-contacts?employee=${id}`, 'GET')
            for (const employee of response['hydra:member']) {
                const item = generateEmployeeContact(employee, this)
                this.employeeContacts.push(item)
            }
            this.isLoaded = true
        }

    },
    getters: {
    },
    state: () => ({
        employeeContacts: [],
        isLoaded: false
    })
})
