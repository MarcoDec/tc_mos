import {actionsItems, gettersItems} from '../tables/items'
import {defineStore} from 'pinia'
import api from '../../api'

export const useEmployeesStore = defineStore('employees', {
    actions: {
        ...actionsItems,
        async fetch(criteria = '') {
            const response = await api(`/api/employees${criteria}`, 'GET')
            this.employees = await this.updatePagination(response)
        },
        async updatePagination(response) {
            const responseData = await response['hydra:member']
            let paginationView = {}
            if (Object.prototype.hasOwnProperty.call(response, 'hydra:view')) {
                paginationView = response['hydra:view']
            } else {
                paginationView = responseData
            }
            if (Object.prototype.hasOwnProperty.call(paginationView, 'hydra:first')) {
                this.pagination = true
                this.firstPage = paginationView['hydra:first'] ? paginationView['hydra:first'].match(/page=(\d+)/)[1] : '1'
                this.lastPage = paginationView['hydra:last'] ? paginationView['hydra:last'].match(/page=(\d+)/)[1] : paginationView['@id'].match(/page=(\d+)/)[1]
                this.nextPage = paginationView['hydra:next'] ? paginationView['hydra:next'].match(/page=(\d+)/)[1] : paginationView['@id'].match(/page=(\d+)/)[1]
                this.currentPage = paginationView['@id'].match(/page=(\d+)/)[1]
                this.previousPage = paginationView['hydra:previous'] ? paginationView['hydra:previous'].match(/page=(\d+)/)[1] : paginationView['@id'].match(/page=(\d+)/)[1]
                return responseData
            }
            this.pagination = false
            return responseData
        },
        async remove(id){
            await api(`/api/employees/${id}`, 'DELETE')
        },
        async addEmployee(payload) {
            await api('/api/employees', 'POST', payload)
        }
    },
    getters: {
        ...gettersItems,
        label() {
            return value =>
                this.options.find(option => option.value === value)?.text ?? null
        },
        itemsEmployees: state => state.employees.map(item => {
            const newObject = {
                '@id': item['@id'],
                timeCard: item.timeCard,
                surname: item.surname,
                name: item.name,
                initials: item.initials,
                username: item.username,
                userEnabled: item.userEnabled,
                state: item.embState.state,
                company: item.company,
                id: item.id
            }
            return newObject
        }),
        options: state =>
            state.items
                .map(employee => employee.option)
                .sort((a, b) => a.text.localeCompare(b.text))
    },

    state: () => ({
        items: [],
        employees: []
    })
})
