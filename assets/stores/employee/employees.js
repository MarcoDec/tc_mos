import api from '../../api'
import {defineStore} from 'pinia'
import generateEmployee from './employee'

export const useEmployeeStore = defineStore('employee', {
    actions: {
        async fetch() {
            const response = await api('/api/employees/24', 'GET')
            const item = generateEmployee(response, this)
            this.employee = item
        },
        async fetchTeams() {
            const response = await api('/api/teams', 'GET')
            this.teams = response['hydra:member']
            console.log('res Teams', response['hydra:member'])
        }

    },
    getters: {
    },
    state: () => ({
        employee: {},
        teams: {}
    })
})
