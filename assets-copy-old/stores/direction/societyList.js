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
        }]
    })
})
