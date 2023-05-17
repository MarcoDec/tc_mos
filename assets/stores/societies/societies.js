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
            console.log('response', response)
            this.item = response
        },
        async update(data, id) {
            await api(`/api/societies/${id}`, 'PATCH', data)
            this.fetchById(id)
        }

    },
    getters: {

        incotermsValue: state => {
            const newObject = {
                incotermsValue: state.item.incoterms ? state.item.incoterms['@id'] : null
            }
            return newObject
        },
        vatMessageValue: state => {
            const newObject = {
                vatMessageValue: state.item.vatMessage ? state.item.vatMessage['@id'] : null
            }
            return newObject
        }
    },
    state: () => ({
        item: {},
        societies: []

    })
})
