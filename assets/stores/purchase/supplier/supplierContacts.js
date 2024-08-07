import api from '../../../api'
import {defineStore} from 'pinia'
import generateSupplierContact from './supplierContact'

export const useSupplierContactsStore = defineStore('items', {
    actions: {
        async ajout(data, id) {
            await api('/api/supplier-contacts', 'POST', data)
            this.$reset()
            this.fetchBySociety(id)
        },
        async deleted(payload){
            await api(`/api/supplier-contacts/${payload}`, 'DELETE')
            this.items = this.items.filter(items => Number(items['@id'].match(/\d+/)[0]) !== payload)
        },
        async fetchBySociety(id) {
            this.items = []
            const response = await api(`/api/supplier-contacts?society=${id}`, 'GET')
            for (const society of response['hydra:member']) {
                const item = generateSupplierContact(society, this)
                this.items.push(item)
            }
        },
        async update(data, id) {
            await api(`/api/supplier-contacts/${id}`, 'PATCH', data)
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
