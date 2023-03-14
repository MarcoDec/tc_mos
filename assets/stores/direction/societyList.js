import api from '../../api'
import {defineStore} from 'pinia'

export const useSocietyListStore = defineStore('societyList', {
    actions: {
        fetchItems() {
            this.items = [
                {
                    adresse: 'Bd de l\'oise',
                    ajout: false,
                    complement: 'RUE DES ARTISANS',
                    deletable: true,
                    name: '3M FRANCE',
                    pays: 'France',
                    update: true,
                    ville: 'CERGY PONTOISE'
                },
                {
                    adresse: 'bbbbb',
                    ajout: false,
                    complement: 'RUE',
                    deletable: true,
                    name: '3M FRANCE',
                    pays: 'France',
                    update: true,
                    ville: 'CERGY PONTOISE'
                }
            ]
        },
        async fetch() {
                const response = await api('/api/societies', 'GET')
                const responseData = await response['hydra:member']
                console.log('res:', responseData)
                this.societies = responseData
        }
    },
    getters: {

    },
    state: () => ({
        items: [{
            adresse: '',
            ajout: false,
            complement: '',
            deletable: false,
            name: '',
            pays: '',
            update: false,
            ville: ''
        }],
        societies: []
    })
})
