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
        async updateSociety (payload){
            await api(`/api/societies/${payload.id}`, 'PATCH', payload.itemsUpdateData)
            if (!payload.sortable.value) {
                this.itemsPagination(this.currentPage)
            }else{
                this.paginationSortableItems({nPage:this.currentPage, sortable:payload.sortable, trierAlpha:payload.trierAlpha})
            }
        },
        async itemsPagination(nPage) {
            const response = await api(`/api/societies?page=${nPage}`, 'GET')
            this.societies = await this.updatePagination(response)
        },
        async countryOption (){
            const response = await api('/api/countries/options', 'GET')
            this.countries = response['hydra:member']
        },
        async sortableItems(payload) {
            let response={}
            if (payload.name === 'name') {
                 response = await api(`/api/societies?order%5B${payload.name}%5D=${payload.trier.value}&page=${this.currentPage}`, 'GET')
            }else{
                 response = await api(`/api/societies?order%5Baddress.${payload.name}%5D=${payload.trier.value}&page=${this.currentPage}`, 'GET')
            }
            this.societies = await this.updatePagination(response)
        },
        async paginationSortableItems(payload) {
            let response={}
            if (!payload.sortable.value) {
                response = await api(`/api/societies?page=${payload.nPage}`, 'GET')
                this.societies = await this.updatePagination(response)
            }else {
                if (payload.trierAlpha.value.name === 'name') {
                    response = await api(`/api/societies?order%5B${payload.trierAlpha.value.name}%5D=${payload.trierAlpha.value.trier.value}&page=${payload.nPage}`, 'GET')
                }else{
                    response = await api(`/api/societies?order%5Baddress.${payload.trierAlpha.value.name}%5D=${payload.trierAlpha.value.trier.value}&page=${payload.nPage}`, 'GET')
                }
                this.societies = await this.updatePagination(response)
            }
        },
        async updatePagination(response) {
            const responseData = await response['hydra:member']
            const paginationView = response['hydra:view']
            this.firstPage = paginationView['hydra:first'].match(/page=(\d+)/)[1]
            this.lastPage = paginationView['hydra:last'] ? paginationView['hydra:last'].match(/page=(\d+)/)[1] : paginationView['@id'].match(/page=(\d+)/)[1]
            this.nextPage = paginationView['hydra:next'] ? paginationView['hydra:next'].match(/page=(\d+)/)[1] : paginationView['@id'].match(/page=(\d+)/)[1]
            this.currentPage = paginationView['@id'].match(/page=(\d+)/)[1]
            this.previousPage = paginationView['hydra:previous'] ? paginationView['hydra:previous'].match(/page=(\d+)/)[1] : paginationView['@id'].match(/page=(\d+)/)[1]
            return responseData
        },
    },
    getters: {
        countriesOption: state => state.countries.map((country)=>{
            const opt = {
                text: country.text,
                value: country.code
            }
            return  opt
        }),
        itemsSocieties: state => state.societies.map((item)=> {
            const { address, address2, city, country,email,phoneNumber,zipCode} = item.address 
            const idAdress = item.address['@id']
            const typeAdress = item.address['@type']
            let itemsTab = []
            const newObject = { 
                ...item,
                address: undefined, // Remove the original nested address object
                address: address ?? null,
                address2: address2 ?? null,
                city: city ?? null,
                country: country ?? null,
                email: email ?? null,
                phoneNumber: phoneNumber ?? null,
                zipCode: zipCode ?? null,
                idAdress: idAdress ?? null,
                typeAdress:typeAdress?? null,
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
        previousPage: '',
        societies: []
    })
})
