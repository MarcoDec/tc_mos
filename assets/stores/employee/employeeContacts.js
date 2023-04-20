import api from '../../api'
import {defineStore} from 'pinia'
import generateEmployeeContact from './employeeContact'

export const useEmployeeContactsStore = defineStore('employeeContacts', {
    actions: {
        async fetchContactsEmpl(id) {
            const response = await api(`/api/employee-contacts?employee=${id}`, 'GET')
            for (const employee of response['hydra:member']) {
                const item = generateEmployeeContact(employee, this)
                this.employeeContacts.push(item)
            }

            console.log('res fetchContactsEmpl', response['hydra:member'])
        }

    },
    getters: {
    },
    state: () => ({
        employeeContacts: []
    })
})
