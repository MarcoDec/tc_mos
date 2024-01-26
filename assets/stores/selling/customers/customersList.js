import {actionsItems, gettersItems} from '../../tables/items'
import {defineStore} from 'pinia'
import api from '../../../api'

export const useCustomersStore = defineStore('customers-list', {
    actions: {
        ...actionsItems,
        async fetch(criteria = '') {
            const response = await api(`/api/customers${criteria}`, 'GET')
            this.customers = await this.updatePagination(response)
        },
        async fetchOne(id) {
            const response = await api(`/api/customers/${id}`, 'GET')
            this.customer = response
            return response
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
            await api(`/api/customers/${id}`, 'DELETE')
            this.customers = this.customers.filter(customer => Number(customer['@id'].match(/\d+/)[0]) !== id)
        },
        async addCustomer(payload) {
            await api('/api/customers', 'POST', payload)
        }
    },
    getters: {
        ...gettersItems,
        label() {
            return value =>
                this.options.find(option => option.value === value)?.text ?? null
        },
        itemsCustomers: state => state.customers.map(item => {
            const newObject = {
                '@id': item['@id'],
                name: item.name,
                state: item.embState.state,
                'address.zipCode': item.address.zipCode,
                'address.city': item.address.city,
                'copper.index.value': item.copper.index.value,
                id: item.id,
                filePath: item.filePath
            }
            return newObject
        }),
        optionCustomers: state => state.customers.map(item => {
            const newObject = {
                value: item['@id'],
                text: item.name
            }
            return newObject
        }),
        options: state =>
            state.items
                .map(customer => customer.option)
                .sort((a, b) => a.text.localeCompare(b.text))
    },

    state: () => ({
        items: [],
        customers: [],
        customer: {}
    })
})
