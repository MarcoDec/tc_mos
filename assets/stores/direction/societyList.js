import api from '../../api'
import {defineStore} from 'pinia'

export const useSocietyListStore = defineStore('societyList', {
    actions: {
        async addSociety(payload){
            await api('/api/societies', 'POST', payload)
            this.itemsPagination(this.lastPage)
        },
        async countryOption() {
            const response = await api('/api/countries/options', 'GET')
            this.countries = response['hydra:member']
        },
        async delated(payload){
            await api(`/api/societies/${payload}`, 'DELETE')
            this.societies = this.societies.filter(society => Number(society['@id'].match(/\d+/)[0]) !== payload)
        },
        async fetch() {
            const response = await api('/api/societies', 'GET')
            this.societies = await this.updatePagination(response)
        },
        async filterBy(payload){
            let url = '/api/societies?'
            if (payload.name !== '') {
                url += `name=${payload.name}&`
            }
            if (payload.address.address !== '') {
                url += `address.address=${payload.address.address}&`
            }
            if (payload.address.address2 !== '') {
                url += `address.address2=${payload.address.address2}&`
            }
            if (payload.address.city !== '') {
                url += `address.city=${payload.address.city}&`
            }
            if (payload.address.country !== '') {
                url += `address.country=${payload.address.country}&`
            }
            url += 'page=1'
            const response = await api(url, 'GET')
            this.societies = await this.updatePagination(response)
        },
        async itemsPagination(nPage) {
            const response = await api(`/api/societies?page=${nPage}`, 'GET')
            this.societies = await this.updatePagination(response)
        },
        async paginationSortableOrFilterItems(payload) {
            let response = {}
            if (payload.filter.value === true){
                let url = '/api/societies?'
                if (payload.filterBy.value.name !== '') {
                    url += `name=${payload.filterBy.value.name}&`
                }
                if (payload.filterBy.value.address.address !== '') {
                    url += `address.address=${payload.filterBy.value.address.address}&`
                }
                if (payload.filterBy.value.address.address2 !== '') {
                    url += `address.address2=${payload.filterBy.value.address.address2}&`
                }
                if (payload.filterBy.value.address.city !== '') {
                    url += `address.city=${payload.filterBy.value.address.city}&`
                }
                if (payload.filterBy.value.address.country !== '') {
                    url += `address.country=${payload.filterBy.value.address.country}&`
                }
                url += `page=${payload.nPage}&`
                if (url.charAt(url.length - 1) === '&') {
                    url = url.slice(0, -1)
                }
                response = await api(url, 'GET')
                this.societies = await this.updatePagination(response)
            } else if (payload.sortable.value === false) {
                response = await api(`/api/societies?page=${payload.nPage}`, 'GET')
                this.societies = await this.updatePagination(response)
            } else {
                if (payload.trierAlpha.value.name === 'name') {
                    response = await api(`/api/societies?order%5B${payload.trierAlpha.value.name}%5D=${payload.trierAlpha.value.trier.value}&page=${payload.nPage}`, 'GET')
                } else {
                    response = await api(`/api/societies?order%5Baddress.${payload.trierAlpha.value.name}%5D=${payload.trierAlpha.value.trier.value}&page=${payload.nPage}`, 'GET')
                }
                this.societies = await this.updatePagination(response)
            }
        },
        async sortableItems(payload) {
            let response = {}
            if (payload.name === 'name') {
                response = await api(`/api/societies?order%5B${payload.name}%5D=${payload.trier.value}&page=${this.currentPage}`, 'GET')
            } else {
                response = await api(`/api/societies?order%5Baddress.${payload.name}%5D=${payload.trier.value}&page=${this.currentPage}`, 'GET')
            }
            this.societies = await this.updatePagination(response)
        },
        async updatePagination(response) {
            const responseData = await response['hydra:member']
            const paginationView = response['hydra:view']
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
        async updateSociety(payload){
            await api(`/api/societies/${payload.id}`, 'PATCH', payload.itemsUpdateData)
            if (payload.sortable.value === false) {
                this.itemsPagination(this.currentPage)
            } else {
                this.paginationSortableOrFilterItems({nPage: this.currentPage, sortable: payload.sortable, trierAlpha: payload.trierAlpha})
            }
        }

    },
    getters: {
        countriesOption: state => state.countries.map(country => {
            const opt = {
                text: country.text,
                value: country.code
            }
            return opt
        }),
        itemsSocieties: state => state.societies.map(item => {
            const {address, address2, city, country, email, phoneNumber, zipCode} = item.address
            const idAdress = item.address['@id']
            const typeAdress = item.address['@type']
            const itemsTab = []
            const newObject = {
                ...item,
                // address: undefined, // Remove the original nested address object
                address: address ?? null,
                address2: address2 ?? null,
                city: city ?? null,
                country: country ?? null,
                email: email ?? null,
                idAdress: idAdress ?? null,
                phoneNumber: phoneNumber ?? null,
                typeAdress: typeAdress ?? null,
                zipCode: zipCode ?? null
            }
            itemsTab.push(newObject)
            return itemsTab
        })
    },
    state: () => ({
        countries: [],
        currentPage: '',
        firstPage: '',
        lastPage: '',
        nextPage: '',
        pagination: false,
        previousPage: '',
        societies: []
    })
})
