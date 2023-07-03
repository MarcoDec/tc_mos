import api from '../../api'
import {defineStore} from 'pinia'
import generateEmployee from './employee'

export const useEmployeeStore = defineStore('employee', {
    actions: {
        async fetchAll() {
            const response = await api('/api/employees?pagination=false', 'GET')
            // const item = generateEmployee(response, this)
            this.items = response['hydra:member']
        },
        async fetchContactsEmpl(id) {
            const response = await api(`/api/employee-contacts?employee=${id}`, 'GET')
            for (const employee of response['hydra:member']) {
                const item = generateEmployee(employee, this)
                this.employeeContacts.push(item)
            }
        },
        async fetchOne(id = 24) {
            this.isLoading = true
            const response = await api(`/api/employees/${id}`, 'GET')
            this.employee = generateEmployee(response, this)
            this.isLoading = false
            this.isLoaded = true
        },
        async fetchTeams() {
            const response = await api('/api/teams', 'GET')
            this.teams = response['hydra:member']
            this.teamsIsLoaded = true
        }

    },
    getters: {
    },
    state: () => ({
        employee: {},
        employeeContacts: [],
        isLoaded: false,
        isLoading: false,
        items: [],
        teams: [],
        teamsIsLoaded: false
    })
})
