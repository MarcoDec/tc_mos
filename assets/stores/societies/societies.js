import api from '../../api'
import {defineStore} from 'pinia'
import generateSocieties from './societie'

export const useSocietyStore = defineStore('societies', {
    actions: {
        async fetch() {
            this.societies = []
            const response = await api('/api/societies', 'GET')
            for (const society of response['hydra:member']) {
                const item = generateSocieties(society, this)
                this.societies.push(item)
            }
        },
        async fetchById(id) {
            const response = await api(`/api/societies/${id}`, 'GET')
            const item =  generateSocieties(response, this)
            this.item = item
        },
        async update(data, id) {
            await api(`/api/societies/${id}`, 'PATCH', data)
            this.fetchById(id)
        }

    },
    getters: {
        // getAddress: state => state.address.address,
        // getAddress2: state => state.address.address2,
        // getCity: state => state.address.city,
        // getCountry: state => state.address.country,
        // getEmail: state => state.address.email,
        // getPhone: state => state.address.phoneNumber,
        // getPostal: state => state.address.zipCode
    },
    state: () => ({
        item: {},
        societies: []

    })
})
