import api from '../../api'
import {defineStore} from 'pinia'
import generateCustomerContact from './customerContact'

export const useCustomerContactsStore = defineStore('items', {
    actions: {
        async ajout(data, id) {
            const response = await api('/api/customer-contacts', 'POST', data)
            console.log('post', response)
            this.$reset()
            this.fetchBySociety(id)
        },
        async deleted(payload){
            await api(`/api/customer-contacts/${payload}`, 'DELETE')
            this.items = this.items.filter(items => Number(items['@id'].match(/\d+/)[0]) !== payload)
        },
        async fetchBySociety(id) {
            this.items = []
            const response = await api(`/api/customer-contacts?society=${id}`, 'GET')
            for (const society of response['hydra:member']) {
                const item = generateCustomerContact(society, this)
                this.items.push(item)
            }
        },
        async update(data, id) {
            await api(`/api/customer-contacts/${id}`, 'PATCH', data)
            this.$reset()
        }

    },
    getters: {
        itemsSocieties: state => state.items.map(item => {
            const {address, address2, city, country, email, phoneNumber, zipCode} = item.address
            const idAdress = item.address['@id']
            const typeAdress = item.address['@type']
            const itemsTab = []
            const newObject = {
                ...item,
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
        items: []
    })
})
